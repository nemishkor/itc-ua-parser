<?php
declare(strict_types=1);

namespace App\Services\Parsers;


use App\Entity\Article;
use App\Services\ParserHelper;
use App\Services\ParserInterface;
use App\ValueObject\ParseQuery;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use DOMDocument;
use DOMNode;
use Exception;
use Psr\Log\LoggerInterface;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use UnexpectedValueException;

class ItcUaParser implements ParserInterface {

    private HttpClientInterface $httpClient;
    private LoggerInterface $logger;
    private ParserHelper $helper;

    public function __construct(HttpClientInterface $httpClient, LoggerInterface $logger, ParserHelper $helper) {
        $this->httpClient = $httpClient;
        $this->logger = $logger;
        $this->helper = $helper;
    }

    public function supports(ParseQuery $parseQuery): bool {
        $urlParts = parse_url($parseQuery->getUrl());
        if ($urlParts === false || !array_key_exists('host', $urlParts)) {
            throw new UnexpectedValueException(sprintf('Url "%s" is incorrect', $parseQuery->getUrl()));
        }

        return $urlParts['host'] === 'itc.ua';
    }

    /**
     * @param ParseQuery $parseQuery
     * @return Collection
     * @throws ClientExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    public function parse(ParseQuery $parseQuery): Collection {

        if ($parseQuery->getSource() === null) {
            $response = $this->httpClient->request('GET', $parseQuery->getUrl());
            $document = $this->helper->createDomDocument($response->getContent());
        } else {
            $document = $this->helper->createDomDocument($parseQuery->getSource());
        }

        $body = $this->helper->getBody($document);
        $bodyClasses = $this->helper->getClasses($body);

        if (in_array("home", $bodyClasses)) {
            return $this->parseHomePage($document, $parseQuery);
        }

        throw new Exception("Unknown page type");
    }

    private function parseHomePage(DOMDocument $document, ParseQuery $parseQuery): Collection {

        $collection = new ArrayCollection();

        $itemsWrapperNode = $document->getElementById("content");
        if ($itemsWrapperNode === null) {
            throw new Exception('Not found node with id "content" on home page');
        }

        foreach ($itemsWrapperNode->childNodes as $childNode) {

            /** @var DOMNode $childNode */
            if ($childNode->nodeName !== 'div') {
                continue;
            }

            $childNodeClasses = $this->helper->getClasses($childNode);

            if (!in_array("post", $childNodeClasses)) {
                continue;
            }

            /** @noinspection PhpParamsInspection */
            $article = $this->parseListPostNode($childNode);
            if ($parseQuery->isLoadFullContentForLists()) {
                $this->updateContent($article);
            }
            $collection->add($article);

            if ($parseQuery->getLimit() !== null && $collection->count() === $parseQuery->getLimit()) {
                break;
            }

        }

        return $collection;
    }

    private function parseListPostNode(\DOMElement $postNode): Article {

        $this->logger->info('Parse list post node');
        $article = new Article();

        $titleNode = $this->helper->findOneByTagNameAndClass($postNode, 'h2', 'entry-title');
        $article->setTitle(preg_replace('/\s+/', ' ', $titleNode->textContent));

        /** @noinspection PhpParamsInspection */
        foreach ($this->helper->findOneChildByTagName(
            $titleNode,
            'a'
        )->attributes as $titleAttrName => $titleAttrNode) {
            if ($titleAttrName === 'href') {
                $this->logger->info('Url found: ' . $titleAttrNode->value);
                $article->setUrl($titleAttrNode->value);
                break;
            }
        }

        $publishedNode = $this->helper->findOneByTagNameAndClass($postNode, 'time', 'published');
        if ($publishedNode === null) {
            $this->logger->warning('Published node not found');
        } else {
            foreach ($publishedNode->attributes as $publishedAttrName => $publishedAttrNode) {
                if ($publishedAttrName === 'datetime') {
                    $article->setCreated(DateTime::createFromFormat(DATE_ATOM, $publishedAttrNode->value));
                    break;
                }
            }
        }

        return $article;
    }

    private function updateContent(Article $article): void {
        if ($article->getUrl() === null) {
            throw new \UnexpectedValueException(sprintf('Url is undefined. Set url of article first'));
        }
        $response = $this->httpClient->request('GET', $article->getUrl());
        $document = $this->helper->createDomDocument($response->getContent());
        $body = $this->helper->getBody($document);
        $articleContentNode = $this->helper->findOneByTagNameAndClass($body, 'div', 'entry-content');
        if ($articleContentNode === null) {
            throw new \Exception('Content DOM node not found in response');
        }
        $article->setContent($document->saveHTML($articleContentNode));
    }

}

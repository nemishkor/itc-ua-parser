<?php
declare(strict_types=1);

namespace App\Services;


use DOMDocument;
use DOMElement;
use DOMNode;
use Psr\Log\LoggerInterface;
use UnexpectedValueException;

class ParserHelper {

    private LoggerInterface $logger;

    public function __construct(LoggerInterface $logger) {
        $this->logger = $logger;
    }

    public function createDomDocument(string $html): DOMDocument {
        $document = new DOMDocument();

        $internalErrors = libxml_use_internal_errors(true);
        $loadResult = $document->loadHTML($html);
        libxml_use_internal_errors($internalErrors);
        foreach (libxml_get_errors() as $error) {
            $this->logger->debug(
                sprintf(
                    'xml errors: %s. level=%s. code=%s. file=%s:%s at column %s',
                    $error->message,
                    $error->level,
                    $error->code,
                    $error->file,
                    $error->line,
                    $error->column
                )
            );
        }

        if ($loadResult === false) {
            throw new UnexpectedValueException(
                sprintf(
                    'Expected valid html. Source is invalid: "%s"',
                    strlen($html) > 15 ? substr($html, 0, 15) . '...' : $html
                )
            );
        }

        return $document;
    }

    public function getBody(DOMDocument $document): DOMElement {
        $bodyNodes = $document->getElementsByTagName('body');

        if ($bodyNodes->count() === 0) {
            throw new UnexpectedValueException('Not found node with tag name "body"');
        }

        if ($bodyNodes->count() > 1) {
            $this->logger->notice(
                sprintf('Found %s nodes with tag name "body" but excepted exact one', $bodyNodes->count())
            );
        }

        return $bodyNodes->item(0);
    }

    public function getClasses(DOMNode $node): array {
        if (!$node->hasAttributes()) {
            return [];
        }
        foreach ($node->attributes as $attribute) {
            /** @var DOMNode $attribute */
            if ($attribute->nodeName === 'class') {
                return explode(' ', $attribute->nodeValue);
            }
        }

        return [];
    }

    public function findByTagNameAndClass(DOMElement $node, string $tagName, string $classname): array {

        /** @var DOMNode[] $result */
        $result = [];

        foreach ($node->getElementsByTagName($tagName) as $foundNodeByName) {
            $classes = $this->getClasses($foundNodeByName);
            if (in_array($classname, $classes)) {
                $result[] = $foundNodeByName;
            }
        }

        return $result;
    }

    public function findOneByTagNameAndClass(
        DOMElement $node,
        string $tagName,
        string $classname
    ): ?DOMNode {

        $result = $this->findByTagNameAndClass($node, $tagName, $classname);

        if (count($result) !== 1) {
            $this->logger->notice(
                sprintf(
                    'Found "%s" nodes with name "%s" and class "%s" but expected "%s"',
                    count($result),
                    $tagName,
                    $classname,
                    1
                )
            );

            return null;
        }

        return $result[0];
    }

    /**
     * @param DOMNode|DOMElement $node
     * @param string $tagName
     * @return DOMNode[]
     */
    public function findChildByTagName(DOMNode $node, string $tagName): array {

        /** @var DOMNode[] $result */
        $result = [];

        foreach ($node->childNodes as $childNode) {
            /** @var DOMNode $childNode */
            if ($childNode->nodeName === $tagName) {
                $result[] = $childNode;
            }
        }

        return $result;
    }

    /**
     * @param DOMElement $node
     * @param string $tagName
     * @return DOMNode|null
     */
    public function findOneChildByTagName(DOMElement $node, string $tagName): ?DOMNode {

        $result = $this->findChildByTagName($node, $tagName);

        if (count($result) !== 1) {
            $this->logger->warning(
                sprintf(
                    'Found "%s" child nodes with name "%s" but expected "%s"',
                    count($result),
                    $tagName,
                    1
                )
            );

            return null;
        }

        return $result[0];
    }

}

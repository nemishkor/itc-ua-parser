<?php

namespace App\Command;

use App\Entity\Article;
use App\Services\Parsers\AbstractParser;
use App\ValueObject\ParseQuery;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class ParseCommand extends Command {

    /** @var mixed */
    protected static $defaultName = 'app:parse';
    /** @var mixed */
    protected static $defaultDescription = 'Parser command';

    private AbstractParser $parser;
    private EntityManagerInterface $entityManager;

    public function __construct(AbstractParser $parser, EntityManagerInterface $entityManager) {
        parent::__construct();
        $this->parser = $parser;
        $this->entityManager = $entityManager;
    }

    protected function configure() {
        $this
            ->setDescription(self::$defaultDescription)
            ->addArgument('url', InputArgument::REQUIRED, 'Url to resource to parse')
            ->addOption('limit', null, InputArgument::OPTIONAL, 'limits article amount to parse')
            ->addOption(
                'loadFullContentForLists',
                null,
                InputArgument::OPTIONAL,
                'set false to make less http requests as possible and do not load content of articles if list provided'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int {
        $io = new SymfonyStyle($input, $output);
        $parseQuery = new ParseQuery(
            $input->getArgument('url'),
            null,
            $input->hasOption('limit') ? intval($input->getOption('limit')) : null,
            filter_var($input->getOption('loadFullContentForLists'), FILTER_VALIDATE_BOOL)
        );

        $collection = $this->parser->parse($parseQuery);
        $articleRepository = $this->entityManager->getRepository(Article::class);
        $updated = 0;
        $created = 0;

        foreach ($collection as $article) {
            /** @var Article $article */
            /** @var Article|null $foundArticle */
            $foundArticle = $articleRepository->findOneBy(['url' => $article->getUrl()]);
            if ($foundArticle === null) {
                $this->entityManager->persist($article);
                $created++;
                continue;
            }
            $foundArticle->setTitle($article->getTitle());
            $foundArticle->setUrl($article->getUrl());
            $foundArticle->setCreated($article->getCreated());
            $foundArticle->setContent($article->getContent());
            $updated++;
        }

        $this->entityManager->flush();

        $io->success(sprintf('Parsed %s entities', $collection->count()));
        $io->success(sprintf('%s articles updated and %s articles created', $updated, $created));

        return Command::SUCCESS;
    }

}

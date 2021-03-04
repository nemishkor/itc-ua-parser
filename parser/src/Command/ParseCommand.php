<?php

namespace App\Command;

use App\Services\Parsers\AbstractParser;
use App\ValueObject\ParseQuery;
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

    public function __construct(AbstractParser $parser) {
        parent::__construct();
        $this->parser = $parser;
    }

    protected function configure() {
        $this
            ->setDescription(self::$defaultDescription)
            ->addArgument('url', InputArgument::OPTIONAL, 'Url to resource to parse');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int {
        $io = new SymfonyStyle($input, $output);
        $parseQuery = new ParseQuery($input->getArgument('url'));

        $collection = $this->parser->parse($parseQuery);

        $io->success(sprintf('Parsed %s entities', $collection->count()));

        return Command::SUCCESS;
    }

}

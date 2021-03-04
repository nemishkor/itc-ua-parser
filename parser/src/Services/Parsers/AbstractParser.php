<?php
declare(strict_types=1);

namespace App\Services\Parsers;


use App\Services\ParserInterface;
use App\ValueObject\ParseQuery;
use Doctrine\Common\Collections\Collection;
use Exception;

class AbstractParser {

    /** @var ParserInterface[] */
    private array $parsers;

    public function __construct(iterable $parsers) {
        $this->parsers = (function (ParserInterface ...$parsers) {
            return $parsers;
        })(
            ...$parsers
        );
    }

    /**
     * @param ParseQuery $parseQuery
     * @return Collection
     * @throws Exception
     */
    public function parse(ParseQuery $parseQuery): Collection {

        foreach ($this->parsers as $parser) {
            if ($parser->supports($parseQuery)) {
                return $parser->parse($parseQuery);
            }
        }

        throw new Exception('No one parser supports the parse query');
    }

}

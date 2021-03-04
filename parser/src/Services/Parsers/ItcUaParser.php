<?php
declare(strict_types=1);

namespace App\Services\Parsers;


use App\Services\ParserInterface;
use App\ValueObject\ParseQuery;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use UnexpectedValueException;

class ItcUaParser implements ParserInterface {

    public function supports(ParseQuery $parseQuery): bool {
        $urlParts = parse_url($parseQuery->getUrl());
        if ($urlParts === false || !array_key_exists('host', $urlParts)) {
            throw new UnexpectedValueException(sprintf('Url "%s" is incorrect', $parseQuery->getUrl()));
        }

        return $urlParts['host'] === 'itc.ua';
    }

    public function parse(ParseQuery $parseQuery): Collection {
        $collection = new ArrayCollection();

        return $collection;
    }

}

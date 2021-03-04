<?php
declare(strict_types=1);

namespace App\Services;


use App\ValueObject\ParseQuery;
use Doctrine\Common\Collections\Collection;

interface ParserInterface {

    public function supports(ParseQuery $parseQuery): bool;

    /**
     * @param ParseQuery $parseQuery
     * @return Collection - collection of parsed entities
     */
    public function parse(ParseQuery $parseQuery): Collection;

}

<?php
declare(strict_types=1);

namespace App\ValueObject;


class ParseQuery {

    private string $url;
    private ?string $source;
    private ?int $limit;
    /**
     * @var bool set false to make less http requests as possible and do not load content of articles if list provided
     */
    private bool $loadFullContentForLists;

    public function __construct(
        string $url,
        ?string $source,
        ?int $limit = null,
        bool $loadFullContentForLists = true
    ) {
        $this->url = $url;
        $this->source = $source;
        $this->limit = $limit;
        $this->loadFullContentForLists = $loadFullContentForLists;
    }

    public function getUrl(): string {
        return $this->url;
    }

    public function getLimit(): ?int {
        return $this->limit;
    }

    public function isLoadFullContentForLists(): bool {
        return $this->loadFullContentForLists;
    }

    public function getSource(): ?string {
        return $this->source;
    }

}

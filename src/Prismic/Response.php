<?hh // strict

/*
 * This file is part of the Prismic PHP SDK
 *
 * Copyright 2013 Zengularity (http://www.zengularity.com).
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Prismic;

class Response
{

    private ImmVector<Document> $results;
    private int $page;
    private int $resultsPerPage;
    private int $resultsSize;
    private int $totalResultsSize;
    private int $totalPages;
    private ?string $nextPage;
    private ?string $prevPage;

    public function __construct(
        ImmVector<Document> $results,
        int $page,
        int $resultsPerPage,
        int $resultsSize,
        int $totalResultsSize,
        int $totalPages,
        ?string $nextPage,
        ?string $prevPage
    ) {
        $this->results = $results;
        $this->page = $page;
        $this->resultsPerPage = $resultsPerPage;
        $this->resultsSize = $resultsSize;
        $this->totalResultsSize = $totalResultsSize;
        $this->totalPages = $totalPages;
        $this->nextPage = $nextPage;
        $this->prevPage = $prevPage;
    }

    public function getResults(): ImmVector<Document> {
        return $this->results;
    }

    public function getPage(): int {
        return $this->page;
    }

    public function getResultsPerPage(): int {
        return $this->resultsPerPage;
    }

    public function getResultsSize(): int {
        return $this->resultsSize;
    }

    public function getTotalResultsSize(): int {
        return $this->totalResultsSize;
    }

    public function getTotalPages(): int {
        return $this->totalPages;
    }

    public function getNextPage(): ?string {
        return $this->nextPage;
    }

    public function getPrevPage(): ?string {
        return $this->prevPage;
    }
}
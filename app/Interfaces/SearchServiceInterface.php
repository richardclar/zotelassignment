<?php

declare(strict_types=1);

namespace App\Interfaces;

use App\DTO\SearchRequestDTO;
use App\DTO\SearchResultDTO;
use Illuminate\Support\Collection;

interface SearchServiceInterface
{
    public function search(SearchRequestDTO $request): Collection;

    public function validateRequest(SearchRequestDTO $request): void;
}

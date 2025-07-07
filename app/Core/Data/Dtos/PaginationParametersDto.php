<?php

namespace App\Core\Data\Dtos;

use Illuminate\Http\Request;
use App\Core\Constants\Pagination;

class PaginationParametersDto
{
    public function __construct(public int $perPage, public int $page) {
        $this->page = max(Pagination::MIN_PAGE, $this->page);
        $this->perPage = min(max($this->perPage, Pagination::MIN_PER_PAGE), Pagination::MAX_PER_PAGE);
    }

    public static function fromRequest(Request $request): self
    {
        return new self(
            perPage: $request->get('per_page', Pagination::DEFAULT_PER_PAGE),
            page: (int) $request->get('page', Pagination::DEFAULT_PAGE),
        );
    }
}
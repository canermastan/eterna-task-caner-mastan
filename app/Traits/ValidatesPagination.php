<?php

namespace App\Traits;

use Illuminate\Http\Request;
use App\Core\Constants\Pagination;

trait ValidatesPagination
{
    public function getValidatedPerPage(Request $request): int
    {
        $perPage = $request->get('per_page', Pagination::DEFAULT_PER_PAGE);
        return min(max((int) $perPage, Pagination::MIN_PER_PAGE), Pagination::MAX_PER_PAGE);
    }
}

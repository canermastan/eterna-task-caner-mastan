<?php

namespace App\Core\Data\Dtos\Post;

use App\Core\Enums\PostStatus;
use Illuminate\Support\Carbon;
use Spatie\LaravelData\Data;

class TogglePublishStatusDto extends Data
{
    public function __construct(
        public int $id,
        public PostStatus $status,
        public Carbon $publishedAt
    ) {}
}
<?php

namespace App\Core\Data\Dtos\Comment;

use App\Core\Enums\CommentStatus;
use Spatie\LaravelData\Data;

class CommentModerationDto extends Data
{
    public function __construct(
        public int $id,
        public string $status
    ) {}

    public static function approve(int $commentId): self
    {
        return new self(
            id: $commentId,
            status: CommentStatus::APPROVED->value
        );
    }

    public static function reject(int $commentId): self
    {
        return new self(
            id: $commentId,
            status: CommentStatus::REJECTED->value
        );
    }
} 
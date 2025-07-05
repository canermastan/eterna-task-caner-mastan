<?php

namespace App\Core\Data\Dtos\Comment;

use App\Core\Data\Requests\Comment\StoreCommentRequest;
use Spatie\LaravelData\Data;

class CreateCommentDto extends Data
{
    public function __construct(
        public int $postId,
        public string $content,
        public ?int $parentId = null,
    ) {}

    public static function fromRequest(StoreCommentRequest $request): self
    {
        return self::from([
            'postId' => $request->post_id,
            'content' => $request->content,
            'parentId' => $request->parent_id,
        ]);
    }
}

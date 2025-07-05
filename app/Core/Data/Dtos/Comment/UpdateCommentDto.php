<?php

namespace App\Core\Data\Dtos\Comment;

use App\Core\Data\Requests\CommentUpdateRequest;
use Spatie\LaravelData\Data;

class UpdateCommentDto extends Data
{
    public function __construct(
        public ?string $content = null,
    ) {}

    public static function fromRequest(CommentUpdateRequest $request): self
    {
        return self::from([
            'content' => $request->content ?? null,
        ]);
    }

    public function toUpdateArray(): array
    {
        $data = [];

        if ($this->content !== null) {
            $data['content'] = $this->content;
        }

        return $data;
    }
} 
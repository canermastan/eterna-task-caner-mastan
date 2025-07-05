<?php

namespace App\Core\Data\Dtos\Post;

use App\Core\Data\Requests\Post\StorePostRequest;
use Illuminate\Http\UploadedFile;
use Spatie\LaravelData\Data;

class CreatePostDto extends Data
{
    public function __construct(
        public array $categoryIds,
        public string $title,
        public string $content,
        public ?UploadedFile $coverImage = null,
    ) {}

    public static function fromRequest(StorePostRequest $request): self
    {
        return self::from([
            'categoryIds' => $request->categoryIds ?? [],
            'title' => $request->title,
            'content' => $request->content,
            'coverImage' => $request->hasFile('coverImage') ? $request->file('coverImage') : null,
        ]);
    }
}

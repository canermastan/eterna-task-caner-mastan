<?php

namespace App\Core\Data\Dtos\Post;

use App\Core\Data\Requests\Post\UpdatePostRequest;
use App\Core\Enums\PostStatus;
use Carbon\Carbon;
use Illuminate\Http\UploadedFile;
use Spatie\LaravelData\Data;

class UpdatePostDto extends Data
{
    public function __construct(
        public ?array $categoryIds = null,
        public ?string $title = null,
        public ?string $content = null,
        public ?UploadedFile $coverImage = null,
        public ?PostStatus $status = null,
        public ?Carbon $published_at = null,
    ) {}

    public static function fromRequest(UpdatePostRequest $request): self
    {
        return self::from([
            'categoryIds' => $request->categoryIds ?? null,
            'title' => $request->title ?? null,
            'content' => $request->content ?? null,
            'coverImage' => $request->hasFile('coverImage') ? $request->file('coverImage') : null,
        ]);
    }

    public function toUpdateArray(): array
    {
        $data = [];

        if ($this->title !== null) {
            $data['title'] = $this->title;
        }

        if ($this->content !== null) {
            $data['content'] = $this->content;
        }

        if ($this->status !== null) {
            $data['status'] = $this->status;
        }

        if ($this->published_at !== null) {
            $data['published_at'] = $this->published_at;
        }

        return $data;
    }
}

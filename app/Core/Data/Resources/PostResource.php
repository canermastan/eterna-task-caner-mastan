<?php

namespace App\Core\Data\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PostResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $this->loadMissing(['categories', 'user']);

        return [
            'id' => $this->id,
            'title' => $this->title,
            'content' => $this->content,
            'slug' => $this->slug,
            'coverImage' => [
                'cover' => $this->getFirstMediaUrl('cover_images', 'cover'),
                'thumb' => $this->getFirstMediaUrl('cover_images', 'thumb'),
            ],
            'categories' => $this->categories->map(function ($category) {
                return [
                    'id' => $category->id,
                    'name' => $category->name,
                ];
            })->toArray(),
            'user' => [
                'id' => $this->user->id,
                'first_name' => $this->user->first_name,
                'last_name' => $this->user->last_name,
            ],
            'status' => $this->status->value,
            'publishedAt' => $this->published_at?->format('Y-m-d H:i:s'),
            'createdAt' => $this->created_at->format('Y-m-d H:i:s'),
        ];
    }
}

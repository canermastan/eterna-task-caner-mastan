<?php

namespace App\Core\Data\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CommentResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'content' => $this->content,
            'status' => $this->status,
            'parent_id' => $this->parent_id,
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
            'created_at_human' => $this->created_at?->diffForHumans(),
            'updated_at_human' => $this->updated_at?->diffForHumans(),

            'user' => $this->whenLoaded('user', function () {
                return [
                    'id' => $this->user->id,
                    'first_name' => $this->user->first_name,
                    'last_name' => $this->user->last_name,
                    'full_name' => $this->user->full_name,
                    'email' => $this->user->email,
                ];
            }),

            'parent' => $this->whenLoaded('parent', function () {
                return [
                    'id' => $this->parent->id,
                    'content' => $this->parent->content,
                    'user' => [
                        'id' => $this->parent->user->id,
                        'full_name' => $this->parent->user->full_name,
                    ],
                ];
            }),

            'children' => $this->whenLoaded('children', function () use ($request) {
                $children = $this->children;
                
                // If user is not admin, only show approved children
                if (!$request->user() || !$request->user()->isAdmin()) {
                    $children = $children->filter(function ($child) {
                        return $child->status === 'approved';
                    });
                }
                
                return CommentResource::collection($children);
            }),

            'children_count' => $this->whenCounted('children'),

            'post' => $this->whenLoaded('post', function () {
                return [
                    'id' => $this->post->id,
                    'title' => $this->post->title,
                    'slug' => $this->post->slug,
                ];
            }),

            'status_label' => $this->getStatusLabel(),
            'status_color' => $this->getStatusColor(),

            'can_edit' => $request->user()?->can('update', $this->resource),
            'can_delete' => $request->user()?->can('delete', $this->resource),
            'can_reply' => $this->status === 'approved' && $request->user() !== null,
        ];
    }

    /**
     * Get status label.
     */
    private function getStatusLabel(): string
    {
        return match($this->status) {
            'pending' => 'Pending',
            'approved' => 'Approved',
            'rejected' => 'Rejected',
            default => 'Unknown',
        };
    }

    /**
     * Get status color for UI.
     */
    private function getStatusColor(): string
    {
        return match($this->status) {
            'pending' => 'warning',
            'approved' => 'success',
            'rejected' => 'danger',
            default => 'secondary',
        };
    }

    public function withResponse(Request $request, $response): void
    {
        $response->header('X-Resource-Type', 'Comment');
    }
}

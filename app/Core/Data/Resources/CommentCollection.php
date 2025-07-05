<?php

namespace App\Core\Data\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class CommentCollection extends ResourceCollection
{
    public function toArray(Request $request): array
    {
        return [
            'comments' => $this->collection,
            'meta' => [
                'total_comments' => $this->collection->count(),
                'approved_comments' => $this->collection->where('status', 'approved')->count(),
                'pending_comments' => $this->collection->where('status', 'pending')->count(),
                'rejected_comments' => $this->collection->where('status', 'rejected')->count(),
            ],
        ];
    }

    public function with(Request $request): array
    {
        return [
            'version' => '1.0',
            'api_url' => url('/api/comments'),
        ];
    }
}

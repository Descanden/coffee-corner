<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CoffeeShopResource extends JsonResource
{
    public $status;
    public $message;
    public $resource;

    public function __construct($status, $message, $resource)
    {
        parent::__construct($resource);
        $this->status = $status;
        $this->message = $message;
    }

    public function toArray(Request $request): array
    {
        if ($this->resource instanceof \Illuminate\Pagination\LengthAwarePaginator) {
            return [
                'success' => $this->status,
                'message' => $this->message,
                'data' => [
                    'posts' => $this->resource->items(),
                    'pagination' => [
                        'current_page' => $this->resource->currentPage(),
                        'total_pages' => $this->resource->lastPage(),
                        'per_page' => $this->resource->perPage(),
                        'total_items' => $this->resource->total(),
                        'next_page_url' => $this->resource->nextPageUrl(),
                        'prev_page_url' => $this->resource->previousPageUrl(),
                        'first_page_url' => $this->resource->url(1),
                        'last_page_url' => $this->resource->url($this->resource->lastPage()),
                    ]
                ]
            ];
        }

        // In case it's a single post response
        return [
            'success' => $this->status,
            'message' => $this->message,
            'data' => $this->resource,
        ];
    }
}
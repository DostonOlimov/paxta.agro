<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Resources\Json\ResourceCollection;

class ApplicationCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {

        return [
            'data' => ApplicationResource::collection($this->collection),
//            'meta' => [
//                'current_page' => $this->currentPage(),
//                'from' => $this->firstItem(),
//                'last_page' => $this->lastPage(),
//                'per_page' => $this->perPage(),
//                'to' => $this->lastItem(),
//                'total' => $this->total(),
//            ],
        ];
    }
}

<?php

namespace App\Http\Resources\V1;

use App\Models\CropData;
use Illuminate\Http\Resources\Json\ResourceCollection;

class CropDataCollection extends ResourceCollection
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
            'data' => CropDataResource::collection($this->collection),
        ];
    }
}

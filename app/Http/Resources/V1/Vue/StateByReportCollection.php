<?php

namespace App\Http\Resources\V1\Vue;

use Illuminate\Http\Resources\Json\ResourceCollection;

class StateByReportCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {

        return  StateByReportResource::collection($this->collection);
    }
}

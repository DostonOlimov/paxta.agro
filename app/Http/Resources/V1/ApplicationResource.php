<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Resources\Json\JsonResource;

class ApplicationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'date' => $this->date,
            'status' => $this->status,
            'createdBy' => $this->created_by,
            'cropData' => new CropDataResource($this->whenLoaded('crops')),
            'companyData' => new CompanyResource($this->whenLoaded('organization')),
            'factoryData' => new FactoryResource($this->whenLoaded('prepared')),
        ];
    }
}

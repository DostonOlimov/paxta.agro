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
            'appNumber' => $this->id,
            'date' => $this->date,
            'type' => $this->type,
            'status' => $this->status,
            'progress' => $this->progress,
            'createdBy' => $this->created_by,
            'createdAt' => $this->created_at,
            'data' => $this->data,
            'acceptedDate' => $this->acceptedDate,
            'cropData' => new CropDataResource($this->whenLoaded('crops')),
            'companyData' => new CompanyResource($this->whenLoaded('organization')),
            'factoryData' => new FactoryResource($this->whenLoaded('prepared')),
            'comment' => new ApplicationCommentResource($this->whenLoaded('comment')),
            'files' => new ApplicationCommentResource($this->whenLoaded('files')),

        ];
    }
}

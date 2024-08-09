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
            'organizationId' => $this->organization_id,
            'type' => $this->type,
            'date' => $this->date,
            'acceptedId' => $this->accepted_id,
            'acceptedDate' => $this->accepted_date,
            'status' => $this->status,
        ];
    }
}

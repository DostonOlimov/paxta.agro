<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Resources\Json\JsonResource;

class CompanyResouerce extends JsonResource
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
            'inn' => $this->inn,
            'name' => $this->name,
            'cityId' => $this->city_id,
            'cityName' => optional($this->city)->name,
            'stateId' => optional($this->city)->state_id,
            'stateName' => optional(optional($this->city)->region)->name,
            'address' => $this->address,
            'ownerName' => $this->owner_name,
            'phoneNumber' => $this->phone_number,

        ];
    }
}

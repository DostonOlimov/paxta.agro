<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Resources\Json\JsonResource;

class CropDataResource extends JsonResource
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
            'cropName' => optional($this->name)->name,
            'kodtnved' => 'kodtnved',
            'partyNumber' => $this->party_number,
            'measureType' => $this->measure_type,
            'amount' => $this->amount,
            'year' => $this->year,
            'toyCount' => $this->toy_count,
            'countryName' => optional($this->country)->name,
        ];
    }
}

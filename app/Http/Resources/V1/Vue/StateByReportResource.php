<?php

namespace App\Http\Resources\V1\Vue;

use Illuminate\Http\Resources\Json\JsonResource;

class StateByReportResource extends JsonResource
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
            'name' => $this->name,
            'apps_count' => $this->application_count,
            'apps_sum_amount' => round($this->application_amount,2),
            'certified_application_count' => $this->certified_application_count,
            'certificates_count' => $this->certificates_count

        ];
    }
}

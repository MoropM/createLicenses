<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Str;

class LicenseResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        // return parent::toArray($request);
        $idUser = explode('|', $this->access_token);
        return [
            'uri' => $this->uri_access,
            'token' => $this->access_token,
            // 'token' => $idUser[0],
            'start_date' => $this->start_date,
            'finish_date' => $this->finish_date,
            'status' => $this->status,
            'license_number' => $this->license,
            'license_token' => $this->license_token,
        ];
        /*
        return [
            'identificador' => $this->id,
            // 'uri_access' => Str::upper($this->uri_access),
            'uri' => $this->uri_access,
            'token' => $this->access_token,
            'finish_date' => $this->finish_date,
            'status' => $this->status,
            'license_token' => $this->license_token,
            'license' => $this->license,
            'created_at' => $this->created_at->format('d/m/Y'),
            'updated_at' => $this->updated_at->format('d/m/Y'),
        ];
        */
    }

    // Propiedades adicionales a retornar
    public function with($request)
    {
        return [
            'status' => true
        ];
    }
}

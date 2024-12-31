<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateLicenseRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            "uri_access" => 'required|string|max:80|min:12|unique:licenses',
            "finishDate" => 'required|string|max:10|min:10',
            // "daysActive" => 'max:5|min:1',
        ];
    }
}

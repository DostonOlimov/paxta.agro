<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateApplicationRequest extends FormRequest
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
            'name' => ['required','integer',Rule::exists('crops_name','id')],
            'country' => 'required|integer',
            'tnved' => 'required|string',
            'party_number' => 'required|string',
            'measure_type' => 'required|string',
            'amount' => 'required|numeric',
            'year' => 'required|integer',
            'toy_count' => 'required|integer',
            'sxeme_number' => 'required|string',
            'organization' => ['required','integer',Rule::exists('organization_companies','id')],
            'prepared' => ['required','integer',Rule::exists('prepared_companies','id')],
            'dob' => 'required|date',
            'data' => 'nullable|string',
        ];
    }
}

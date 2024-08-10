<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ReservationUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'end_date' => [
                'required',
                'date',
//                'date_format:Y-m-d H:i:s'
            ],
            'discount' => [
                'required',
                'numeric',
                'min:0'
            ],
            'amount_paid' => [
                'required',
                'numeric',
                'min:0'
            ]
        ];
    }
}

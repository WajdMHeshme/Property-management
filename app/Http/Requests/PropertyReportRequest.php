<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PropertyReportRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     * Since access control is already handled by middleware (auth + role),
     * we can safely return true here.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Define validation rules for the properties report filters.
     */
    public function rules(): array
    {
        return [
            'status' => 'nullable|in:available,booked,rented,hidden',
            'city'   => 'nullable|string',
            'from'   => 'nullable|date',
            'to'     => 'nullable|date',
        ];
    }

    /**
     * Optional: Customize attribute names for validation messages.
     */
    public function attributes(): array
    {
        return [
            'status' => 'property status',
            'city'   => 'city',
            'from'   => 'from date',
            'to'     => 'to date',
        ];
    }
}

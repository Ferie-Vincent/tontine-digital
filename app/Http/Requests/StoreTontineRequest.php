<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreTontineRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'contribution_amount' => 'required|integer|min:1000',
            'target_amount_per_tour' => 'nullable|integer|min:1000',
            'target_amount_total' => 'nullable|integer|min:1000',
            'frequency' => 'required|in:weekly,biweekly,monthly',
            'max_members' => 'required|integer|min:2|max:100',
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'nullable|date|after:start_date',
            'rules' => 'nullable|string',
        ];
    }

    protected function validationErrorBag(): string
    {
        return 'createTontine';
    }
}

<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTontineRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $tontine = $this->route('tontine');

        return [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'contribution_amount' => 'required|integer|min:1000',
            'target_amount_per_tour' => 'nullable|integer|min:1000',
            'target_amount_total' => 'nullable|integer|min:1000',
            'frequency' => 'required|in:weekly,biweekly,monthly',
            'max_members' => 'required|integer|min:2|max:100',
            'end_date' => 'nullable|date|after:' . $tontine->start_date->format('Y-m-d'),
            'rules' => 'nullable|string',
            'status' => 'sometimes|in:draft,pending,active,paused,completed,cancelled',
        ];
    }

    protected function validationErrorBag(): string
    {
        return 'editTontine';
    }
}

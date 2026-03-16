<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreTourRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $tontine = $this->route('tontine');

        return [
            'beneficiary_id' => [
                'required',
                'exists:users,id',
                function ($attribute, $value, $fail) use ($tontine) {
                    $isMember = $tontine->members()
                        ->where('user_id', $value)
                        ->where('status', 'active')
                        ->exists();
                    if (!$isMember) {
                        $fail('Le bénéficiaire doit être un membre actif de la tontine.');
                    }
                },
            ],
            'due_date' => 'required|date',
            'notes' => 'nullable|string',
        ];
    }
}

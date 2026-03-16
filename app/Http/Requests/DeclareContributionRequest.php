<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DeclareContributionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'payment_method' => 'required|in:orange_money,mtn_momo,wave,cash,bank_transfer,other',
            'transaction_reference' => 'nullable|string|max:100',
            'sender_phone' => 'nullable|string|max:20',
            'transaction_date' => 'nullable|date',
            'notes' => 'nullable|string|max:500',
            'screenshot' => 'nullable|image|max:5120',
        ];
    }
}

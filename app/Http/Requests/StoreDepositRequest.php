<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class StoreDepositRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Auth::check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'account_id' => 'required|integer|exists:accounts,id',
            'deposit_category_id' => 'required|integer|exists:deposit_categories,id',
            'payment_method_id' => 'required|integer|exists:payment_methods,id',
            'amount' => 'required|numeric',
            'deposit_date' => 'required|date',
            'notes' => 'nullable',
        ];
    }
}

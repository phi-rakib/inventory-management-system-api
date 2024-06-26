<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Models\Expense;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;

class StoreExpenseRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Gate::allows('create', Expense::class);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'required',
            'expense_category_id' => 'required|integer|exists:expense_categories,id',
            'account_id' => 'required|integer|exists:accounts,id',
            'payment_method_id' => 'required|integer|exists:payment_methods,id',
            'expense_date' => 'required|date',
            'amount' => 'required|numeric',
            'description' => 'nullable',
        ];
    }
}

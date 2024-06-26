<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Models\Account;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;

class StoreAccountRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Gate::allows('create', Account::class);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'string|required|max:255',
            'account_number' => 'alpha_num|required',
            'balance' => 'numeric|required',
            'description' => 'nullable',
        ];
    }
}

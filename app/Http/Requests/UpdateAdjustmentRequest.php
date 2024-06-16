<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;

class UpdateAdjustmentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Gate::allows('update', $this->adjustment);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'warehouse_id' => 'required|integer|exists:warehouses,id',
            'adjustment_date' => 'required|date',
            'reason' => 'required',
            'adjustment_items' => 'required|array',
            'adjustment_items.*.product_id' => 'required|integer|exists:products,id',
            'adjustment_items.*.quantity' => 'required|integer',
            'adjustment_items.*.type' => 'required|string|in:addition,subtraction',
        ];
    }
}

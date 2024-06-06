<?php

namespace App\Services;

use App\Models\Adjustment;
use App\Models\Product;
use Illuminate\Support\Facades\DB;

class AdjustmentService
{
    public function __construct()
    {
        //
    }

    public function store($request)
    {
        DB::transaction(function () use ($request) {

            $warehouseId = $request->input('warehouse_id');
            $adjustments = $request->input('adjustment_items');

            // update product_warehouse
            foreach ($adjustments as $adjustment) {
                Product::find($adjustment['product_id'])
                    ->warehouses()
                    ->updateExistingPivot($warehouseId, [
                        'quantity' => Product::find($adjustment['product_id'])
                            ->warehouses()
                            ->where('warehouse_id', $warehouseId)
                            ->first()
                            ->pivot
                            ->quantity + ($adjustment['type'] == 'addition' ? $adjustment['quantity'] : (-1) * $adjustment['quantity']),
                    ]);
            }

            $items = [];
            foreach ($adjustments as $adjustment) {
                $items[$adjustment['product_id']] = [
                    'quantity' => $adjustment['quantity'],
                    'type' => $adjustment['type'],
                ];
            }

            // create adjustment
            $adjustment = Adjustment::create([
                'warehouse_id' => $warehouseId,
                'reason' => $request->input('reason'),
                'adjustment_date' => $request->input('adjustment_date'),
            ]);

            // create adjustment_product
            $adjustment->products()->attach($items);
        });

    }

    public function update($request, $adjustment)
    {
        DB::transaction(function () use ($adjustment, $request) {

            $warehouseId = $request->input('warehouse_id');
            $adjustments = $request->input('adjustment_items');
            $reason = $request->input('reason');
            $adjustment_date = $request->input('adjustment_date');

            $adjustmentProductItems = [];
            foreach ($adjustments as $item) {
                $adjustmentProductItems[$item['product_id']] = [
                    'quantity' => $item['quantity'],
                    'type' => $item['type'],
                ];
            }

            //update adjustment
            $adjustment->update([
                'warehouse_id' => $warehouseId,
                'reason' => $reason,
                'adjustment_date' => $adjustment_date,
            ]);

            // update product_warehouse(rollback to previous state)
            foreach ($adjustment->products as $item) {
                Product::find($item->pivot->product_id)
                    ->warehouses()
                    ->updateExistingPivot($warehouseId, [
                        'quantity' => Product::find($item->pivot->product_id)
                            ->warehouses()
                            ->where('warehouse_id', $warehouseId)
                            ->first()
                            ->pivot
                            ->quantity + ($item->pivot->type == 'addition' ? (-1) * $item->pivot->quantity : $item->pivot->quantity),
                    ]);
            }

            // update product_warehouse
            foreach ($adjustments as $item) {
                Product::find($item['product_id'])
                    ->warehouses()
                    ->updateExistingPivot($warehouseId, [
                        'quantity' => Product::find($item['product_id'])
                            ->warehouses()
                            ->where('warehouse_id', $warehouseId)
                            ->first()
                            ->pivot
                            ->quantity + ($item['type'] == 'addition' ? $item['quantity'] : (-1) * $item['quantity']),
                    ]);
            }

            // update adjustment_product
            $adjustment->products()->sync($adjustmentProductItems);
        });
    }

    public function destroy($adjustment)
    {
        $warehouseId = $adjustment->warehouse_id;

        foreach ($adjustment->products as $item) {
            Product::find($item->pivot->product_id)
                ->warehouses()
                ->updateExistingPivot($warehouseId, [
                    'quantity' => Product::find($item->pivot->product_id)
                        ->warehouses()
                        ->where('warehouse_id', $warehouseId)
                        ->first()
                        ->pivot
                        ->quantity + ($item->pivot->type == 'addition' ? (-1) * $item->pivot->quantity : $item->pivot->quantity),
                ]);
        }

        $adjustment->products()->detach();

        $adjustment->delete();
    }
}

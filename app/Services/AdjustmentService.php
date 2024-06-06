<?php

namespace App\Services;

use App\Models\Adjustment;
use App\Models\Product;
use App\Models\Warehouse;
use Illuminate\Support\Facades\DB;

class AdjustmentService
{
    public function __construct()
    {
        //
    }

    public function store($data)
    {
        DB::transaction(function () use ($data) {

            $warehouseId = $data['warehouse_id'];
            $adjustments = $data['adjustment_items'];
            $reason = $data['reason'];
            $adjustmentDate = $data['adjustment_date'];

            // update product_warehouse
            $productIds = array_column($adjustments, 'product_id'); 
            $products = Warehouse::find($warehouseId)->products()->whereIn('product_id', $productIds)->get();

            $updates = [];
            foreach ($adjustments as $adjustment) {
                $product = $products->where('id', $adjustment['product_id'])->first();
                if ($product) {
                    $quantityChange = $adjustment['type'] == 'addition' ? $adjustment['quantity'] : -1 * $adjustment['quantity'];
                    $updates[$product->id] = ['quantity' => $product->pivot->quantity + $quantityChange];
                }
            }
            Warehouse::find($warehouseId)->products()->sync($updates);

            // create adjustment
            $adjustment = Adjustment::create([
                'warehouse_id' => $warehouseId,
                'reason' => $reason,
                'adjustment_date' => $adjustmentDate,
            ]);

            // create adjustment_product
            $adjustment->products()->attach(array_column($adjustments, null, 'product_id'));
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

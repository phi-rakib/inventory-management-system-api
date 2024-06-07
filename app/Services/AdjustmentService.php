<?php

namespace App\Services;

use App\Models\Adjustment;
use App\Models\Product;
use App\Models\Warehouse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

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
            $products = Warehouse::find($warehouseId)
                ->products()
                ->whereIn('product_id', $productIds)
                ->get(['product_id'])
                ->pluck('pivot', 'product_id');

            $updates = [];
            foreach ($adjustments as $adjustment) {
                $product = isset($products[$adjustment['product_id']]) ? $products[$adjustment['product_id']] : null;
                if ($product) {
                    $quantityChange = $adjustment['type'] == 'addition' ? $adjustment['quantity'] : -1 * $adjustment['quantity'];
                    $updates[$product['product_id']] = ['quantity' => $product['quantity'] + $quantityChange];
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
            $adjustments = $request->input('adjustment_items');
            $reason = $request->input('reason');
            $adjustment_date = $request->input('adjustment_date');

            // product_warehouse back to previous state
            $adjustedProducts = $adjustment->products->pluck('pivot', 'id')->toArray();
            $warehouseProducts = Warehouse::find($adjustment->warehouse_id)->products->pluck('pivot', 'id')->toArray();
            Log::debug('previous adjustment');
            Log::debug($adjustedProducts);
            Log::debug('warehouse products');
            Log::debug($warehouseProducts);

            $update = [];
            foreach ($adjustedProducts as $productId => $product) {
                $updatedQuantity = $product['type'] == 'addition' ? (-1) * $product['quantity'] : $product['quantity'];
                $update[$productId] = ['quantity' => $warehouseProducts[$productId]['quantity'] + $updatedQuantity];
            }
            Warehouse::find($adjustment->warehouse_id)->products()->sync($update);

            // update product_warehouse
            $warehouseProducts = Warehouse::find($adjustment->warehouse_id)->products->pluck('pivot', 'id')->toArray();

            Log::debug('previous warehouse products');
            Log::debug($warehouseProducts);

            Log::debug('adjustment products');
            Log::debug($adjustments);

            $update = [];
            foreach ($adjustments as $product) {
                $productId = $product['product_id'];
                $updatedQuantity = $product['type'] == 'addition' ? $product['quantity'] : (-1) * $product['quantity'];
                $update[$productId] = ['quantity' => $warehouseProducts[$productId]['quantity'] + $updatedQuantity];
            }
            Warehouse::find($adjustment->warehouse_id)->products()->sync($update);

            Log::debug('new warehouse products');
            Log::debug(Warehouse::find($adjustment->warehouse_id)->products->pluck('pivot', 'id')->toArray());

            // update adjustment_product
            $adjustment->products()->sync(array_column($adjustments, null, 'product_id'));

            // update adjustment
            $adjustment->update([
                'adjustment_date' => $adjustment_date,
                'reason' => $reason,
            ]);
        });
    }

    public function destroy($adjustment)
    {
        DB::transaction(function () use ($adjustment) {
            $warehouseProducts = $adjustment->warehouse()->with(['products:id'])->first()->toArray();

            $warehouseProducts = array_column($warehouseProducts['products'], 'pivot', 'id');

            $adjustedProducts = $adjustment->products->pluck('pivot', 'id')->toArray();

            $update = [];
            foreach ($adjustedProducts as $productId => $product) {
                $updatedQuantity = $product['type'] == 'subtraction' ? $product['quantity'] : (-1) * $product['quantity'];
                $update[$productId] = ['quantity' => $warehouseProducts[$productId]['quantity'] + $updatedQuantity];
            }

            // update product_warehouse
            Warehouse::find($adjustment->warehouse_id)->products()->sync($update);

            // delete adjustment_product
            $adjustment->products()->detach();

            // delete adjustment
            $adjustment->delete();
        });
    }
}

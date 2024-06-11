<?php

namespace App\Services;

use App\Models\Adjustment;
use App\Models\Warehouse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdjustmentService
{
    public function __construct()
    {
        //
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function store(array $data): void
    {
        DB::transaction(function () use ($data) {

            $warehouseId = (int) $data['warehouse_id'];
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

    public function update(Request $request, Adjustment $adjustment): void
    {
        DB::transaction(function () use ($adjustment, $request) {
            $adjustments = $request->input('adjustment_items');
            $reason = $request->input('reason');
            $adjustment_date = $request->input('adjustment_date');

            // product_warehouse back to previous state
            $adjustedProducts = $adjustment->products->pluck('pivot', 'id')->toArray();
            $warehouseProducts = Warehouse::find($adjustment->warehouse_id)->products->pluck('pivot', 'id')->toArray();

            $update = [];
            foreach ($adjustedProducts as $productId => $product) {
                $updatedQuantity = $product['type'] == 'addition' ? (-1) * $product['quantity'] : $product['quantity'];
                $update[$productId] = ['quantity' => $warehouseProducts[$productId]['quantity'] + $updatedQuantity];
            }
            Warehouse::find($adjustment->warehouse_id)->products()->sync($update);

            // update product_warehouse
            $warehouseProducts = Warehouse::find($adjustment->warehouse_id)->products->pluck('pivot', 'id')->toArray();

            $update = [];
            foreach ($adjustments as $product) {
                $productId = $product['product_id'];
                $updatedQuantity = $product['type'] == 'addition' ? $product['quantity'] : (-1) * $product['quantity'];
                $update[$productId] = ['quantity' => $warehouseProducts[$productId]['quantity'] + $updatedQuantity];
            }
            Warehouse::find($adjustment->warehouse_id)->products()->sync($update);

            // update adjustment_product
            $adjustment->products()->sync(array_column($adjustments, null, 'product_id'));

            // update adjustment
            $adjustment->update([
                'adjustment_date' => $adjustment_date,
                'reason' => $reason,
            ]);
        });
    }

    public function destroy(Adjustment $adjustment): void
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

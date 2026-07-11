<?php

namespace App\Observers;

use App\Models\Product;
use App\Models\LabProduct;

class SynchronizationObserver
{
    public function created($model)
    {
        if ($model instanceof Product) {
            LabProduct::updateOrCreate(
                ['id' => $model->id], // sadece id eşleşmesi
                [
                    'product_name' => $model->name,
                    'product_code' => $model->code,
                ]
            );
        }

        if ($model instanceof LabProduct) {
            Product::updateOrCreate(
                ['id' => $model->id], // sadece id eşleşmesi
                [
                    'name' => $model->product_name,
                    'code' => $model->product_code,
                ]
            );
        }
    }

    public function updated($model)
    {
        if ($model instanceof Product) {
            LabProduct::where('id', $model->id)->update([
                'product_name' => $model->name,
                'product_code' => $model->code,
            ]);
        }

        if ($model instanceof LabProduct) {
            Product::where('id', $model->id)->update([
                'name' => $model->product_name,
                'code' => $model->product_code,
            ]);
        }
    }

    public function deleted($model)
    {
        if ($model instanceof Product) {
            LabProduct::where('id', $model->id)->delete();
        }

        if ($model instanceof LabProduct) {
            Product::where('id', $model->id)->delete();
        }
    }
}

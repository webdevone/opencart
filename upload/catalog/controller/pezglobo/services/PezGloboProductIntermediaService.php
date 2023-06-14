<?php

final class PezGloboProductIntermediaService
{
    public static function save($model, array $data)
    {
        $products = $model->exists(
            [
                'model' => htmlspecialchars_decode($data['model']),
                'manufacturer_id' => $data['manufacturer_id']
            ]
        );

        if (!$products) {
            $model->addProduct($data);
            return;
        }
        
        $model->editProduct($products, $data);
    }
}

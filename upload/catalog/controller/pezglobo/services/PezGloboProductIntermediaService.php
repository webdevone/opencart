<?php

final class PezGloboProductIntermediaService 
{
    public static function save($model, array $data)
    {
        $products = $model->exists([
            'model' => $data['model'],
            'manufacturer_id' => $data['manufacturer_id']
        ]);

        if (!$products) {
            $model->addProduct($data);
            return;
        }
        
        echo "---------------------------------------------------------- <br>";
        echo "encontrado " . $data['model'] . " ? : <br>";
        var_dump($products);
        echo "<br>";
        
        $model->editProduct($products, $data);
    }
}

<?php

class PezGloboProductIntermediaService extends Model
{
    private $productIntermediaModel;
    
    public function __construct($registry) {
        $this->registry = $registry;
        $this->load->model('catalog/product_intermedia');
        $this->productIntermediaModel = $this->model_catalog_product_intermedia;
    }

    public function save(array $data)
    {
        $products = $this->productIntermediaModel->exists(
            [
                'model' => htmlspecialchars_decode($data['model']),
                'manufacturer_id' => $data['manufacturer_id'],
            ]
        );

        $data['status'] = 1;
        if (!$products) {
            $this->productIntermediaModel->addProduct($data);
            return;
        }
        $this->productIntermediaModel->editProduct($products, $data);
    }
    
    public function getProducts($manufacturer_id)
    {
        $prouducts = $this->productIntermediaModel->getProducts(
            [
                'filter_manufacturer_id' => $manufacturer_id,
                'start' => 1,
                'limit' => 10,
                'filter_status' => 1
            ]
        );

        if (empty($prouducts)) {
            return [];
        }

        $arr = [];
        foreach ($prouducts as $product) {
            if (!$product) {
                continue;
            }
            $product_intermedia = $this->getProductWithImages($product);
            $arr[] = $product_intermedia;
        }

        return $arr;
    }

    private function getFormatedProductImages($product) {
        $image = $product['image'];
        $images = json_decode($image, true);
        if ($images) {
            return $images;
        }

        return [];
    }

    private function getProductWithImages($product) {
        $images = $this->getFormatedProductImages($product);
        if (empty($images)) {
            return;
        }
        $images = $images['images'];
        $image = $images[0];
        if (count($images) > 1) {
            array_shift($images);
            $product['images'] = $images;
        }
        $product['image'] = $image;

        return $product;
    }
}


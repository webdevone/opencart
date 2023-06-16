<?php

class PezGloboProductService extends Model
{
    private $productModel;
    
    public function __construct($registry) {
        $this->registry = $registry;
        $this->load->model('catalog/product');
        $this->productModel = $this->model_catalog_product;
    }

    public function save(array $data)
    {
        $products = $this->productModel->exists(
            [
                'model' => htmlspecialchars_decode($data['model']),
                'manufacturer_id' => $data['manufacturer_id']
            ]
        );
        $data['sku'] = '';
        $data['upc'] = '';
        $data['ean'] = '';
        $data['mpn'] = '';
        $data['subtract'] = '';
        $data['sort_order'] = '';
        $data['length_class_id'] = '';
        $data['isbn'] = '';
        $data['shipping'] = '';
        $data['points'] = '';
        $data['minimum'] = '';
        $data['weight_class_id'] = '';
        $data['tax_class_id'] = '';
        $data['jan'] = '';
        $data['date_available'] = '';
        $data['location'] = '';
        $data['stock_status_id'] = (int)$data['quantity'] > 0 ? 7 : 5;
        $data['product_store'] = [0];
        if (!$products) {
            $this->productModel->addProduct($data);
            return;
        }

        $this->productModel->editProduct($products, $data);
    }
    
    public function getProducts($manufacturer_process_status)
    {
        return $this->productModel->getProducts(
            [
                'filter_manufacturer_id' => $manufacturer_process_status['manufacturer_id'],
                'start' => 0,
                'limit' => 10,
                'status' => 0
            ]
        );
    }
}

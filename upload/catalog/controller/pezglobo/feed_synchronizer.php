<?php

require_once __DIR__ . DIRECTORY_SEPARATOR . 'services' . DIRECTORY_SEPARATOR . 'PezGloboProductIntermediaService.php';
require_once __DIR__ . DIRECTORY_SEPARATOR . 'services' . DIRECTORY_SEPARATOR . 'PezGloboProductService.php';
require_once __DIR__ . DIRECTORY_SEPARATOR . 'domain' . DIRECTORY_SEPARATOR .'PezGloboProcessStatus.php';


final class ControllerPezgloboFeedSynchronizer extends Controller {
    public function index() {
        $this->load->model('catalog/product_intermedia');
		$this->load->model('catalog/manufacturer_process_status');
        $manufacturer_process_status = $this->getManufacturerProcessStatus();
        if (empty($manufacturer_process_status)) {
            $this->response->setOutput('feed agregator is running');
            return;
        }
        $proudct_intermedia_service = new PezGloboProductIntermediaService($this->registry);
         $products_intermedia        = $proudct_intermedia_service->getProducts(
            $manufacturer_process_status['manufacturer_id']
        );
        echo 'aqqqqa <pre>';
        var_dump($products_intermedia);
        echo '</pre>';
        
        if (empty($products_intermedia)) {
            $this->response->setOutput('there is no products to sync');
            return;
        }

        $this->load->model('catalog/product');
        foreach ($products_intermedia as $proudct_intermedia) {
            $pezglobo_product_service = new PezGloboProductService($this->registry);
            $pezglobo_product_service->save($proudct_intermedia);
            // $this->model_catalog_product_intermedia->editProduct(
            //     $proudct_intermedia['product_id'],
            //     [
            //         'status' => 2
            //     ]
            // );
        }
        
        $this->response->setOutput('Done!');
    }

    private function getManufacturerProcessStatus() {
		$this->load->model('catalog/manufacturer_process_status');
		return $this->model_catalog_manufacturer_process_status->getManufacturerProcessStatusByStatus(PezGloboProcessStatus::FINISHED);
	}
}

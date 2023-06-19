<?php

require_once __DIR__ . DIRECTORY_SEPARATOR . 'services' . DIRECTORY_SEPARATOR . 'PezGloboXMLFeedService.php';
require_once __DIR__ . DIRECTORY_SEPARATOR . 'services' . DIRECTORY_SEPARATOR . 'PezGloboProductIntermediaService.php';

class ControllerPezgloboFeedAggregator extends Controller {
	public function index() {
		$manufacturer_process_status = $this->getManufacturerProcessStatus();
		if (empty($manufacturer_process_status)) {
			$this->response->setOutput('there is no manufacturer feed in queue');
			return;
		}
		$this->load->model('catalog/manufacturer');
		$feedUrl = $this->model_catalog_manufacturer->getManufacturerOverloadFeedUrl(
			$manufacturer_process_status['manufacturer_id']
		);
		
		$feedIterator = new PezGloboXMLFeedService($feedUrl, $manufacturer_process_status['manufacturer_id'], $this->registry);
		$products = $feedIterator->batch();
		// echo "<pre>";
		// var_dump($manufacturer_process_status);
		// echo "</pre>";
		
		if (empty($products)) {
			$this->model_catalog_manufacturer_process_status->editManufacturerProcessStatusByManufacturerId(
				$manufacturer_process_status['manufacturer_id'],
				PezGloboProcessStatus::AGGREGATOR_FINISHED
			);
			$this->response->setOutput('there is no products');
			return;
		}
		
		$this->load->model('catalog/product_intermedia');
		foreach ($products as $product) {
			$product_intermedia_service = new PezGloboProductIntermediaService($this->registry);
			$product_intermedia_service->save($product);
		}

		$this->response->setOutput('Done!');
	}

	private function getManufacturerProcessStatus() {
		$this->load->model('catalog/manufacturer_process_status');
		$manufacturer_process_status = $this->model_catalog_manufacturer_process_status->getManufacturerProcessStatusByStatus(PezGloboProcessStatus::AGGREGATOR_IN_PROGRESS);

		if (empty($manufacturer_process_status)) {
			$manufacturer_process_status = $this->model_catalog_manufacturer_process_status->getManufacturerProcessStatusByStatus(PezGloboProcessStatus::AGGREGATOR_IN_QUEUE);
		}
		if (empty($manufacturer_process_status)) {
			$manufacturer_process_status = $this->model_catalog_manufacturer_process_status->getOldestManufacturerProcessStatusByStatus(PezGloboProcessStatus::SYNCHRONIZER_FINISHED);
		}

		return $manufacturer_process_status;
	}
}

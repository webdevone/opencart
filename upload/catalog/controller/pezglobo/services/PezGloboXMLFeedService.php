<?php

require_once __DIR__ . DIRECTORY_SEPARATOR . 'PezGloboLog.php';
require_once __DIR__ . DIRECTORY_SEPARATOR . 'PezGloboGCategoryService.php';
require_once __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'domain' . DIRECTORY_SEPARATOR .'PezGloboProcessStatus.php';

final class PezGloboXMLFeedService extends Model {
    private $batchSize = 10;
    private $language_id = 2;
    private $store_id = 0;
    private $feedUrl;
    private $logFile;
    private $manufacturer_id;

    private $status = 1;

    public function __construct($feedUrl, $manufacturer_id, $registry)
    {
        $this->registry = $registry;
        $this->feedUrl = $feedUrl;
        $logFile = parse_url($feedUrl, PHP_URL_HOST);
        if (false !== $logFile) {
            $this->logFile = "pezglobo_{$logFile}_feed_next_batch.log";
        }
        $this->manufacturer_id = $manufacturer_id;
    }

    public function batch()
    {
        $xml = file_get_contents($this->feedUrl);
        $feed = simplexml_load_string($xml);
        $namespace = $feed->getDocNamespaces(true)['g'];
        $elementos = $feed->xpath('//channel/item');
        echo count($elementos) . " elementos <br>";
        $batch_actual = PezGloboLog::read($this->logFile);
        if (empty($batch_actual)) {
            $batch_actual = 0;
            $offset = 0;
        } else {
            $offset =  $batch_actual * 10;
        }
        $lote = array_slice($elementos, $offset, $this->batchSize);
        PezGloboLog::write($this->logFile, $batch_actual + 1);
        $this->load->model('catalog/manufacturer_process_status');

        if (empty($lote)) {
            PezGloboLog::delete($this->logFile);
            $this->model_catalog_manufacturer_process_status->editManufacturerProcessStatusByManufacturerId($this->manufacturer_id, PezGloboProcessStatus::FINISHED);
            $log = new Log('pezglobo.log');
            $log->write('Proceso finalizado de feed xml manu id : '. $this->manufacturer_id);
            return [];
        }
        
        $this->model_catalog_manufacturer_process_status->editManufacturerProcessStatusByManufacturerId($this->manufacturer_id, PezGloboProcessStatus::IN_PROGRESS);

        return $this->processBatch($lote, $namespace);
    }

    private function processBatch($lote, $namespace)
    {
        $data = [];
        foreach ($lote as $item) {
            $title = htmlspecialchars_decode((string)$item->title);
            $descripcion = (string)$item->description;
            $price = (float)$item->children($namespace)->price;
            $imageLink = (string)$item->children($namespace)->image_link;
            $link = (string)$item->link;
            $quantity = (string)$item->children($namespace)->quantity;
            $weight = (string)$item->children($namespace)->weight;
            $availability = (string)$item->children($namespace)->availability;
            $images = [
                'images' => [
                    $imageLink
                ]
            ];
            $data[] = [
                'model' => $title,
                'price' => $price,
                'image' => json_encode($images),
                'link' => $link,
                'quantity' => $quantity,
                'weight' => $weight,
                'manufacturer_id' => $this->manufacturer_id,
                'product_description' => [
                    $this->language_id => [
                        'description' => $descripcion,
                        'name' => $title,
                        'tag' => PezGloboGCategoryService::get((string) $item->children($namespace)->google_product_category)
                    ] 
                ],
                'store_id' => $this->store_id,
                'availability' => $availability,
                'status' => $this->status
            ];
        }
        
        return $data;
    }

    private function processChildren($arr) {
        $values = [];
        foreach ($arr as $value) {
            $values[] = (string) $value;
        }

        return $values;
    }
}

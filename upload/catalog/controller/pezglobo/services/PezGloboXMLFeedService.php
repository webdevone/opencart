<?php

require_once __DIR__ . DIRECTORY_SEPARATOR . 'PezGloboLog.php';
require_once __DIR__ . DIRECTORY_SEPARATOR . 'PezGloboGCategoryService.php';

final class PezGloboXMLFeedService {
    private $batchSize = 10;
    private $language_id = 2;
    private $store_id = 0;
    private $feedUrl;
    private $logFile;
    private $manufacturer_id;


    public function __construct($feedUrl, $manufacturer_id)
    {
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
        $batch_actual = PezGloboLog::read($this->logFile);
        if (empty($batch_actual)) {
            $batch_actual = 0;
            $offset = 0;
        } else {
            $offset =  $batch_actual * 10;
        }
        $lote = array_slice($elementos, $offset, $this->batchSize);
        PezGloboLog::write($this->logFile, $batch_actual + 1);
        if (empty($lote)) {
            PezGloboLog::delete($this->logFile);
            return [];
        }

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

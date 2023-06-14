<?php
class ModelCatalogManufacturerProcessStatus extends Model {
    public function editManufacturerProcessStatusByManufacturerId($manufacturer_id, $status) {
        $this->db->query("UPDATE " . DB_PREFIX . "manufacturer_process_status SET status = '" . (int)$status . "' WHERE manufacturer_id = '" . (int)$manufacturer_id . "'");
    }

    public function getManufacturerProcessStatusByStatus($status) {
        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "manufacturer_process_status WHERE status = '" . (int)$status . "' LIMIT 1");
    
        return $query->row;
    }
}

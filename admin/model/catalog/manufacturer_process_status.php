<?php
class ModelCatalogManufacturerProcessStatus extends Model {
    public function addManufacturerProcessStatus($manufacturer_id) {
        $this->db->query("INSERT INTO " . DB_PREFIX . "manufacturer_process_status SET manufacturer_id = '" . (int)$manufacturer_id . "'");

        return $this->db->getLastId();
    }

    public function editManufacturerProcessStatus($id, $data) {
        $this->db->query("UPDATE " . DB_PREFIX . "manufacturer_process_status SET manufacturer_id = '" . (int)$data['manufacturer_id'] . "', status = '" . (int)$data['status'] . "' WHERE id_manufacturer_process_status = '" . (int)$id . "'");
    }

    public function getManufacturerProcessStatus($id) {
        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "manufacturer_process_status WHERE id_manufacturer_process_status = '" . (int)$id . "'");

        return $query->row;
    }

    public function getManufacturerProcessStatusByManufacturerId($manufacturer_id) {
        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "manufacturer_process_status WHERE manufacturer_id = '" . (int)$manufacturer_id . "'");

        return $query->row;
    }
}

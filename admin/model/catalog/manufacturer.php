<?php
class ModelCatalogManufacturer extends Model {
	public function addManufacturer($data) {
		$this->db->query("INSERT INTO " . DB_PREFIX . "manufacturer SET name = '" . $this->db->escape($data['name']) . "', sort_order = '" . (int)$data['sort_order'] . "'");

		$manufacturer_id = $this->db->getLastId();

		if (isset($data['image'])) {
			$this->db->query("UPDATE " . DB_PREFIX . "manufacturer SET image = '" . $this->db->escape($data['image']) . "' WHERE manufacturer_id = '" . (int)$manufacturer_id . "'");
		}

		if (isset($data['manufacturer_store'])) {
			foreach ($data['manufacturer_store'] as $store_id) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "manufacturer_to_store SET manufacturer_id = '" . (int)$manufacturer_id . "', store_id = '" . (int)$store_id . "'");
			}
		}
				
		// SEO URL
		if (isset($data['manufacturer_seo_url'])) {
			foreach ($data['manufacturer_seo_url'] as $store_id => $language) {
				foreach ($language as $language_id => $keyword) {
					if (!empty($keyword)) {
						$this->db->query("INSERT INTO " . DB_PREFIX . "seo_url SET store_id = '" . (int)$store_id . "', language_id = '" . (int)$language_id . "', query = 'manufacturer_id=" . (int)$manufacturer_id . "', keyword = '" . $this->db->escape($keyword) . "'");
					}
				}
			}
		}
		
		// Pez globo
		if (isset($data['facebook_url'])) {
			$this->db->query("INSERT INTO " . DB_PREFIX . "manufacturer_overload SET manufacturer_id = '" . (int)$manufacturer_id . "', facebook_url = '" . $this->db->escape($data['facebook_url']) . "'");
		}

		if (isset($data['instagram_url'])) {
			$this->db->query("UPDATE " . DB_PREFIX . "manufacturer_overload SET instagram_url = '" . $this->db->escape($data['instagram_url'])  . "' WHERE manufacturer_id = '" . (int)$manufacturer_id . "'");
		}

		if (isset($data['tiktok_url'])) {
			$this->db->query("UPDATE " . DB_PREFIX . "manufacturer_overload SET tiktok_url = '" . $this->db->escape($data['tiktok_url'])  . "' WHERE manufacturer_id = '" . (int)$manufacturer_id . "'");
		}

		if (isset($data['whatsapp_url'])) {
			$this->db->query("UPDATE " . DB_PREFIX . "manufacturer_overload SET whatsapp_url = '" . $this->db->escape($data['whatsapp_url'])  . "' WHERE manufacturer_id = '" . (int)$manufacturer_id . "'");
		}

		if (isset($data['store_url'])) {
			$this->db->query("UPDATE " . DB_PREFIX . "manufacturer_overload SET store_url = '" . $this->db->escape($data['store_url'])  . "' WHERE manufacturer_id = '" . (int)$manufacturer_id . "'");
		}

		if (isset($data['phone_number'])) {
			$this->db->query("UPDATE " . DB_PREFIX . "manufacturer_overload SET phone_number = '" . $this->db->escape($data['phone_number'])  . "' WHERE manufacturer_id = '" . (int)$manufacturer_id . "'");
		}

		$this->load->model('catalog/manufacturer_process_status');
		$this->model_catalog_manufacturer_process_status->addManufacturerProcessStatus($manufacturer_id);
		// end Pez globo
		$this->cache->delete('manufacturer');

		return $manufacturer_id;
	}

	public function editManufacturer($manufacturer_id, $data) {
		$this->db->query("UPDATE " . DB_PREFIX . "manufacturer SET name = '" . $this->db->escape($data['name']) . "', sort_order = '" . (int)$data['sort_order'] . "' WHERE manufacturer_id = '" . (int)$manufacturer_id . "'");

		if (isset($data['image'])) {
			$this->db->query("UPDATE " . DB_PREFIX . "manufacturer SET image = '" . $this->db->escape($data['image']) . "' WHERE manufacturer_id = '" . (int)$manufacturer_id . "'");
		}

		$this->db->query("DELETE FROM " . DB_PREFIX . "manufacturer_to_store WHERE manufacturer_id = '" . (int)$manufacturer_id . "'");

		if (isset($data['manufacturer_store'])) {
			foreach ($data['manufacturer_store'] as $store_id) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "manufacturer_to_store SET manufacturer_id = '" . (int)$manufacturer_id . "', store_id = '" . (int)$store_id . "'");
			}
		}

		$this->db->query("DELETE FROM `" . DB_PREFIX . "seo_url` WHERE query = 'manufacturer_id=" . (int)$manufacturer_id . "'");

		if (isset($data['manufacturer_seo_url'])) {
			foreach ($data['manufacturer_seo_url'] as $store_id => $language) {
				foreach ($language as $language_id => $keyword) {
					if (!empty($keyword)) {
						$this->db->query("INSERT INTO `" . DB_PREFIX . "seo_url` SET store_id = '" . (int)$store_id . "', language_id = '" . (int)$language_id . "', query = 'manufacturer_id=" . (int)$manufacturer_id . "', keyword = '" . $this->db->escape($keyword) . "'");
					}
				}
			}
		}

		$this->cache->delete('manufacturer');

		// Pez globo
		if (isset($data['facebook_url'])) {
			$this->db->query("UPDATE " . DB_PREFIX . "manufacturer_overload SET facebook_url = '" . $this->db->escape($data['facebook_url'])  . "' WHERE manufacturer_id = '" . (int)$manufacturer_id . "'");
		}

		if (isset($data['instagram_url'])) {
			$this->db->query("UPDATE " . DB_PREFIX . "manufacturer_overload SET instagram_url = '" . $this->db->escape($data['instagram_url'])  . "' WHERE manufacturer_id = '" . (int)$manufacturer_id . "'");
		}

		if (isset($data['tiktok_url'])) {
			$this->db->query("UPDATE " . DB_PREFIX . "manufacturer_overload SET tiktok_url = '" . $this->db->escape($data['tiktok_url'])  . "' WHERE manufacturer_id = '" . (int)$manufacturer_id . "'");
		}

		if (isset($data['whatsapp_url'])) {
			$this->db->query("UPDATE " . DB_PREFIX . "manufacturer_overload SET whatsapp_url = '" . $this->db->escape($data['whatsapp_url'])  . "' WHERE manufacturer_id = '" . (int)$manufacturer_id . "'");
		}

		if (isset($data['store_url'])) {
			$this->db->query("UPDATE " . DB_PREFIX . "manufacturer_overload SET store_url = '" . $this->db->escape($data['store_url'])  . "' WHERE manufacturer_id = '" . (int)$manufacturer_id . "'");
		}

		if (isset($data['phone_number'])) {
			$this->db->query("UPDATE " . DB_PREFIX . "manufacturer_overload SET phone_number = '" . $this->db->escape($data['phone_number'])  . "' WHERE manufacturer_id = '" . (int)$manufacturer_id . "'");
		}
		// end Pez globo
	}

	public function deleteManufacturer($manufacturer_id) {
		$this->db->query("DELETE FROM `" . DB_PREFIX . "manufacturer` WHERE manufacturer_id = '" . (int)$manufacturer_id . "'");
		$this->db->query("DELETE FROM `" . DB_PREFIX . "manufacturer_to_store` WHERE manufacturer_id = '" . (int)$manufacturer_id . "'");
		$this->db->query("DELETE FROM `" . DB_PREFIX . "seo_url` WHERE query = 'manufacturer_id=" . (int)$manufacturer_id . "'");

		$this->cache->delete('manufacturer');
	}

	public function getManufacturer($manufacturer_id) {
		$query = $this->db->query("SELECT DISTINCT * FROM " . DB_PREFIX . "manufacturer WHERE manufacturer_id = '" . (int)$manufacturer_id . "'");

		return $query->row;
	}

	public function getManufacturers($data = array()) {
		$sql = "SELECT * FROM " . DB_PREFIX . "manufacturer";

		if (!empty($data['filter_name'])) {
			$sql .= " WHERE name LIKE '" . $this->db->escape($data['filter_name']) . "%'";
		}

		$sort_data = array(
			'name',
			'sort_order'
		);

		if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
			$sql .= " ORDER BY " . $data['sort'];
		} else {
			$sql .= " ORDER BY name";
		}

		if (isset($data['order']) && ($data['order'] == 'DESC')) {
			$sql .= " DESC";
		} else {
			$sql .= " ASC";
		}

		if (isset($data['start']) || isset($data['limit'])) {
			if ($data['start'] < 0) {
				$data['start'] = 0;
			}

			if ($data['limit'] < 1) {
				$data['limit'] = 20;
			}

			$sql .= " LIMIT " . (int)$data['start'] . "," . (int)$data['limit'];
		}

		$query = $this->db->query($sql);

		return $query->rows;
	}

	public function getManufacturerStores($manufacturer_id) {
		$manufacturer_store_data = array();

		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "manufacturer_to_store WHERE manufacturer_id = '" . (int)$manufacturer_id . "'");

		foreach ($query->rows as $result) {
			$manufacturer_store_data[] = $result['store_id'];
		}

		return $manufacturer_store_data;
	}
	
	public function getManufacturerSeoUrls($manufacturer_id) {
		$manufacturer_seo_url_data = array();
		
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "seo_url WHERE query = 'manufacturer_id=" . (int)$manufacturer_id . "'");

		foreach ($query->rows as $result) {
			$manufacturer_seo_url_data[$result['store_id']][$result['language_id']] = $result['keyword'];
		}

		return $manufacturer_seo_url_data;
	}
	
	public function getTotalManufacturers() {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "manufacturer");

		return $query->row['total'];
	}

	// Pez globo
	public function getManufacturerOverloadFacebookUrl($manufacturer_id) {
		return $this->getManufacturerOverload($manufacturer_id)['facebook_url'];
	}

	public function getManufacturerOverloadInstagramUrl($manufacturer_id) {
		return $this->getManufacturerOverload($manufacturer_id)['instagram_url'];
	}

	public function getManufacturerOverloadTiktokUrl($manufacturer_id) {
		return $this->getManufacturerOverload($manufacturer_id)['tiktok_url'];
	}
	
	public function getManufacturerOverloadWhatsappUrl($manufacturer_id) {
		return $this->getManufacturerOverload($manufacturer_id)['whatsapp_url'];
	}

	public function getManufacturerOverloadStoreUrl($manufacturer_id) {
		return $this->getManufacturerOverload($manufacturer_id)['store_url'];
	}

	public function getManufacturerOverloadPhoneNumber($manufacturer_id) {
		return $this->getManufacturerOverload($manufacturer_id)['phone_number'];
	}
	
	private function getManufacturerOverload ($manufacturer_id) {
		if (empty($manufacturer_id)) {
			return;
		}
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "manufacturer_overload WHERE manufacturer_id = '" . $manufacturer_id ."'");
		if (empty($query->rows[0])) {
			return [
				'facebook_url' => '',
				'instagram_url' => '',
				'tiktok_url' => '',
				'whatsapp_url' => '',
				'store_url' => '',
				'phone_number' => ''
			];
		}

		return $query->rows[0];
	}

	// end Pez globo
}

<?php
class ModelLocalisationOrderStatus extends Model {
	public function getOrderStatus($order_status_id) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "order_status WHERE order_status_id = '" . (int)$order_status_id . "' AND language_id = '" . (int)$this->config->get('config_language_id') . "'");

		return $query->row;
	}
	
	public function getOrderStatusByName($order_status) {
		//print_r(($order_status);
		$query = $this->db->query("SELECT order_status_id FROM " . DB_PREFIX . "order_status WHERE name = '" . $this->db->escape($order_status ). "' AND language_id = '" . (int)$this->config->get('config_language_id') . "'");
		if(isset($query->row['order_status_id'])){
			return $query->row['order_status_id'];
		}
		return 19;
	}

	public function getOrderStatuses() {
		$order_status_data = $this->cache->get('order_status.' . (int)$this->config->get('config_language_id'));

		if (!$order_status_data) {
			$query = $this->db->query("SELECT order_status_id, name FROM " . DB_PREFIX . "order_status WHERE language_id = '" . (int)$this->config->get('config_language_id') . "' ORDER BY name");

			$order_status_data = $query->rows;

			$this->cache->set('order_status.' . (int)$this->config->get('config_language_id'), $order_status_data);
		}
		
		return $order_status_data;
	}
}

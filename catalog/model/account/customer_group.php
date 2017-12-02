<?php
class ModelAccountCustomerGroup extends Model {
	public function getCustomerGroup($customer_group_id) {
		$query = $this->db->query("SELECT DISTINCT * FROM " . DB_PREFIX . "customer_group cg LEFT JOIN " . DB_PREFIX . "customer_group_description cgd ON (cg.customer_group_id = cgd.customer_group_id) WHERE cg.customer_group_id = '" . (int)$customer_group_id . "' AND cgd.language_id = '" . (int)$this->config->get('config_language_id') . "'");

		return $query->row;
	}
	
	public function getCustomerGroupByName($customer_name) {
		$query = $this->db->query("SELECT DISTINCT * FROM " . DB_PREFIX . "customer_group cg LEFT JOIN " . DB_PREFIX . "customer_group_description cgd ON (cg.customer_group_id = cgd.customer_group_id) WHERE cgd.name = '" . $this->db->escape($customer_name) . "' AND cgd.language_id = '" . (int)$this->config->get('config_language_id') . "'");

		return $query->row;
	}

	public function getCustomerGroups() {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "customer_group cg LEFT JOIN " . DB_PREFIX . "customer_group_description cgd ON (cg.customer_group_id = cgd.customer_group_id) WHERE cgd.language_id = '" . (int)$this->config->get('config_language_id') . "' ORDER BY cg.sort_order ASC, cgd.name ASC");

		return $query->rows;
	}
	
	
	public function getCustomerGroupCharge() {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "customer_group cg WHERE cg.config_charging<>'' and cg.config_charging<>'null'  ORDER BY cg.sort_order ASC");

		return $query->row;
	}
	
	public function updateCustomerGroupCharge($customer_group_id, $data){
		$this->db->query("UPDATE " . DB_PREFIX . "customer_group SET config_charging = '" . $this->db->escape(json_encode($data, true)) . "' WHERE customer_group_id = '" . (int)$customer_group_id . "'");

	}
	
	public function updateCustomer($customer_group_id, $customer_id){
		$this->db->query("UPDATE " . DB_PREFIX . "customer SET customer_group_id = '" . (int)$customer_group_id. "' WHERE customer_id = '" . (int)$customer_id . "'");

	}
}
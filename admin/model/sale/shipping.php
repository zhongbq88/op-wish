<?php
class ModelSaleShipping extends Model {
	
	public function getShippings(){
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "shipping ORDER BY date_added DESC");

		return $query->rows;
	}
	
	public function getShipping($shipping_id){
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "shipping WHERE shipping_id='".(int)$shipping_id."'");

		return $query->row;
	}
	
	public function saveShippings($data){
		if(isset($data['shipping_id'])){
			$this->db->query("DELETE FROM " . DB_PREFIX . "shipping WHERE shipping_id='".(int)$data['shipping_id']."'");
		}
		//print_r($data['shipping']);
		$query = $this->db->query("INSERT INTO " . DB_PREFIX . "shipping SET shipping_name='".$this->db->escape($data['shipping']['name'])."',shipping_country='".$this->db->escape(json_encode($data['shipping']['countryCode']))."' ,shipping_option='".$this->db->escape(json_encode($data['shipping']))."',  date_added = NOW()");
	}
	
	public function getShippingCost($shipping_zone){
		if(empty($shipping_zone)){
			return;
		}
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "shipping WHERE shipping_country LIKE '%".$this->db->escape($shipping_zone)."%'");
		if(isset($query->row['shipping_option'])){
			return json_decode($query->row['shipping_option'],true);
		}
		return;
	}
	
	public function deleteShipping($shipping_id){
		if(isset($shipping_id)){
			$this->db->query("DELETE FROM " . DB_PREFIX . "shipping WHERE shipping_id='".(int)$shipping_id."'");
		}
	}
}

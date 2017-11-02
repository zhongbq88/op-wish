<?php
class ModelSaleShipping extends Model {
	
	public function getShippings(){
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "shipping ORDER BY date_added DESC");

		return $query->rows;
	}
}

<?php
class ModelSettingExtension extends Model {
	function getExtensions($type) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "extension WHERE `type` = '" . $this->db->escape($type) . "'");

		return $query->rows;
	}
	
	public function cechkCode($code) {
		$query = $this->db->query("SELECT code FROM " . DB_PREFIX . "invitation_code WHERE `code` = '" . $this->db->escape($code) . "'");
		return isset($query->row['code']);
	}
}
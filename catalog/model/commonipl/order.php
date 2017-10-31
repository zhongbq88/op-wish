<?php
class ModelCommoniplOrder extends Model {
	public function getOrder($order_id) {
		$order_query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "order` WHERE order_id = '" . (int)$order_id . "' AND customer_id = '" . (int)$this->customer->getId() . "' AND order_status_id > '0'");

		if ($order_query->num_rows) {
			$country_query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "country` WHERE country_id = '" . (int)$order_query->row['payment_country_id'] . "'");

			if ($country_query->num_rows) {
				$payment_iso_code_2 = $country_query->row['iso_code_2'];
				$payment_iso_code_3 = $country_query->row['iso_code_3'];
			} else {
				$payment_iso_code_2 = '';
				$payment_iso_code_3 = '';
			}

			$zone_query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "zone` WHERE zone_id = '" . (int)$order_query->row['payment_zone_id'] . "'");

			if ($zone_query->num_rows) {
				$payment_zone_code = $zone_query->row['code'];
			} else {
				$payment_zone_code = '';
			}

			$country_query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "country` WHERE country_id = '" . (int)$order_query->row['shipping_country_id'] . "'");

			if ($country_query->num_rows) {
				$shipping_iso_code_2 = $country_query->row['iso_code_2'];
				$shipping_iso_code_3 = $country_query->row['iso_code_3'];
			} else {
				$shipping_iso_code_2 = '';
				$shipping_iso_code_3 = '';
			}

			$zone_query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "zone` WHERE zone_id = '" . (int)$order_query->row['shipping_zone_id'] . "'");

			if ($zone_query->num_rows) {
				$shipping_zone_code = $zone_query->row['code'];
			} else {
				$shipping_zone_code = '';
			}

			return array(
				'order_id'                => $order_query->row['order_id'],
				'invoice_no'              => $order_query->row['invoice_no'],
				'invoice_prefix'          => $order_query->row['invoice_prefix'],
				'store_id'                => $order_query->row['store_id'],
				'store_name'              => $order_query->row['store_name'],
				'store_url'               => $order_query->row['store_url'],
				'customer_id'             => $order_query->row['customer_id'],
				'firstname'               => $order_query->row['firstname'],
				'lastname'                => $order_query->row['lastname'],
				'telephone'               => $order_query->row['telephone'],
				'email'                   => $order_query->row['email'],
				'payment_firstname'       => $order_query->row['payment_firstname'],
				'payment_lastname'        => $order_query->row['payment_lastname'],
				'payment_company'         => $order_query->row['payment_company'],
				'payment_address_1'       => $order_query->row['payment_address_1'],
				'payment_address_2'       => $order_query->row['payment_address_2'],
				'payment_postcode'        => $order_query->row['payment_postcode'],
				'payment_city'            => $order_query->row['payment_city'],
				'payment_zone_id'         => $order_query->row['payment_zone_id'],
				'payment_zone'            => $order_query->row['payment_zone'],
				'payment_zone_code'       => $payment_zone_code,
				'payment_country_id'      => $order_query->row['payment_country_id'],
				'payment_country'         => $order_query->row['payment_country'],
				'payment_iso_code_2'      => $payment_iso_code_2,
				'payment_iso_code_3'      => $payment_iso_code_3,
				'payment_address_format'  => $order_query->row['payment_address_format'],
				'payment_method'          => $order_query->row['payment_method'],
				'shipping_firstname'      => $order_query->row['shipping_firstname'],
				'shipping_lastname'       => $order_query->row['shipping_lastname'],
				'shipping_company'        => $order_query->row['shipping_company'],
				'shipping_address_1'      => $order_query->row['shipping_address_1'],
				'shipping_address_2'      => $order_query->row['shipping_address_2'],
				'shipping_postcode'       => $order_query->row['shipping_postcode'],
				'shipping_city'           => $order_query->row['shipping_city'],
				'shipping_zone_id'        => $order_query->row['shipping_zone_id'],
				'shipping_zone'           => $order_query->row['shipping_zone'],
				'shipping_zone_code'      => $shipping_zone_code,
				'shipping_country_id'     => $order_query->row['shipping_country_id'],
				'shipping_country'        => $order_query->row['shipping_country'],
				'shipping_iso_code_2'     => $shipping_iso_code_2,
				'shipping_iso_code_3'     => $shipping_iso_code_3,
				'shipping_address_format' => $order_query->row['shipping_address_format'],
				'shipping_method'         => $order_query->row['shipping_method'],
				'comment'                 => $order_query->row['comment'],
				'total'                   => $order_query->row['total'],
				'order_status_id'         => $order_query->row['order_status_id'],
				'language_id'             => $order_query->row['language_id'],
				'currency_id'             => $order_query->row['currency_id'],
				'currency_code'           => $order_query->row['currency_code'],
				'currency_value'          => $order_query->row['currency_value'],
				'date_modified'           => $order_query->row['date_modified'],
				'date_added'              => $order_query->row['date_added'],
				'customer_group_id'       => $order_query->row['customer_group_id'],
				'ip'                      => $order_query->row['ip'],
				'forwarded_ip'            => $order_query->row['forwarded_ip']
			);
		} else {
			return false;
		}
	}
	
	public function getFilterOrders($start = 0, $limit = 20,$data) {
		if ($start < 0) {
			$start = 0;
		}

		if ($limit < 1) {
			$limit = 1;
		}
		$sql = "SELECT o.order_id, o.firstname, o.lastname, os.name as status, o.date_added, o.total, o.currency_code, o.currency_value,o.forwarded_ip FROM `" . DB_PREFIX . "order` o LEFT JOIN " . DB_PREFIX . "order_status os ON (o.order_status_id = os.order_status_id) WHERE o.customer_id = '" . (int)$this->customer->getId() . "' AND o.store_id = '" . (int)$this->config->get('config_store_id') . "'  AND o.total > '0' AND os.language_id = '" . (int)$this->config->get('config_language_id') . "'";
		if (!empty($data['filter_order_id'])) {
			if(strpos($data['filter_order_id'], '#')!== false){
				$sql .= " AND o.forwarded_ip = '" . $data['filter_order_id'] . "'";
			}else{
				$sql .= " AND o.forwarded_ip = '#" . $data['filter_order_id'] . "'";
			}
			
		}
		if (isset($data['filter_order_status_id']) && $data['filter_order_status_id'] !== '') {
			if($data['filter_order_status_id']==7){
				$sql .= " AND o.order_status_id in('7','11','10','14')";
			}else if(is_array($data['filter_order_status_id'])){
				$sql .= " AND o.order_status_id not in(".implode( ", ", $data['filter_order_status_id']).")";
			}else{
				$sql .= " AND o.order_status_id = '" . (int)$data['filter_order_status_id'] . "'";
			}
		} else {
			$sql .= " AND o.order_status_id > '0'";
		}
		if (!empty($data['filter_customer'])) {
			$sql .= " AND ( CONCAT(o.shipping_firstname, ' ', o.shipping_lastname) LIKE '%" . $this->db->escape($data['filter_customer']) . "%' OR CONCAT(o.firstname, ' ', o.lastname) LIKE '%" . $this->db->escape($data['filter_customer']) . "%')";
		}
$sql .="ORDER BY o.date_added DESC LIMIT " . (int)$start . "," . (int)$limit;
		$query = $this->db->query($sql);

		return $query->rows;
	}
	
	public function getOrders($start = 0, $limit = 20) {
		if ($start < 0) {
			$start = 0;
		}

		if ($limit < 1) {
			$limit = 1;
		}

		$query = $this->db->query("SELECT o.order_id, o.firstname, o.lastname, os.name as status, o.date_added, o.total, o.currency_code, o.currency_value, o.forwarded_ip FROM `" . DB_PREFIX . "order` o LEFT JOIN " . DB_PREFIX . "order_status os ON (o.order_status_id = os.order_status_id) WHERE o.customer_id = '" . (int)$this->customer->getId() . "' AND o.order_status_id > '0' AND o.store_id = '" . (int)$this->config->get('config_store_id') . "' AND os.language_id = '" . (int)$this->config->get('config_language_id') . "' ORDER BY o.date_added DESC LIMIT " . (int)$start . "," . (int)$limit);

		return $query->rows;
	}
	
	public function getOrderStatus($order_status_id){
		$query = $this->db->query("SELECT name FROM " . DB_PREFIX . "order_status WHERE order_status_id = '" . (int)$order_status_id . "'");
		return $query->row;
	}
	
	public function getOrderStatusByName($status_name){
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "order_status WHERE name = '" . $this->db->escape($status_name) . "'");
		return $query->row;
	}

	/*public function getOrders($data = array()) {
		$sql = "SELECT o.order_id, CONCAT(o.firstname, ' ', o.lastname) AS customer, (SELECT os.name FROM " . DB_PREFIX . "order_status os WHERE os.order_status_id = o.order_status_id AND os.language_id = '" . (int)$this->config->get('config_language_id') . "') AS order_status, o.shipping_code, o.total, o.currency_code, o.currency_value, o.date_added, o.date_modified FROM `" . DB_PREFIX . "order` o";

		if (!empty($data['filter_order_status'])) {
			$implode = array();

			$order_statuses = explode(',', $data['filter_order_status']);

			foreach ($order_statuses as $order_status_id) {
				$implode[] = "o.order_status_id = '" . (int)$order_status_id . "'";
			}

			if ($implode) {
				$sql .= " WHERE (" . implode(" OR ", $implode) . ")";
			}
		} elseif (isset($data['filter_order_status_id']) && $data['filter_order_status_id'] !== '') {
			$sql .= " WHERE o.order_status_id = '" . (int)$data['filter_order_status_id'] . "'";
		} else {
			$sql .= " WHERE o.order_status_id > '0'";
		}

		if (!empty($data['filter_order_id'])) {
			$sql .= " AND o.order_id = '" . (int)$data['filter_order_id'] . "'";
		}

		if (!empty($data['filter_customer'])) {
			$sql .= " AND CONCAT(o.firstname, ' ', o.lastname) LIKE '%" . $this->db->escape($data['filter_customer']) . "%'";
		}

		if (!empty($data['filter_date_added'])) {
			$sql .= " AND DATE(o.date_added) = DATE('" . $this->db->escape($data['filter_date_added']) . "')";
		}

		if (!empty($data['filter_date_modified'])) {
			$sql .= " AND DATE(o.date_modified) = DATE('" . $this->db->escape($data['filter_date_modified']) . "')";
		}

		if (!empty($data['filter_total'])) {
			$sql .= " AND o.total = '" . (float)$data['filter_total'] . "'";
		}

		$sort_data = array(
			'o.order_id',
			'customer',
			'order_status',
			'o.date_added',
			'o.date_modified',
			'o.total'
		);

		if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
			$sql .= " ORDER BY " . $data['sort'];
		} else {
			$sql .= " ORDER BY o.order_id";
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
	}*/

	public function getOrderProduct($order_id, $order_product_id) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "order_product WHERE order_id = '" . (int)$order_id . "' AND order_product_id = '" . (int)$order_product_id . "'");

		return $query->row;
	}

	public function getOrderProducts($order_id) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "order_product  WHERE order_id = '" . (int)$order_id . "'");

		return $query->rows;
	}
	
	public function checkProduct($product_id) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product  WHERE product_id = '" . (int)$product_id . "'");

		return $query->row;
	}
	
	public function getOrderProductsByIdList($order_id) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "order_product WHERE order_id in ('" . implode("','",$order_id). "')");

		return $query->rows;
	}
	
	public function getOrderProductsByOrderProductId($order_product_id) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "order_product WHERE order_product_id = '" . (int)$order_product_id . "'");

		return $query->rows;
	}
	
	public function updateOrderProduct($order_product_id,$quantity,$options,$design_file){
		
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "order_product WHERE order_product_id = '" . (int)$order_product_id . "'");
		$order = $query->row;
		$price = $order['price'];
		$shopify_sku = $order['shopify_sku'];
		
		if(!empty($options)){
			$sql = "SELECT price,sku_id,sku FROM " . DB_PREFIX . "product_sku WHERE product_id='".$order['product_id']."' AND product_options like '%" .$options. "%'";
			//print_r();
			//echo($sql);
			$query2 = $this->db->query($sql);
			if($query2->row){
				//echo($sql);
				$price = $query2->row['price'];
				//$shopify_sku = $query2->row['sku'].'.'.$query2->row['sku_id'];
			}
			
		}
		
		$this->db->query("UPDATE `" . DB_PREFIX . "order_product` SET options = '" . $this->db->escape($options). "',price='".(float)$price."', quantity = '" . (int)$quantity . "',total ='".(float)$price*((int)$quantity)."',shopify_sku = '" . $this->db->escape($shopify_sku). "' WHERE order_product_id = '" . (int)$order_product_id . "'");
		$total = (float)$price*((int)$quantity)-$order['total'];
		$this->db->query("UPDATE `" . DB_PREFIX . "order` SET total = total+".(float)$total." WHERE order_id = '" . (int)$order['order_id'] . "'");

	}
	
	public function updateOrderStatus($order_id,$order_status_id){
		$this->db->query("UPDATE `" . DB_PREFIX . "order` SET order_status_id = '" . (int)$order_status_id . "' WHERE order_id = '" . $order_id . "'");

	}

	public function getOrderOption($order_option_id) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "order_option WHERE order_option_id = '" . (int)$order_option_id . "'");
		return $query->rows;
	}

	public function getOrderOptions($order_id, $order_product_id) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "order_option WHERE order_id = '" . (int)$order_id . "' AND order_product_id = '" . (int)$order_product_id . "'");

		return $query->rows;
	}

	public function getOrderVouchers($order_id) {
		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "order_voucher` WHERE order_id = '" . (int)$order_id . "'");

		return $query->rows;
	}

	public function getOrderTotals($order_id) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "order_total WHERE order_id = '" . (int)$order_id . "' ORDER BY sort_order");

		return $query->rows;
	}

	public function getOrderHistories($order_id) {
		$query = $this->db->query("SELECT date_added, os.name AS status, oh.comment, oh.notify, oh.shopping_number, oh.shopping_method FROM " . DB_PREFIX . "order_history oh LEFT JOIN " . DB_PREFIX . "order_status os ON oh.order_status_id = os.order_status_id WHERE oh.order_id = '" . (int)$order_id . "' AND os.language_id = '" . (int)$this->config->get('config_language_id') . "' ORDER BY oh.date_added");

		return $query->rows;
	}

	public function getStatusTotalOrders() {
		$query = $this->db->query("SELECT o.total,o.order_id,o.order_status_id FROM `" . DB_PREFIX . "order` o WHERE customer_id = '" . (int)$this->customer->getId() . "' AND o.order_status_id > '0' AND o.store_id = '" . (int)$this->config->get('config_store_id') . "'  AND o.total > '0'");

		return $query->rows;
	}
	
	public function getOrdersStatusByOrderId($order_id) {
		$query = $this->db->query("SELECT o.order_status_id FROM `" . DB_PREFIX . "order` o WHERE order_id = '" . $order_id . "'");
		return $query->rows;
	}
	
	public function getTotalOrders() {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM `" . DB_PREFIX . "order` o WHERE customer_id = '" . (int)$this->customer->getId() . "' AND o.order_status_id > '0' AND o.store_id = '" . (int)$this->config->get('config_store_id') . "'");

		return $query->row['total'];
	}
	
	public function geFiltertTotalOrders($data) {
		
		$sql = "SELECT COUNT(*) AS total FROM `" . DB_PREFIX . "order` o WHERE customer_id = '" . (int)$this->customer->getId() . "' AND o.store_id = '" . (int)$this->config->get('config_store_id') . "'  AND o.total > '0' ";
		if (!empty($data['filter_order_id'])) {
			$sql .= " AND o.order_id = '" . (int)$data['filter_order_id'] . "'";
		}
		if (isset($data['filter_order_status_id']) && $data['filter_order_status_id'] !== '') {
			if($data['filter_order_status_id']==7){
				$sql .= " AND o.order_status_id in('7','11','10','14')";
			}else if(is_array($data['filter_order_status_id'])){
				$sql .= " AND o.order_status_id not in(".implode( ", ", $data['filter_order_status_id']).")";
			}else{
				$sql .= " AND o.order_status_id = '" . (int)$data['filter_order_status_id'] . "'";
			}
			
		} else {
			$sql .= " AND o.order_status_id > '0'";
		}
		if (!empty($data['filter_customer'])) {
			$sql .= " AND CONCAT(o.shipping_firstname, ' ', o.shipping_lastname) LIKE '%" . $this->db->escape($data['filter_customer']) . "%'";
		}
		$query = $this->db->query($sql);

		return $query->row['total'];
	}

	public function getTotalOrderProductsByOrderId($order_id) {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "order_product WHERE order_id = '" . (int)$order_id . "'");

		return $query->row['total'];
	}

	public function getTotalOrderVouchersByOrderId($order_id) {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM `" . DB_PREFIX . "order_voucher` WHERE order_id = '" . (int)$order_id . "'");

		return $query->row['total'];
	}
	
	public function getOrderLastAddDate($customer_group_id) {
		$query = $this->db->query("SELECT date_added FROM " . DB_PREFIX . "order WHERE customer_group_id = '".(int)$customer_group_id."'  ORDER BY date_added DESC LIMIT 1");
		if(isset($query->row['date_added'])){
			return $query->row['date_added'];
		}
		return 0;
	}
	
	public function addOrder($data) {
		
		$query = $this->db->query("SELECT order_status_id FROM " . DB_PREFIX . "order WHERE  customer_id = '" . (int)$data['customer_id'] . "' AND forwarded_ip = '" . $this->db->escape($data['forwarded_ip']) . "'");
		if(isset($query->row['order_status_id'])){
			$sql = "UPDATE `" . DB_PREFIX . "order` SET  invoice_prefix = '" . $this->db->escape($data['invoice_prefix']) . "', store_id = '" . (int)$data['store_id'] . "', store_name = '" . $this->db->escape($data['store_name']) . "', store_url = '" . $this->db->escape($data['store_url']) . "', customer_id = '" . (int)$data['customer_id'] . "', customer_group_id = '" . (int)$data['customer_group_id'] . "', firstname = '" . $this->db->escape($data['firstname']) . "', lastname = '" . $this->db->escape($data['lastname']) . "', email = '" . $this->db->escape($data['email']) . "', telephone = '" . $this->db->escape($data['telephone']) . "', custom_field = '" . $this->db->escape(isset($data['custom_field']) ? json_encode($data['custom_field']) : '') . "', payment_firstname = '" . $this->db->escape($data['payment_firstname']) . "', payment_lastname = '" . $this->db->escape($data['payment_lastname']) . "', payment_company = '" . $this->db->escape($data['payment_company']) . "', payment_address_1 = '" . $this->db->escape($data['payment_address_1']) . "', payment_address_2 = '" . $this->db->escape($data['payment_address_2']) . "', payment_city = '" . $this->db->escape($data['payment_city']) . "', payment_postcode = '" . $this->db->escape($data['payment_postcode']) . "', payment_country = '" . $this->db->escape($data['payment_country']) . "', payment_country_id = '" . (int)$data['payment_country_id'] . "', payment_zone = '" . $this->db->escape($data['payment_zone']) . "', payment_zone_id = '" . (int)$data['payment_zone_id'] . "', payment_address_format = '" . $this->db->escape($data['payment_address_format']) . "', payment_custom_field = '" . $this->db->escape(isset($data['payment_custom_field']) ? json_encode($data['payment_custom_field']) : '') . "', payment_method = '" . $this->db->escape($data['payment_method']) . "', payment_code = '" . $this->db->escape($data['payment_code']) . "', shipping_firstname = '" . $this->db->escape($data['shipping_firstname']) . "', shipping_lastname = '" . $this->db->escape($data['shipping_lastname']) . "', shipping_company = '" . $this->db->escape($data['shipping_company']) . "', shipping_address_1 = '" . $this->db->escape($data['shipping_address_1']) . "', shipping_address_2 = '" . $this->db->escape($data['shipping_address_2']) . "', shipping_city = '" . $this->db->escape($data['shipping_city']) . "', shipping_postcode = '" . $this->db->escape($data['shipping_postcode']) . "', shipping_country = '" . $this->db->escape($data['shipping_country']) . "', shipping_country_id = '" . (int)$data['shipping_country_id'] . "', shipping_zone = '" . $this->db->escape($data['shipping_zone']) . "', shipping_zone_id = '" . (int)$data['shipping_zone_id'] . "', shipping_address_format = '" . $this->db->escape($data['shipping_address_format']) . "', shipping_custom_field = '" . $this->db->escape(isset($data['shipping_custom_field']) ? json_encode($data['shipping_custom_field']) : '') . "', shipping_method = '" . $this->db->escape($data['shipping_method']) . "', shipping_code = '" . $this->db->escape($data['shipping_code']) . "', comment = '" . $this->db->escape($data['comment']) . "', total = '" . (float)$data['total'] . "', affiliate_id = '" . (int)$data['affiliate_id'] . "', commission = '" . (float)$data['commission'] . "', marketing_id = '" . (int)$data['marketing_id'] . "', tracking = '" . $this->db->escape($data['tracking']) . "', language_id = '" . (int)$data['language_id'] . "', currency_id = '" . (int)$data['currency_id'] . "', currency_code = '" . $this->db->escape($data['currency_code']) . "', currency_value = '" . (float)$data['currency_value'] . "', ip = '" . $this->db->escape($data['ip']) . "', forwarded_ip = '" .  $this->db->escape($data['forwarded_ip']) . "', user_agent = '" . $this->db->escape($data['user_agent']) . "', accept_language = '" . $this->db->escape($data['accept_language']) . "', date_added = '" . $this->db->escape(date("Y-m-d H:i:s" ,strtotime($data['date_added']))) . "', date_modified ='" . $this->db->escape(date("Y-m-d H:i:s" ,strtotime($data['date_modified']))) . "',order_status_id='".(($data['order_status_id']==1)?($query->row['order_status_id']):$data['order_status_id'])."'";
			//print_r('123');
			//print_r($sql);
			$this->db->query($sql);
			return ;
		}
		
		$this->db->query("INSERT INTO `" . DB_PREFIX . "order` SET  invoice_prefix = '" . $this->db->escape($data['invoice_prefix']) . "', store_id = '" . (int)$data['store_id'] . "', store_name = '" . $this->db->escape($data['store_name']) . "', store_url = '" . $this->db->escape($data['store_url']) . "', customer_id = '" . (int)$data['customer_id'] . "', customer_group_id = '" . (int)$data['customer_group_id'] . "', firstname = '" . $this->db->escape($data['firstname']) . "', lastname = '" . $this->db->escape($data['lastname']) . "', email = '" . $this->db->escape($data['email']) . "', telephone = '" . $this->db->escape($data['telephone']) . "', custom_field = '" . $this->db->escape(isset($data['custom_field']) ? json_encode($data['custom_field']) : '') . "', payment_firstname = '" . $this->db->escape($data['payment_firstname']) . "', payment_lastname = '" . $this->db->escape($data['payment_lastname']) . "', payment_company = '" . $this->db->escape($data['payment_company']) . "', payment_address_1 = '" . $this->db->escape($data['payment_address_1']) . "', payment_address_2 = '" . $this->db->escape($data['payment_address_2']) . "', payment_city = '" . $this->db->escape($data['payment_city']) . "', payment_postcode = '" . $this->db->escape($data['payment_postcode']) . "', payment_country = '" . $this->db->escape($data['payment_country']) . "', payment_country_id = '" . (int)$data['payment_country_id'] . "', payment_zone = '" . $this->db->escape($data['payment_zone']) . "', payment_zone_id = '" . (int)$data['payment_zone_id'] . "', payment_address_format = '" . $this->db->escape($data['payment_address_format']) . "', payment_custom_field = '" . $this->db->escape(isset($data['payment_custom_field']) ? json_encode($data['payment_custom_field']) : '') . "', payment_method = '" . $this->db->escape($data['payment_method']) . "', payment_code = '" . $this->db->escape($data['payment_code']) . "', shipping_firstname = '" . $this->db->escape($data['shipping_firstname']) . "', shipping_lastname = '" . $this->db->escape($data['shipping_lastname']) . "', shipping_company = '" . $this->db->escape($data['shipping_company']) . "', shipping_address_1 = '" . $this->db->escape($data['shipping_address_1']) . "', shipping_address_2 = '" . $this->db->escape($data['shipping_address_2']) . "', shipping_city = '" . $this->db->escape($data['shipping_city']) . "', shipping_postcode = '" . $this->db->escape($data['shipping_postcode']) . "', shipping_country = '" . $this->db->escape($data['shipping_country']) . "', shipping_country_id = '" . (int)$data['shipping_country_id'] . "', shipping_zone = '" . $this->db->escape($data['shipping_zone']) . "', shipping_zone_id = '" . (int)$data['shipping_zone_id'] . "', shipping_address_format = '" . $this->db->escape($data['shipping_address_format']) . "', shipping_custom_field = '" . $this->db->escape(isset($data['shipping_custom_field']) ? json_encode($data['shipping_custom_field']) : '') . "', shipping_method = '" . $this->db->escape($data['shipping_method']) . "', shipping_code = '" . $this->db->escape($data['shipping_code']) . "', comment = '" . $this->db->escape($data['comment']) . "', total = '" . (float)$data['total'] . "', affiliate_id = '" . (int)$data['affiliate_id'] . "', commission = '" . (float)$data['commission'] . "', marketing_id = '" . (int)$data['marketing_id'] . "', tracking = '" . $this->db->escape($data['tracking']) . "', language_id = '" . (int)$data['language_id'] . "', currency_id = '" . (int)$data['currency_id'] . "', currency_code = '" . $this->db->escape($data['currency_code']) . "', currency_value = '" . (float)$data['currency_value'] . "', ip = '" . $this->db->escape($data['ip']) . "', forwarded_ip = '" .  $this->db->escape($data['forwarded_ip']) . "', user_agent = '" . $this->db->escape($data['user_agent']) . "', accept_language = '" . $this->db->escape($data['accept_language']) . "', date_added = '" . $this->db->escape(date("Y-m-d H:i:s" ,strtotime($data['date_added']))) . "', date_modified ='" . $this->db->escape(date("Y-m-d H:i:s" ,strtotime($data['date_modified']))) . "',order_status_id='".(int)$data['order_status_id']."'");

		$order_id = $this->db->getLastId();
		$result = array();
if (isset($data['products'])) {
			foreach ($data['products'] as $product) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "order_product SET order_id = '" . $order_id . "', product_id = '" . (int)$product['product_id'] . "', name = '" . $this->db->escape($product['name']) . "', model = '" . $this->db->escape($product['model']) . "', quantity = '" . (int)$product['quantity'] . "', price = '" . (float)$product['price'] . "', total = '" . (float)$product['total'] . "', tax = '" . (float)$product['tax'] . "', reward = '" . (int)$product['reward'] . "', shopify_price = '" . (float)$product['shopify_price'] . "',shopify_sku = '" . $this->db->escape($product['shopify_sku']) . "', line_item_id = '" . $product['line_item_id'] . "', line_item_order_id = '" . $product['line_item_order_id'] . "', shopify_product_id = '" . $product['shopify_product_id'] . "'");
				$result[] = $this->db->getLastId();
			}
			
				
		}
		/*// Products
		if (isset($data['products'])) {
			foreach ($data['products'] as $product) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "order_product SET order_id = '" . (int)$order_id . "', product_id = '" . (int)$product['product_id'] . "', name = '" . $this->db->escape($product['name']) . "', model = '" . $this->db->escape($product['model']) . "', quantity = '" . (int)$product['quantity'] . "', price = '" . (float)$product['price'] . "', total = '" . (float)$product['total'] . "', tax = '" . (float)$product['tax'] . "', reward = '" . (int)$product['reward'] . "'");

				$order_product_id = $this->db->getLastId();

				foreach ($product['option'] as $option) {
					$this->db->query("INSERT INTO " . DB_PREFIX . "order_option SET order_id = '" . (int)$order_id . "', order_product_id = '" . (int)$order_product_id . "', product_option_id = '" . (int)$option['product_option_id'] . "', product_option_value_id = '" . (int)$option['product_option_value_id'] . "', name = '" . $this->db->escape($option['name']) . "', `value` = '" . $this->db->escape($option['value']) . "', `type` = '" . $this->db->escape($option['type']) . "'");
				}
			}
		}

		// Gift Voucher
		$this->load->model('extension/total/voucher');

		// Vouchers
		if (isset($data['vouchers'])) {
			foreach ($data['vouchers'] as $voucher) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "order_voucher SET order_id = '" . (int)$order_id . "', description = '" . $this->db->escape($voucher['description']) . "', code = '" . $this->db->escape($voucher['code']) . "', from_name = '" . $this->db->escape($voucher['from_name']) . "', from_email = '" . $this->db->escape($voucher['from_email']) . "', to_name = '" . $this->db->escape($voucher['to_name']) . "', to_email = '" . $this->db->escape($voucher['to_email']) . "', voucher_theme_id = '" . (int)$voucher['voucher_theme_id'] . "', message = '" . $this->db->escape($voucher['message']) . "', amount = '" . (float)$voucher['amount'] . "'");

				$order_voucher_id = $this->db->getLastId();

				$voucher_id = $this->model_extension_total_voucher->addVoucher($order_id, $voucher);

				$this->db->query("UPDATE " . DB_PREFIX . "order_voucher SET voucher_id = '" . (int)$voucher_id . "' WHERE order_voucher_id = '" . (int)$order_voucher_id . "'");
			}
		}

		// Totals
		if (isset($data['totals'])) {
			foreach ($data['totals'] as $total) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "order_total SET order_id = '" . (int)$order_id . "', code = '" . $this->db->escape($total['code']) . "', title = '" . $this->db->escape($total['title']) . "', `value` = '" . (float)$total['value'] . "', sort_order = '" . (int)$total['sort_order'] . "'");
			}
		}*/

		return $order_id;
	}
	
	public function addProductSku($product){
		if ($product) {
			//foreach ($data as $product) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "product_sku SET product_id = '" . (int)$product['product_id'] . "', sku = '" . $this->db->escape($product['sku']) . "', product_model = '" . $this->db->escape($product['model']) . "', product_option_id = '" . (int)$product['product_option_id'] . "',option_value_id = '" . (int)$product['option_value_id'] . "', product_options = '" . $this->db->escape($product['product_options']) . "', design_file = '" . $this->db->escape($product['design_file']) . "', price = '" .(float)$product['price']. "', option_file = '" .$this->db->escape(isset($product['option_file'])?json_encode($product['option_file']):'') . "', customer_id = '" .(int)$this->customer->getId() . "'");
			//}
			return $this->db->getLastId();
		}
		return 0;
	}
	
	
	
	public function getProductSku($sku) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_variants WHERE variants_sku = '" .$this->db->escape($sku). "' ");

		return $query->row;
	}
	
	public function getOrderProductsBySku($sku) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_variants WHERE variants_sku = '" .$this->db->escape($sku). "' ");

		return $query->row;
	}
	
	
	public function saveShopifyOrders($orders,$customer_id){
		if ($orders) {
			foreach ($orders as $order) {
				try{
					$this->db->query("INSERT INTO " . DB_PREFIX . "shopify_orders SET order_id = '" .$order['id'] . "', email = '" .(isset($order['email'])?$order['email']:'') . "', order_name = '" .(isset($order['name'])?$order['name']:'#' .$order['id']) . "', financial_status = '" .(isset($order['financial_status'])? $this->db->escape($order['financial_status']): $this->db->escape($order['status'])) . "', fulfillment_status = '" . (isset($order['fulfillment_status'])?$this->db->escape($order['fulfillment_status']):$this->db->escape($order['status'])) . "', order_json = '" .$this->db->escape(json_encode($order)) . "',  customer_id = '" .(int)$customer_id . "'");
				}catch (Exception $e)
		  {
				}
			}
		}
	}
	
	public function editAddress($order_id, $data,$country) {
		$this->db->query("UPDATE " . DB_PREFIX . "order SET email = '" . $this->db->escape($data['email']) . "', telephone = '" . $this->db->escape($data['telephone']) . "',shipping_firstname = '" . $this->db->escape($data['firstname']) . "', shipping_lastname = '" . $this->db->escape($data['lastname']) . "', shipping_company = '" . $this->db->escape($data['company']) . "', shipping_address_1 = '" . $this->db->escape($data['address_1']) . "', shipping_address_2 = '" . $this->db->escape($data['address_2']) . "', shipping_postcode = '" . $this->db->escape($data['postcode']) . "', shipping_city = '" . $this->db->escape($data['city']) . "', shipping_zone_id = '" .(isset($data['zone_id'])?(int)$data['zone_id']:0) . "', shipping_country = '" . $this->db->escape($country) . "', shipping_country_id = '" . (int)$data['country_id'] . "' WHERE order_id  = '" . (int)$order_id . "' AND customer_id = '" . (int)$this->customer->getId() . "'");

	}
	
	public function getOrderProductLineItemId($order_id) {
		$query = $this->db->query("SELECT line_item_id,line_item_order_id FROM " . DB_PREFIX . "order_product WHERE order_id = '" . (int)$order_id . "' AND quantity>0 AND price>0");

		return $query->rows;
	}
	
	public function getOrderProductSales($order_ids) {
		$query = $this->db->query("SELECT quantity,price,shopify_price FROM " . DB_PREFIX . "order_product WHERE order_id in (" .$order_ids . ") AND quantity>0 AND price>0");

		return $query->rows;
	}
	
	public function getCustomToken($customer_id){

		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "customer WHERE customer_id = '" . (int)$customer_id . "'");

		return $query->row;
	}
	
	public function getLastOrderId(){
		$query = $this->db->query("SELECT order_id FROM " . DB_PREFIX . "order  ORDER BY order_id DESC LIMIT 1");
		if(isset($query->row['order_id'])){
			return $query->row['order_id'];
		}
		return 1;
	}
	
}

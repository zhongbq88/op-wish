<?php

require __DIR__.'/vendor/autoload.php';
use phpish\shopify;

class ControllerShopifyLoadorders extends Controller {
	
	public function index(){
		
		$this->load->model('account/customer');
	    $json = array();
		//echo $this->request->get['syn'];
		if(isset($this->request->get['syn'])&&$this->request->get['syn']=='all'){
			$customer_infos = $this->model_account_customer->getCustomerByGroupId(4);	
			//print_r($customer_infos); 
			foreach($customer_infos as $customer_info){
				if(isset($customer_info['token'])&&$customer_info['lastname']=='myshopify'){
					$json =  $this->getOrders($customer_info['firstname'].'.myshopify.com',$customer_info['token'],$customer_info);
				}
			}
			
		}else{
			$customer_info = array();
			//print_r($this->customer->isLogged().$this->customer->getId());
			if ($this->customer->isLogged()) {
				$customer_info = $this->model_account_customer->getCustomer($this->customer->getId());	  
			}
			//print_r($customer_info);
			$json =  $this->getOrders($this->session->data['shop'],$this->session->data['oauth_token'],$customer_info);
			/*if(isset($json['success'])){
				$json['order_list'] = $this->load->controller('commonipl/orders/getList');
			}*/
		}
		if (isset($this->request->get['syn'])) {
			$this->response->addHeader('Content-Type: application/json');
			$this->response->setOutput(json_encode($json));
		}
	}
	
	public function getOrders($shopify,$outh_token,$customer_info){
		  //print_r($shopify.'--'.$outh_token);
		  $shopify = shopify\client($shopify, SHOPIFY_APP_API_KEY, $outh_token);
		  $json = array();
		  try
		  {
			  $this->load->model('shopify/order');
			  $adddate = $this->model_shopify_order->getOrderLastAddDate($customer_info['customer_group_id']);
			  if(isset($adddate)&&$adddate!=0){
			  	$adddate = str_replace(' ',"T",$adddate)."+00:00";
			  //print_r($adddate);
			  	$orders = $shopify('GET /admin/orders.json?status=any'/*&processed_at_min='.$adddate.'&created_at_min='.$adddate*/);
			  }else{
			  	  $orders = $shopify('GET /admin/orders.json?status=any');
			  }
			  
			  $this->load->model('localisation/order_status');
			  $order_statuses = $this->model_localisation_order_status->getOrderStatuses();
			  if(count($orders)>0){
				  $json['success'] = 'true';
			  }
			  //print_r($orders);
			  foreach($orders as $order){
				 $od = $this->initOrder($order,$order_statuses,$customer_info);
				 //print_r($od);
				 $order_id = $this->model_shopify_order->addOrder($od);
			  }
			  
			 $this->model_shopify_order->saveShopifyOrders($orders);
		  }
		  catch (shopify\ApiException $e)
		  {
			  	print_r($e->getRequest());
		print_r($e->getResponse());
			  $json['error'] = $e->getResponse();
		  }
		  catch (shopify\CurlException $e)
		  {
			  	print_r($e->getRequest());
		print_r($e->getResponse());
			  $json['error'] = $e->getResponse();
		  }
		  return $json;
	}
	
	public function initOrder($order,$order_statuses,$customer_info){
	$order_data =  array(
				'order_id'                => $order['id'],
				'email'                   => $order['email'],
				'telephone'               => isset($order['shipping_address'])?$order['shipping_address']['phone']:'',
				'custom_field'            => '',
				'payment_firstname'       => $this->getValue($order,'payment_firstname'),
				'payment_lastname'        => $this->getValue($order,'payment_lastname'),
				'payment_company'         => $this->getValue($order,'payment_company'),
				'payment_address_1'       => $this->getValue($order,'payment_address_1'),
				'payment_address_2'       => $this->getValue($order,'payment_address_2'),
				'payment_postcode'        => $this->getValue($order,'payment_postcode'),
				'payment_city'            => $this->getValue($order,'payment_city'),
				'payment_zone_id'         => 3528,//$this->getValue($order,'payment_zone_id'),
				'payment_zone'            => $this->getValue($order,'payment_zone'),
				'payment_zone_code'       => '',
				'payment_country_id'      => 222,//$this->getValue($order,'payment_country_id'),
				'payment_country'         => $this->getValue($order,'payment_country'),
				'payment_iso_code_2'      => '',
				'payment_iso_code_3'      => '',
				'payment_address_format'  => $this->getValue($order,'payment_address_format'),
				'payment_custom_field'    => '',
				'payment_method'          => $this->getValue($order,'payment_method'),
				'payment_code'            => 'pp_express',
				'shipping_firstname'      => isset($order['shipping_address'])?$order['shipping_address']['first_name']:'',
				'shipping_lastname'       => isset($order['shipping_address'])?$order['shipping_address']['last_name']:'',
				'shipping_company'        => isset($order['shipping_address'])?$order['shipping_address']['company']:'',
				'shipping_address_1'      => isset($order['shipping_address'])?$order['shipping_address']['address1']:'',
				'shipping_address_2'      => isset($order['shipping_address'])?$order['shipping_address']['address2']:'',
				'shipping_postcode'       => '',
				'shipping_city'           => isset($order['shipping_address'])?$order['shipping_address']['city']:'',
				'shipping_zone_id'        => 0,
				'shipping_zone'           => isset($order['shipping_address'])?$order['shipping_address']['province_code']:'',
				'shipping_zone_code'      => '',
				'shipping_country_id'     => 0,
				'shipping_country'        => isset($order['shipping_address'])?$order['shipping_address']['country']:'',
				'shipping_iso_code_2'     => '',
				'shipping_iso_code_3'     => '',
				'shipping_address_format' => '',
				'shipping_custom_field'   => isset($order['shipping_address'])?json_encode($order['shipping_address'], true):'',
				'shipping_method'         => 'Flat Shipping Rate',
				'shipping_code'           => isset($order['shipping_address'])?$order['shipping_address']['country_code']:'',
				'comment'                 => $order['note'],
				//'total'                   => $order['total_price'],
				'reward'                  => '',
				'order_status_id'         => $this->getOrderStatus(!empty($order['financial_status'])?$order['financial_status']:$order['fulfillment_status'],$order_statuses),
				'order_status'            => !empty($order['financial_status'])?$order['financial_status']:$order['fulfillment_status'],
				'affiliate_id'            => '',
				'affiliate_firstname'     => '',
				'affiliate_lastname'      => '',
				'commission'              => '',
				'language_id'             => 0,
				'language_code'           => '',
				'currency_id'             => 0,
				'currency_code'           => $order['currency'],
				'currency_value'          => $order['id'],
				'ip'                      => $order['browser_ip'],
				'forwarded_ip'            => $order['name'],
				'user_agent'              => $this->getValue(isset($order['client_details'])?$order['client_details']:'','user_agent'),
				'accept_language'         => $this->getValue(isset($order['client_details'])?$order['client_details']:'','accept_language'),
				'date_added'              => $order['created_at'],
				'date_modified'           => $order['updated_at']
			);
			if (!empty($customer_info)) {
				$order_data['customer_id'] = $customer_info['customer_id'];
				$order_data['customer_group_id'] = $customer_info['customer_group_id'];
				$order_data['firstname'] = $customer_info['firstname'];
				$order_data['lastname'] = $customer_info['lastname'];
				//$order_data['email'] = $customer_info['email'];
				//$order_data['telephone'] = $customer_info['telephone'];
				$order_data['custom_field'] = json_decode($customer_info['custom_field'], true);
			} elseif (isset($this->session->data['guest'])) {
				$order_data['customer_id'] = 0;
				$order_data['customer_group_id'] = $this->session->data['guest']['customer_group_id'];
				$order_data['firstname'] = $this->session->data['guest']['firstname'];
				$order_data['lastname'] = $this->session->data['guest']['lastname'];
				//$order_data['email'] = $this->session->data['guest']['email'];
				//$order_data['telephone'] = $this->session->data['guest']['telephone'];
				$order_data['custom_field'] = $this->session->data['guest']['custom_field'];
			}
			$order_data['store_id'] = $this->config->get('config_store_id');
			$order_data['store_name'] = $this->config->get('config_name');
			$od = array();
			$total = 0;
			print_r($order_data['financial_status']);
			print_r($order_data['order_status_id']);
			if($order['line_items']){
				/*foreach($order['line_items'] as $items){
					print_r($items['sku']);
				}*/
				$order_data['invoice_prefix'] = $order['line_items'][0]['sku'];
				$sku='';
				$quantity=0;
				foreach($order['line_items'] as $items){
					 $sku = $items['sku'];
					 $skus = explode('.', $items['sku']);
					 $sk = $skus[count($skus)-1];
					  /*if(empty($sk) || $sk==$items['sku'] ){
						  $sk = preg_replace('/\D/s', '',$items['sku']);
					  }*/
					  //echo $sk.",";
					 $orderProducts = $this->model_shopify_order->getOrderProductsBySku($items['sku']);
					  //print_r( $orderProducts);
					  if(isset($orderProducts['product_id'])){
						  $od[] = array(
						  'name'=> $items['name'],
						  'order_id'=> $order['id'],
						  'product_id'=> $orderProducts['product_id'],
						  'name'=> $items['name'],
						  'quantity'=> $items['quantity'],
						  'price'=> $orderProducts['price'],
						  'total'=> $orderProducts['price']*$items['quantity'],
						  'shopify_price'=> $items['price'],
						  'shopify_product_id'=> $items['product_id'],
						  'shopify_sku'=> $items['sku'],
						  'tax'=> 0,
						  'reward'=> '',
						  'line_item_id'=> $items['id'],
						  'line_item_order_id'=>  $order['id']
						  );
						  $total +=$orderProducts['price']*$items['quantity'];
					  }else{
						  $od[] = array(
						  'name'=> $items['name'],
						  'order_id'=> $order['id'],
						  'product_id'=> $items['product_id'],
						  'name'=> $items['name'],
						  'model'=> $items['title'],
						  'quantity'=> $items['quantity'],
						  'price'=> 0,
						  'total'=> 0,
						  'options'=>'',
						  'shopify_price'=> $items['price'],
						  'shopify_sku'=> $items['sku'],
						  'tax'=> 0,
						  'reward'=> '',
						  'line_item_id'=> $items['id'],
						  'line_item_order_id'=>  $order['id'],
						  'shopify_product_id'=> $items['product_id']
						  );
					 }
				//}
					if(!empty($od)){
						$order_data['products'] = $od;
						//print_r($order_data['products']);
					}
				}
			}
			if ($order_data['store_id']) {
				$order_data['store_url'] = $this->config->get('config_url');
			} else {
				if ($this->request->server['HTTPS']) {
					$order_data['store_url'] = HTTPS_SERVER;
				} else {
					$order_data['store_url'] = HTTP_SERVER;
				}
			}
			if (isset($this->request->cookie['tracking'])) {
				$order_data['tracking'] = $this->request->cookie['tracking'];

				$subtotal = $this->cart->getSubTotal();

				// Affiliate
				$affiliate_info = $this->model_account_customer->getAffiliateByTracking($this->request->cookie['tracking']);

				if ($affiliate_info) {
					$order_data['affiliate_id'] = $affiliate_info['customer_id'];
					$order_data['commission'] = ($subtotal / 100) * $affiliate_info['commission'];
				} else {
					$order_data['affiliate_id'] = 0;
					$order_data['commission'] = 0;
				}

				// Marketing
				$this->load->model('checkout/marketing');

				$marketing_info = $this->model_checkout_marketing->getMarketingByCode($this->request->cookie['tracking']);

				if ($marketing_info) {
					$order_data['marketing_id'] = $marketing_info['marketing_id'];
				} else {
					$order_data['marketing_id'] = 0;
				}
			} else {
				$order_data['affiliate_id'] = 0;
				$order_data['commission'] = 0;
				$order_data['marketing_id'] = 0;
				$order_data['tracking'] = '';
			}
			$order_data['total'] = $total;
			return $order_data;	
	}
	
	public function getValue($order,$key){
		if(isset($order[$key])){
			return $order[$key];
		}
		return '';
	}
	
	public function getOrderStatus($status,$order_statuses){
		switch($status) {
			case 'canceled':
				return $this->getStatus($order_statuses,'Canceled');
				break;
			case 'refunded':
				return  $this->getStatus($order_statuses,'Refunded');
			case 'reversed':
				return  $this->getStatus($order_statuses,'Reversed');
			case 'voided':
				return $this->getStatus($order_statuses,'Voided');
			case 'paid':
			case 'partially_paid':
				return  $this->getStatus($order_statuses,'To-Order');
		}
		return  $this->getStatus($order_statuses,'Pending');
	}
	
	public function getStatus($order_statuses,$key){
		foreach($order_statuses as $status){
			if($status['name']==$key){
				return $status['order_status_id'];
			}
		}
		return 1;
	}
	
	public function collection(){
		
		  $shopify = shopify\client($this->session->data['shop'], SHOPIFY_APP_API_KEY, $this->session->data['oauth_token']);
		  
		  try
		  {

			  $collection = $shopify('GET /admin/collection_listings.json');
			  return $collection['collections'];
		  }
		  catch (shopify\ApiException $e)
		  {
			  	//print_r($e->getRequest());
				//print_r($e->getResponse());
			  
		  }
		  catch (shopify\CurlException $e)
		  {
			  	//print_r($e->getRequest());
				//print_r($e->getResponse());
			 
		  }
		  return false;
	}
}

?>
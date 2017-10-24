<?php

class Controllerwoocommerceloadorders extends Controller {
	
	public function index(){
		
		$this->load->model('account/customer');
	    $json = array();
		//echo $this->request->get['syn'];
		if(isset($this->request->get['syn'])&&$this->request->get['syn']=='all'){
			$customer_infos = $this->model_account_customer->getCustomerGroupByName('woocommerce');	
			//print_r($customer_infos); 
			foreach($customer_infos as $customer_info){
				if(isset($customer_info['consumer_key'])){
					$json =  $this->getOrders($customer_info);
				}
			}
			
		}else{
			$customer_info = array();
			//print_r($this->customer->isLogged().$this->customer->getId());
			if ($this->customer->isLogged()) {
				$customer_info = $this->model_account_customer->getCustomer($this->customer->getId());	  
			}
			//print_r($customer_info);
			$json =  $this->getOrders($customer_info);
			if(isset( $json['success'])){
				$json['order_list'] = $this->load->controller('commonipl/orders/getList');
			}
			
		}
		if (isset($this->request->get['syn'])) {
			$this->response->addHeader('Content-Type: application/json');
			$this->response->setOutput(json_encode($json));
		}
	}
	
	public function getOrders($customer_info){
		  //print_r($shopify.'--'.$outh_token);
		  if(!isset($customer_info)){
			  return ;
		   }
		  $json = array();
		  try
		  {
			  $this->load->model('commonipl/order');
			  
			  include(__DIR__.'/oauthclient.php');
			$orders = Oauthclient::getInstance($customer_info['store'],$customer_info['consumer_key'],$customer_info['consumer_secret'],$customer_info['token'])->get('orders');
			
			 //print_r($orders);
			  $this->load->model('localisation/order_status');
			  $order_statuses = $this->model_localisation_order_status->getOrderStatuses();
			  if(count($orders)>0){
				  $json['success'] = 'true';
			  }
			  //print_r($orders);
			  foreach($orders as $order){
				 $od = $this->initOrder($order,$order_statuses,$customer_info);
				 //print_r($od);
				 $order_id = $this->model_commonipl_order->addOrder($od);
			  }
			  $this->model_commonipl_order->saveShopifyOrders($orders,$customer_info['customer_id']);
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
				'email'                   => isset($order['billing'])?$order['billing']['email']:'',
				'telephone'               => isset($order['billing'])?$order['billing']['phone']:'',
				'custom_field'            => '',
				'payment_firstname'       =>isset($order['billing'])?$order['billing']['first_name']:'',
				'payment_lastname'        => isset($order['billing'])?$order['billing']['last_name']:'',
				'payment_company'         => isset($order['billing'])?$order['billing']['company']:'',
				'payment_address_1'       => isset($order['billing'])?$order['billing']['address_1']:'',
				'payment_address_2'       => isset($order['billing'])?$order['billing']['address_2']:'',
				'payment_postcode'        => isset($order['billing'])?$order['billing']['postcode']:'',
				'payment_city'            => isset($order['billing'])?$order['billing']['city']:'',
				'payment_zone_id'         => 3528,//$this->getValue($order,'payment_zone_id'),
				'payment_zone'            => $this->getValue($order,'payment_zone'),
				'payment_zone_code'       => '',
				'payment_country_id'      => 222,//$this->getValue($order,'payment_country_id'),
				'payment_country'         =>  isset($order['billing'])?$order['billing']['country']:'',
				'payment_iso_code_2'      => '',
				'payment_iso_code_3'      => '',
				'payment_address_format'  => $this->getValue($order,'payment_address_format'),
				'payment_custom_field'    => '',
				'payment_method'          => $this->getValue($order,'payment_method'),
				'payment_code'            => $this->getValue($order,'payment_code'),
				'shipping_firstname'      => isset($order['shipping'])?$order['shipping']['first_name']:'',
				'shipping_lastname'       => isset($order['shipping'])?$order['shipping']['last_name']:'',
				'shipping_company'        => isset($order['shipping'])?$order['shipping']['company']:'',
				'shipping_address_1'      => isset($order['shipping'])?$order['shipping']['address_1']:'',
				'shipping_address_2'      => isset($order['shipping'])?$order['shipping']['address_2']:'',
				'shipping_postcode'       => isset($order['shipping'])?$order['shipping']['postcode']:'',
				'shipping_city'           => isset($order['shipping'])?$order['shipping']['city']:'',
				'shipping_zone_id'        => 0,
				'shipping_zone'           => '',
				'shipping_zone_code'      => '',
				'shipping_country_id'     => 0,
				'shipping_country'        => isset($order['shipping'])?$order['shipping']['country']:'',
				'shipping_iso_code_2'     => '',
				'shipping_iso_code_3'     => '',
				'shipping_address_format' => '',
				'shipping_custom_field'   => isset($order['shipping'])?json_encode($order['shipping'], true):'',
				'shipping_method'         => isset($order['shipping'])?$order['shipping']['state']:'',
				'shipping_code'           => '',
				'comment'                 => $order['customer_note'],
				//'total'                   => $order['total_price'],
				'reward'                  => '',
				'order_status_id'         => $this->getOrderStatus($order['status']),
				'order_status'            => $order['status'],
				'affiliate_id'            => '',
				'affiliate_firstname'     => '',
				'affiliate_lastname'      => '',
				'commission'              => '',
				'language_id'             => 0,
				'language_code'           => '',
				'currency_id'             => 0,
				'currency_code'           => $order['currency'],
				'currency_value'          => '',
				'ip'                      => $order['customer_ip_address'],
				'forwarded_ip'            =>  '#'.$order['id'],
				'user_agent'              =>  $this->getValue(isset($order['customer_user_agent'])?$order['customer_user_agent']:'','user_agent'),
				'accept_language'         =>  '',
				'date_added'              => $order['date_created'],
				'date_modified'           => $order['date_modified']
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
					  $orderProducts = $this->model_commonipl_order->getOrderProductsBySku($sk);
					  //print_r( $orderProducts);
					  if(count($orderProducts)>0&& $orderProducts[0]['sku'].'.'.$orderProducts[0]['sku_id']==$items['sku']){
						  $od[] = array(
						  'name'=> $items['name'],
						  'order_id'=> $order['id'],
						  'product_id'=> $orderProducts[0]['product_id'],
						  'model'=> $orderProducts[0]['product_model'],
						  'quantity'=> $items['quantity'],
						  'price'=> $orderProducts[0]['price'],
						  'total'=> $orderProducts[0]['price']*$items['quantity'],
						  'shopify_price'=> $items['price'],
						  'shopify_sku'=> $items['sku'],
						  'tax'=> 0,
						  'reward'=> '',
						  'line_item_id'=> $items['id'],
						  'line_item_order_id'=>  $order['id']
						  );
						  $total +=$orderProducts[0]['price']*$items['quantity'];
					  }else{
						  $od[] = array(
						  'name'=> $items['name'],
						  'order_id'=> $order['id'],
						  'product_id'=> $items['product_id'],
						  'name'=> $items['name'],
						  'model'=>  $items['name'],
						  'quantity'=> $items['quantity'],
						  'price'=> 0,
						  'total'=> 0,
						  'shopify_price'=> $items['price'],
						  'shopify_sku'=> $items['sku'],
						  'tax'=> 0,
						  'reward'=> '',
						  'line_item_id'=> $items['id'],
						  'line_item_order_id'=>  $order['id']
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
	
	public function getOrderStatus($status){
		switch($status) {
			case 'canceled':
				return $this->config->get('payment_pp_express_canceled_reversal_status_id');
				break;
			case 'refunded':
				return  $this->config->get('payment_pp_express_refunded_status_id');
			case 'reversed':
				return  $this->config->get('payment_pp_express_reversed_status_id');
			case 'voided':
				return  $this->config->get('payment_pp_express_voided_status_id');
		}
		return 1;
	}
}

?>
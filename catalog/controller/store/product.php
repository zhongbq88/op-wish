<?php
class ControllerStoreProduct extends Controller {


	public function index() {
		$this->load->language('store/product');
		$this->load->model('commonipl/product');
		$this->load->model('tool/image');
		if(isset($this->request->get['product_id'])){
			$product_id = $this->request->get['product_id'];
		}else{
			$product_id = 0;
		}
		$product = $this->model_commonipl_product->getPublishProductById($product_id);
		if(isset($product)){
			$p = json_decode($product['shopify_product_json'],true);
			
			//print_r($p);
			//print_r($images);
			$data['product'] = array(
				'product_id'=>$product_id,
				'name'=>$p['title'],
				'des'=>$p['body_html'],
				'style'=>$p['options'][0],
				'tags' =>$p['tags'],
				'variants'=> $p['variants']
			);
			$options = array();
			$images = $p['images'];
			$data['product']['images'] = array();
		
			foreach($images as $key=> $image){
					//echo str_replace(HTTP_SERVER.'image/', '', $image['src']);
				$data['product']['images'][] = array(
					'thumb_image'=>$this->model_tool_image->resize(str_replace(HTTP_SERVER.'image/', '', $image['src']), 142, 142),
					'pop_image'=>$image['src'],
					'image_pos'=>$key
				);
			}
			$option1 ='';
			$optionIndex =0;
			//print_r($p['options']);
			$optionCount = count($p['options']);
			$option = array();
			$p_index =-1;
			$style = array();
			foreach($p['variants'] as $key=> $variant){
				//print_r($variant);
				if($option1!=$variant['option1']){
					$option[$variant['option1']] = array();
					$p_index++;
					$style[] = array(
						'price'=>$variant['price'],
							'compare_at_price'=>$variant['compare_at_price'],
							'name'=>$variant['option1']
					); 
				}
				$index =0;
				for($i=1;$i<$optionCount;$i++){
					if(isset($variant['option'.($i+1)])){
						$option[$variant['option1']][$p['options'][$i]['name']][] = array(
							'price'=>$variant['price'],
							'compare_at_price'=>$variant['compare_at_price'],
							'name'=>$variant['option'.($i+1)]
						);
						$index++;
					}
				}				
				//print_r($variant);
				$option1 = $variant['option1'];
			}
		}
		//print_r($option);
		//$data['opts'] = $option;
		$data['product']['style'] = array(
			'name' =>$p['options'][0]['name'],
			'values'=>$style
		);
		$data['product']['options'] = $option;
		$data['footer'] = $this->load->controller('store/footer');
		$data['header'] = $this->load->controller('store/header');
		
		//print_r($this->);
		$this->response->setOutput($this->load->view('store/product', $data));
	}
	
	
	public function createorder(){
		
		$this->load->language('store/product');
		$this->load->model('commonipl/product');
		$this->load->model('tool/image');
		if(isset($this->request->post['product_id'])){
			$product_id = $this->request->post['product_id'];
		}else{
			$product_id = 0;
		}
		$product = $this->model_commonipl_product->getPublishProductById($product_id);
		$json = array();
		if(isset($product)){
			$p = json_decode($product['shopify_product_json'],true);
			$this->load->model('commonipl/order');
			$p['id'] =  '#'.($this->model_commonipl_order->getLastOrderId()+1);
			//print_r($p['id']);
			//print_r($this->model_commonipl_order->getLastOrderId());
			$order = $this->initOrder($p,$product['add_product_id']);
			//print_r($order );
			$order_id = $this->model_commonipl_order->addOrder($order);
			//print_r($order_id);
			if($order_id){
				$json['success'] = 'index.php?route=commonipl/orders/info&order_id='.$order_id;
			}
			
		}
		
		//$json['success'] = 'index.php?route=commonipl/dashboard';
		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}
	
	public function initOrder($order,$add_product_id){
		
		if(isset($this->request->post['selected'])){
			$selected = array_filter($this->request->post['selected']);
		}else{
			$selected = array();
		}
		
		if(isset($this->request->post['quantity'])){
			$qty = array_filter($this->request->post['quantity']);
		}else{
			$qty = array();
		}
		$this->load->model('account/customer');
		$customer_info = array();
		if ($this->customer->isLogged()) {
				$customer_info = $this->model_account_customer->getCustomer($this->customer->getId());	  
		}
		$order_data =  array(
		
				'order_id'                => $order['id'],
				'email'                   => $customer_info['email'],
				'telephone'               => $customer_info['telephone'],
				'custom_field'            => '',
				'payment_firstname'       => '',
				'payment_lastname'        => '',
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
				'comment'                 => '',
				//'total'                   => $order['total_price'],
				'reward'                  => '',
				'order_status_id'         => 1,
				'order_status'            => 'Padding',
				'affiliate_id'            => '',
				'affiliate_firstname'     => '',
				'affiliate_lastname'      => '',
				'commission'              => '',
				'language_id'             => 0,
				'language_code'           => '',
				'currency_id'             => 0,
				'currency_code'           => '',
				'currency_value'          => '',
				'ip'                      => '',
				'forwarded_ip'            => $order['id'],
				'user_agent'              => $this->getValue(isset($order['customer_user_agent'])?$order['customer_user_agent']:'','user_agent'),
				'accept_language'         => '',
				'date_added'              => date("Y-m-d h:i:s"),
				'date_modified'           => date("Y-m-d h:i:s")
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
			//	print_r($order_data);
			if($order['variants']){
				/*foreach($order['line_items'] as $items){
					print_r($items['sku']);
				}*/
				
				$order_data['invoice_prefix'] = '';
				$sku='';
				$quantity=0;
				$optionCount = count($order['options']);
				foreach($order['variants'] as $key => $items){
					
					if(!in_array($key+1,$selected)){
						continue;
					}
					 $sku = $items['sku'];
					 $skus = explode('.', $items['sku']);
					 $sk = $skus[count($skus)-1];
					  /*if(empty($sk) || $sk==$items['sku'] ){
						  $sk = preg_replace('/\D/s', '',$items['sku']);
					  }*/
					  //echo $sk.",";
					  
					  $name ='';
					  for($i=1;$i<=$optionCount;$i++){
						if(isset($items['option'.$i])){
							$name .='-'.$items['option'.$i];
						}
					  }
					  $name = substr($name,1);
					  //print_r($qty[$key]);
					  $orderProducts = $this->model_commonipl_order->getOrderProductsBySku($sk);
					  //print_r( $orderProducts);
					  if(count($orderProducts)>0&& $orderProducts[0]['sku'].'.'.$orderProducts[0]['sku_id']==$items['sku']){
						  $od[] = array(
						  'name'=> $name,
						  'order_id'=> $order['id'],
						  'product_id'=> $orderProducts[0]['product_id'],
						  'model'=> $orderProducts[0]['product_model'],
						  'quantity'=> $qty[$key],
						  'price'=> $orderProducts[0]['price'],
						  'total'=> $orderProducts[0]['price']*$qty[$key],
						  'shopify_price'=> $items['price'],
						  'shopify_sku'=> $items['sku'],
						  'tax'=> 0,
						  'reward'=> '',
						  'line_item_id'=> $key,
						  'line_item_order_id'=>  $order['id'],
						  'shopify_product_id' =>$add_product_id
						  );
						  $total +=$orderProducts[0]['price']*$qty[$key];
					  }else{
						  $od[] = array(
						  'name'=> $name,
						  'order_id'=> $order['id'],
						  'product_id'=> $items['product_id'],
						  'name'=> $items['name'],
						  'model'=>  $items['name'],
						  'quantity'=> $qty[$key],
						  'price'=> 0,
						  'total'=> 0,
						  'shopify_price'=> $items['price'],
						  'shopify_sku'=> $items['sku'],
						  'tax'=> 0,
						  'reward'=> '',
						  'line_item_id'=> $key,
						  'line_item_order_id'=>  $order['id'],
						  'shopify_product_id' =>$add_product_id
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
			//print_r($order_data);
			return $order_data;	
	}
	
	public function getValue($order,$key){
		if(isset($order[$key])){
			return $order[$key];
		}
		return '';
	}
	
}

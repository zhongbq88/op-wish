<?php
class ControllerCommoniplOrders extends Controller {
	public function index() {
		if (!$this->customer->isLogged()) {
			$this->session->data['redirect'] = $this->url->link('account/order', '', true);
			$this->response->redirect($this->url->link('account/login', '', true));
		}
		$this->load->model('localisation/order_status');

		$data['order_statuses'] = $this->model_localisation_order_status->getOrderStatuses();
		$index = 0;
		foreach($data['order_statuses'] as $result){
			if($result['order_status_id']!=1 && $result['order_status_id']!=3 && $result['order_status_id']!=7&& $result['order_status_id']!=2&& $result[	'order_status_id']!=18){
				unset($data['order_statuses'][$index]);
			}
			$index++;
		}
		$data['continue'] = $this->url->link('commonipl/account', '', true);
		$data['footer'] = $this->load->controller('common/footer');
		$data['header'] = $this->load->controller('common/header');
		$data['paid'] = $this->url->link('extension/payment/pp_express/ipn', '', true);
		$data['sys_action'] = $this->url->link('shopify/loadorders&syn=true','', true);
		$data['order_list'] = $this->getList();
		$this->response->setOutput($this->load->view('commonipl/orders', $data));
	}
	
	public function filter(){
		$json['success'] = $this->getList();
		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}
	
	public function getList(){
		$data = array();
		$filter = array();
		if (isset($this->request->get['filter_order_status'])) {
			$filterstr =  $this->request->get['filter_order_status'];
			if($filterstr=='Pending'){
				$filter['filter_order_status_id'] = 1;
			}elseif($filterstr=='processing'){
				$filter['filter_order_status_id'] = 2;
			}else if($filterstr=='Shipped'){
				$filter['filter_order_status_id'] = 3;
			}else if($filterstr=='OnHold'){
				$filter['filter_order_status_id'] = 18;
			}else if($filterstr=='Cancelled'){
				$filter['filter_order_status_id'] = 7;
			}
		}else if (isset($this->request->get['filter_order_status_id'])) {
				$filter['filter_order_status_id'] = $this->request->get['filter_order_status_id'];
		}
		if (isset($this->request->get['filter_customer'])) {
			$filter['filter_customer'] =  $this->request->get['filter_customer'];
			$data['filter_customer']=$filter['filter_customer'];
		}
		if (isset($this->request->get['filter_order_id'])) {
			$filter['filter_order_id'] =  $this->request->get['filter_order_id'];
			$data['filter_order_id']=$filter['filter_order_id'];
		}
		if (!$this->customer->isLogged()) {
			$this->session->data['redirect'] = $this->url->link('account/order', '', true);

			$this->response->redirect($this->url->link('account/login', '', true));
		}

		$this->load->language('commonipl/order');

		$this->document->setTitle($this->language->get('heading_title'));
		$this->document->setTabIndex(2);
		$url = '';

		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}
		

		if (isset($this->request->get['page'])) {
			$page = $this->request->get['page'];
		} else {
			$page = 1;
		}

		$data['orders'] = array();

		$this->load->model('commonipl/order');

		$order_total = $this->model_commonipl_order->geFiltertTotalOrders($filter);

		$results = $this->model_commonipl_order->getFilterOrders(($page - 1) * 10, 10,$filter);

        //print_r($results);
		foreach ($results as $result) {
			//echo $result['order_id'];
			$orderProducts = $this->model_commonipl_order->getOrderProducts($result['order_id']);
			//$total = isset($orderProducts[0])?$orderProducts[0]['total']:0;
			//print_r($orderProducts);
			$total = 0;
			$quantity = 0;
			$selfProduct = 0;
			$otherProduct =0;
			foreach($orderProducts as $orderProduct){
				$product = $this->model_commonipl_order->checkProduct($orderProduct['product_id']);
				if(isset($product['sku'])){
					  $selfProduct = 1;
				//print_r($orderProduct['price']);
					  $qty = isset($orderProduct)?$orderProduct['quantity']:0;
					  //print_r($qty);
					  $total += isset($orderProduct)?$orderProduct['price']*$qty:0;
					  $quantity += $qty;
				}else {
					$otherProduct = 1;
				}
			}
			
			$total = number_format($total,2);
			//print_r($total);
			$data['orders'][] = array(
				'order_id'   => $result['order_id'],
				'shopify_order_id'   => $result['forwarded_ip'],
				//'order_product_id' =>isset($$orderProduct)?$orderProduct['name']:'0',
				'name'       => isset($result['shipping_firstname'])?$result['shipping_firstname'] . ' ' . $result['shipping_lastname']:$result['firstname'] . ' ' . $result['lastname'],
				'status'     => $result['status'],
				'date_added' => date($this->language->get('date_format_short'), strtotime($result['date_added'])),
				//'products'   => isset($orderProduct)?$orderProduct['name']:'',
				'cancel' => 'cancel',
				'quantity'   => $quantity,
				'total'      => "$".$total,
				'product_status'=> ($selfProduct == 1?($otherProduct==1?2:1):0),
				'total_paid' => $total,
				'view'       => $this->url->link('commonipl/orders/info', 'order_id=' . $result['order_id'], true),
				'pay'       => $this->url->link('commonipl/orders/pay', 'order_id=' . $result['order_id'], true),
			);
			
		}

		
		$pagination = new Pagination();
		$pagination->total = $order_total;
		$pagination->page = $page;
		$pagination->limit = 10;
		$pagination->url = $this->url->link('commonipl/orders', 'page={page}', true);

		$data['pagination'] = $pagination->render();

		$data['results'] = sprintf($this->language->get('text_pagination'), ($order_total) ? (($page - 1) * 10) + 1 : 0, ((($page - 1) * 10) > ($order_total - 10)) ? $order_total : ((($page - 1) * 10) + 10), $order_total, ceil($order_total / 10));
		
		return $this->load->view('commonipl/order_list', $data);
	}

	public function info() {
		$this->load->language('commonipl/order');
		if (isset($this->request->get['order_id'])) {
			$order_id = $this->request->get['order_id'];
		} else {
			$order_id = 0;
		}

		if (!$this->customer->isLogged()) {
			$this->session->data['redirect'] = $this->url->link('commonipl/orders/info', 'order_id=' . $order_id, true);

			$this->response->redirect($this->url->link('account/login', '', true));
		}

		$this->load->model('commonipl/order');

		$order_info = $this->model_commonipl_order->getOrder($order_id);

		if ($order_info) {
			$this->document->setTitle($this->language->get('text_order'));
			$this->document->setTabIndex(2);
			$url = '';

			if (isset($this->request->get['page'])) {
				$url .= '&page=' . $this->request->get['page'];
			}
			$data['order_status_id'] = $order_info['order_status_id'];
			$data['sale_order_id'] = $order_info['forwarded_ip'];
			$data['breadcrumbs'] = array();

			$data['breadcrumbs'][] = array(
				'text' => $this->language->get('text_home'),
				'href' => $this->url->link('common/home')
			);

			$data['breadcrumbs'][] = array(
				'text' => $this->language->get('text_account'),
				'href' => $this->url->link('commonipl/account', '', true)
			);

			$data['breadcrumbs'][] = array(
				'text' => $this->language->get('heading_title'),
				'href' => $this->url->link('account/order', $url, true)
			);

			$data['breadcrumbs'][] = array(
				'text' => $this->language->get('text_order'),
				'href' => $this->url->link('commonipl/orders/info', 'order_id=' . $this->request->get['order_id'] . $url, true)
			);

			if (isset($this->session->data['error'])) {
				$data['error_warning'] = $this->session->data['error'];

				unset($this->session->data['error']);
			} else {
				$data['error_warning'] = '';
			}

			if (isset($this->session->data['success'])) {
				$data['success'] = $this->session->data['success'];

				unset($this->session->data['success']);
			} else {
				$data['success'] = '';
			}

			if ($order_info['invoice_no']) {
				$data['invoice_no'] = $order_info['invoice_prefix'] . $order_info['invoice_no'];
			} else {
				$data['invoice_no'] = '';
			}
			$this->load->model('localisation/order_status');

			$order_status = $this->model_localisation_order_status->getOrderStatus($order_info['order_status_id']);
			if(isset($order_status)){
				$data['order_status'] = $order_status['name'];
			}
		
			$data['order_id'] = $this->request->get['order_id'];
			$data['date_added'] = date($this->language->get('date_format_short'), strtotime($order_info['date_added']));

			if ($order_info['payment_address_format']) {
				$format = $order_info['payment_address_format'];
			} else {
				$format = '{firstname} {lastname}' . "\n" . '{company}' . "\n" . '{address_1}' . "\n" . '{address_2}' . "\n" . '{city} {postcode}' . "\n" . '{zone}' . "\n" . '{country}';
			}

			$find = array(
				'{firstname}',
				'{lastname}',
				'{company}',
				'{address_1}',
				'{address_2}',
				'{city}',
				'{postcode}',
				'{zone}',
				'{zone_code}',
				'{country}'
			);

			$replace = array(
				'firstname' => $order_info['payment_firstname'],
				'lastname'  => $order_info['payment_lastname'],
				'company'   => $order_info['payment_company'],
				'address_1' => $order_info['payment_address_1'],
				'address_2' => $order_info['payment_address_2'],
				'city'      => $order_info['payment_city'],
				'postcode'  => $order_info['payment_postcode'],
				'zone'      => $order_info['payment_zone'],
				'zone_code' => $order_info['payment_zone_code'],
				'country'   => $order_info['payment_country']
			);

			$data['payment_address'] = str_replace(array("\r\n", "\r", "\n"), '<br />', preg_replace(array("/\s\s+/", "/\r\r+/", "/\n\n+/"), '<br />', trim(str_replace($find, $replace, $format))));

			$data['payment_method'] = $order_info['payment_method'];

			if ($order_info['shipping_address_format']) {
				$format = $order_info['shipping_address_format'];
			} else {
				$format =  '{email}' . "\n" . '{telephone}' . "\n" .'{firstname} {lastname}' . "\n" . '{company}' . "\n" . '{address_1}' . "\n" . '{address_2}' . "\n" . '{city} {postcode}' . "\n" . '{zone}' . "\n" . '{country}';
			}

			$find = array(
				'{email}',
				'{telephone}',
				'{firstname}',
				'{lastname}',
				'{company}',
				'{address_1}',
				'{address_2}',
				'{city}',
				'{postcode}',
				'{zone}',
				'{zone_code}',
				'{country}'
			);
			
			$replace = array(
				'email' => $this->language->get('entry_email').': '.$order_info['email'],
				'telephone' => $this->language->get('entry_telephone').': '.$order_info['telephone'],
				'firstname' => $this->language->get('entry_name').': '.$order_info['shipping_firstname'],
				'lastname'  => $order_info['shipping_lastname'],
				'company'   => $this->language->get('entry_company').': '.$order_info['shipping_company'],
				'address_1' => $this->language->get('entry_address_1').': '.$order_info['shipping_address_1'],
				'address_2' => $this->language->get('entry_address_2').': '.$order_info['shipping_address_2'],
				'city'      => $this->language->get('entry_city').': '.$order_info['shipping_city'],
				'postcode'  => $order_info['shipping_postcode'],
				'zone'      => $order_info['shipping_zone'],
				'zone_code' => $order_info['shipping_zone_code'],
				'country'   => $this->language->get('entry_country').': '.$order_info['shipping_country']
			);


			$data['shipping_address'] = str_replace(array("\r\n", "\r", "\n"), '<br />', preg_replace(array("/\s\s+/", "/\r\r+/", "/\n\n+/"), '<br />', trim(str_replace($find, $replace, $format))));

			$data['shipping_method'] = $order_info['shipping_method'];

			$this->load->model('catalog/product');
			$this->load->model('tool/upload');

			// Products
			$data['products'] = array();
			$orderid = $this->request->get['order_id'];
			$this->load->model('tool/image');
			
			/*$order_product_id;
			if($order_info['customer_group_id']==4){
				
				$order_option_id = substr($order_info['invoice_prefix'],3);
			
				$opt = $this->model_commonipl_order->getProductSku($order_option_id);
				if(isset($opt[0])){
					//$orderid = $opt[0]['order_id'];
					$order_product_id = $opt[0]['order_product_id'];
				}
			}*/
            $products = $this->model_commonipl_order->getOrderProducts($this->request->get['order_id']);
			$subtotal=0;
			$tax=0;
			$shipping=0;
			$total=0;
			$saletotal=0;
			foreach ($products as $product) {
				
				$option_select = array();
				$option_select_name = array();
				$productoption = $this->model_catalog_product->getProductOptions($product['product_id']);
				foreach ($productoption as $opt) {
					//print_r($opt);
					if($opt['type']=='select' && $opt['product_option_value'][0]['image']==''){
						$option_select = $opt['product_option_value'];
						$option_select_name = $opt['name'];
					}
				}
				$option_data = array();
				//print_r($product['shopify_sku'].'-id='.$this->request->get['order_id']);

				$options = $this->model_commonipl_order->getProductSku($product['shopify_sku']);
				//print_r( $options);
				$image='';
				$model ='';
				$selected_name='';
				//print_r($product['shopify_sku']);
				if(!empty($options)){
				if (isset($options['variants_image'])) {
					$image = $this->model_tool_image->resize($options['variants_image'], $this->config->get('theme_' . $this->config->get('config_theme') . '_image_product_width'), $this->config->get('theme_' . $this->config->get('config_theme') . '_image_product_height'));
				} else {
					$image = $this->model_tool_image->resize('placeholder.png', $this->config->get('theme_' . $this->config->get('config_theme') . '_image_product_width'), $this->config->get('theme_' . $this->config->get('config_theme') . '_image_product_height'));
				}

				}/*else{
					$option_data[] = array(
							'value' => 'SKU:'.$product['shopify_sku']
						);
				}*/
				$option_data[] = array(
							'value' => 'SKU:'.$product['shopify_sku']
				);
				if(!empty($product['options'])){
					$selected_name = $product['options'];
				}
				
				$product_info = $this->model_catalog_product->getProduct($product['product_id']);

				if ($product_info) {
					$reorder = $this->url->link('account/order/reorder', 'order_id=' . $order_id . '&order_product_id=' . $product['order_product_id'], true);
				} else {
					$reorder = '';
				}

				$data['products'][] = array(
					'name'     => $product['name'],
					'model'    => $product_info['model'],
					'option'   => $option_data,
					'option_select'   => $option_select,
					'selected_name'    => trim($selected_name),
					'quantity' => $product['quantity'],
					'design_file'    => $image,
					'price'    => number_format($product['price'],2),
					'sale_total'    => number_format($product['shopify_price']*$product['quantity'],2),
					'total'    => number_format($product['price']*$product['quantity'],2),
					'order_product_id'    => $product['order_product_id'],
					'reorder'  => $reorder,
					'return'   => $this->url->link('account/return/add', 'order_id=' . $order_info['order_id'] . '&product_id=' . $product['product_id'], true)
				);
				$subtotal+= $product['price']*$product['quantity'];
				$tax+= $product['tax'];
				$saletotal+= $product['shopify_price']*$product['quantity'];
				
			}
			$data['haspay'] = $subtotal==0?1:($data['order_status_id']!=1&&$data['order_status_id']!=18?1:0);
			$data['totals'] = array();
			$data['totals'][] = array(
					'title' => "Sub-Total:",
					'text'  => number_format($subtotal,2),
					'text2'  => number_format($saletotal,2)
				);
				
				$data['totals'][] = array(
					'title' => "tax:",
					'text'  => number_format($tax,2),
					'text2'  => 0
				);
				$data['totals'][] = array(
					'title' => "Total:",
					'text'  => number_format($subtotal+$tax,2),
					'text2'  => ''
				);
			// Voucher
			$data['vouchers'] = array();

			$vouchers = $this->model_commonipl_order->getOrderVouchers($orderid);

			foreach ($vouchers as $voucher) {
				$data['vouchers'][] = array(
					'description' => $voucher['description'],
					'amount'      => $this->currency->format($voucher['amount'], $order_info['currency_code'], $order_info['currency_value'])
				);
			}

			// Totals
			

			

			$data['comment'] = nl2br($order_info['comment']);

			// History
			$data['histories'] = array();

			$results = $this->model_commonipl_order->getOrderHistories($orderid);
			
			$shipping_method='';

			foreach ($results as $result) {
				if(empty($shipping_method)&&!empty($result['shopping_method'])){
					$shipping_method = $result['shopping_method']." : <a href='https://www.aftership.com/' target='_blank'>".$result['shopping_number']."</a>";
				}
				$data['histories'][] = array(
					'date_added' => date($this->language->get('date_format_short'), strtotime($result['date_added'])),
					'status'     => $result['status'],
					'comment'    => $result['notify'] ? nl2br($result['comment']) : ''
				);
			}

			$data['shipping_method'] = $shipping_method;

			$data['continue'] = $this->url->link('commonipl/orders', '', true);
			
			$data['paid'] = $this->url->link('commonipl/orders/pay', 'order_id=' . $orderid . $url, true);
			
			//$data['status'] = $this->request->get['status'];
			$data['footer'] = $this->load->controller('common/footer');
			$data['header'] = $this->load->controller('common/header');

			$this->response->setOutput($this->load->view('commonipl/order_info', $data));
		} else {
			return new Action('error/not_found');
		}
	}
	
	
	public function onhod(){
		if (isset($this->request->get['order_id'])) {
			$order_id = $this->request->get['order_id'];
			$this->load->model('commonipl/order');
			$status = $this->model_commonipl_order->getOrderStatusByName('On-Hold');
		    $this->model_commonipl_order->updateOrderStatus($order_id,$status['order_status_id']);
			$json = array();
			$json['status'] = $status['name'];
			$json['success'] ='true';
			$this->response->addHeader('Content-Type: application/json');
			$this->response->setOutput(json_encode($json));
			//$this->response->redirect($this->url->link('commonipl/orders', '', true));
		}
	
	}
	
	public function cancel(){
		if (isset($this->request->get['order_id'])) {
			$order_id = $this->request->get['order_id'];
			$this->load->model('commonipl/order');
			$status = $this->model_commonipl_order->getOrderStatusByName('Canceled');
		    $this->model_commonipl_order->updateOrderStatus($order_id,$status['order_status_id']);
			$json = array();
			$json['status'] = $status['name'];
			$json['success'] ='true';
			$this->response->addHeader('Content-Type: application/json');
			$this->response->setOutput(json_encode($json));
			//$this->response->redirect($this->url->link('commonipl/orders', '', true));
		}
	
	}
	
	public function update(){
		if (isset($this->request->post['order_product_id'])) {
			$order_product_id = $this->request->post['order_product_id'];
			$quantity = $this->request->post['quantity'];
			$size_name = $this->request->post['size_name'];
			$design_file = $this->request->post['design_file'];
			$this->load->model('commonipl/order');
			
		    $order_info = $this->model_commonipl_order->updateOrderProduct($order_product_id,$quantity,$size_name,$design_file);
			$json['success'] ='1';
			$this->response->addHeader('Content-Type: application/json');
			$this->response->setOutput(json_encode($json));
		}
	
	}
	
	public function pay(){
		//$this->response->redirect($this->url->link('extension/payment/pp_express/spcheckout', '', true));
		/*echo '<script language="javascript">window.alert("123");</script>';*/
		if (!isset($this->request->get['order_id'])) {
			$json = array();
			if(isset($this->request->post['order_id'])){
				$this->session->data['order_id'] = explode(",", $this->request->post['order_id']);
				$json['success'] ='1';
			}
			$this->response->addHeader('Content-Type: application/json');
			$this->response->setOutput(json_encode($json));
		}else {
			$this->session->data['order_id'] = array($this->request->get['order_id']);
			$this->response->redirect($this->url->link('extension/payment/pp_express/checkout', '', true));
			//$this->response->redirect($this->url->link('extension/payment/twocheckout', '', true));
		}
			
			
	}

	public function reorder() {
		$this->load->language('account/order');

		if (isset($this->request->get['order_id'])) {
			$order_id = $this->request->get['order_id'];
		} else {
			$order_id = 0;
		}

		$this->load->model('commonipl/order');

		$order_info = $this->model_commonipl_order->getOrder($order_id);

		if ($order_info) {
			if (isset($this->request->get['order_product_id'])) {
				$order_product_id = $this->request->get['order_product_id'];
			} else {
				$order_product_id = 0;
			}

			$order_product_info = $this->model_commonipl_order->getOrderProduct($order_id, $order_product_id);

			if ($order_product_info) {
				$this->load->model('catalog/product');

				$product_info = $this->model_catalog_product->getProduct($order_product_info['product_id']);

				if ($product_info) {
					$option_data = array();

					$order_options = $this->model_commonipl_order->getOrderOptions($order_product_info['order_id'], $order_product_id);

					foreach ($order_options as $order_option) {
						if ($order_option['type'] == 'select' || $order_option['type'] == 'radio' || $order_option['type'] == 'image') {
							$option_data[$order_option['product_option_id']] = $order_option['product_option_value_id'];
						} elseif ($order_option['type'] == 'checkbox') {
							$option_data[$order_option['product_option_id']][] = $order_option['product_option_value_id'];
						} elseif ($order_option['type'] == 'text' || $order_option['type'] == 'textarea' || $order_option['type'] == 'date' || $order_option['type'] == 'datetime' || $order_option['type'] == 'time') {
							$option_data[$order_option['product_option_id']] = $order_option['value'];
						} elseif ($order_option['type'] == 'file') {
							$option_data[$order_option['product_option_id']] = $this->encryption->encrypt($this->config->get('config_encryption'), $order_option['value']);
						}
					}

					$this->cart->add($order_product_info['product_id'], $order_product_info['quantity'], $option_data);

					$this->session->data['success'] = sprintf($this->language->get('text_success'), $this->url->link('product/product', 'product_id=' . $product_info['product_id']), $product_info['name'], $this->url->link('checkout/cart'));

					unset($this->session->data['shipping_method']);
					unset($this->session->data['shipping_methods']);
					unset($this->session->data['payment_method']);
					unset($this->session->data['payment_methods']);
				} else {
					$this->session->data['error'] = sprintf($this->language->get('error_reorder'), $order_product_info['name']);
				}
			}
		}

		$this->response->redirect($this->url->link('commonipl/orders/info', 'order_id=' . $order_id));
	}
}
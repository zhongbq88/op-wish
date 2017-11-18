<?php

//include('woocommerce.php');

class ControllerCommoniplDashboard extends Controller{

	public function index(){
		/*if (!$this->customer->isLogged()) {
			$this->response->redirect($this->url->link('common/connect', '', true));
		}*/
		//print_r($this->session->data['home']);
		if($this->load->controller('shopify/oauth/checkChargeApp')==false){
			die('<script> top.location.href="https://'.$this->session->data['shop'].'/admin/apps"</script>');
			return;
		}
		
		echo '<script language="JavaScript">;alert("卸载了";location.href="www.jbxue.com";</script>;';
		return;
		
		
		if(isset($this->session->data['srcImages'])){
			unset($this->session->data['srcImages']);
		}
		if(isset($this->session->data['home'])){
			unset($this->session->data['home']);
		}
		/*$status = $this->request->get['success'];
		if($status!=1){
			unset($this->session->data['shop']);
		}
		$user_id =  $this->request->get['user_id'];*/

		$this->load->language('commonipl/dashboard');

		$this->document->setTitle($this->language->get('heading_title'));
		
		$data = array();

		$this->load->model('commonipl/order');

		$results = $this->model_commonipl_order->getStatusTotalOrders();
 		$this->load->model('localisation/order_status');
		$order_statuses = $this->model_localisation_order_status->getOrderStatuses();
		$pending =0;
		$processing =0;
		$shipped =0;
		$on_hold =0;
		$cancelled =0;
		$total =0;
		$charges =0;
		$orderIds ='';
		foreach ($results as $result) {
			
			if($result['order_status_id']==$this->getStatus($order_statuses,'To-Order')){
				$pending+=1;
			}else if($result['order_status_id']==$this->getStatus($order_statuses,'In-Processing')){
				$processing+=1;
			}else if($result['order_status_id']==$this->getStatus($order_statuses,'Shipped')){
				$shipped+=1;
			}else if($result['order_status_id']==$this->getStatus($order_statuses,'On-Hold')){
				$on_hold+=1;
			}else if($result['order_status_id']==$this->getStatus($order_statuses,'Refunded')){
				$cancelled+=1;
			}
			if($result['order_status_id']==$this->getStatus($order_statuses,'In-Processing') || $result['order_status_id']==$this->getStatus($order_statuses,'Chargeback') || $result['order_status_id']==$this->getStatus($order_statuses,'Complete')|| $result['order_status_id']==$this->getStatus($order_statuses,'Shipped')){
				$orderIds .=",'".$result['order_id']."'";
				//$total+=(float)$result['total'];
				//$charges+=(float)$result['total'];
			}
		}
		//echo $orderIds;
		
		if(!empty($orderIds)){
			//echo '----'.substr($orderIds,1);
			$results = $this->model_commonipl_order->getOrderProductSales(substr($orderIds,1));
			foreach ($results as $result) {
				$total+=(float)$result['shopify_price']*(float)$result['quantity'];
				$charges+=(float)$result['price']*(float)$result['quantity'];
			}
		}
		
		$data['pending'] = $pending; 
		$data['processing'] = $processing; 
		$data['shipped'] = $shipped; 
		$data['on_hold'] = $on_hold; 
		$data['cancelled'] = $cancelled; 
		$data['aug_total'] = $total; 
		$data['aug_charges'] = $charges; 
		$data['tabtype'] = 0;
		
		$this->load->language('commonipl/product');
		$this->load->model('commonipl/product');
		$this->load->model('tool/image');
		$products = $this->model_commonipl_product->getPublishProduct();
		$productList = array();
		//$productIds ='';
		foreach($products as $product){
			//$productIds .=",'".$product['shopify_product_id']."'";
			$p = json_decode($product['shopify_product_json'],true);
			//print_r($p['id']);
			if(empty($p['id'])||$p['id']==0){
				 if (isset($p['images'])) {
					$image =$p['images'][0]['src'] ;
				} else {
					$image = $this->model_tool_image->resize('placeholder.png', $this->config->get('theme_' . $this->config->get('config_theme') . '_image_product_width'), $this->config->get('theme_' . $this->config->get('config_theme') . '_image_product_height'));
				}
				$sales = $this->model_commonipl_product->getPublishProductSales($product['add_product_id']);
				$productList[] = array(
					'name'=>$p['title'],
					'image'=>$image,
					'status'=>'published',
					'published_at'=>date($this->language->get('date_format_short'), strtotime($product['date_added'])),
					'sales'=>$sales,
					'view'  => 'index.php?route=store/product&product_id='.$product['add_product_id']
				);
			}else if(isset($p['title'])){
				if (isset($p['image'])) {
					$image =$p['image']['src'] ;
				} else {
					$image = $this->model_tool_image->resize('placeholder.png', $this->config->get('theme_' . $this->config->get('config_theme') . '_image_product_width'), $this->config->get('theme_' . $this->config->get('config_theme') . '_image_product_height'));
				}
				$sales = $this->model_commonipl_product->getPublishProductSales($p['id']);
				$productList[] = array(
					'name'=>$p['title'],
					'image'=>$image,
					'status'=>'published',
					'published_at'=>date($this->language->get('date_format_short'), strtotime($product['date_added'])),
					'sales'=>$sales,
					'href'  => 'https://'.$this->session->data['shop'].'/admin/products/'.$p['id']
				);
			}else if(isset($p['name'])){
				
				if ($p['images']) {
					$image =$p['images'][0]['src'] ;
				} else {
					$image = $this->model_tool_image->resize('placeholder.png', $this->config->get('theme_' . $this->config->get('config_theme') . '_image_product_width'), $this->config->get('theme_' . $this->config->get('config_theme') . '_image_product_height'));
				}
				$sales = $this->model_commonipl_product->getPublishProductSales($p['id']);
				$productList[] = array(
					'name'=>$p['name'],
					'image'=>$image,
					'status'=>$p['status'],
					'published_at'=>date($this->language->get('date_format_short'), strtotime($product['date_added'])),
					'sales'=>$sales,
					'href'  => 'https://'.$this->session->data['shop'].'/admin/products/'.$p['id']
				);
			}
		}
		//print_r($productList);
		$data['products'] = $productList;
		$this->load->model('setting/module');
		$setting_info = $this->model_setting_module->getModule('27');

		if ($setting_info && $setting_info['status']) {
			$output = $this->load->controller('extension/module/banner', $setting_info);
			if (isset($output)) {
				 $data['content_top'] = $output;
			}
		}
		
		$data['footer'] = $this->load->controller('common/footer');
		$data['header'] = $this->load->controller('common/header');
		if(isset($this->session->data['sussecc'])){
			$data['success'] = $this->session->data['sussecc'];
			unset($this->session->data['sussecc']);
		}
		$this->response->setOutput($this->load->view('commonipl/dashboard', $data));
	}
	
	public function getStatus($order_statuses,$key){
		foreach($order_statuses as $status){
			if($status['name']==$key){
				return $status['order_status_id'];
			}
		}
		return 1;
	}
	
}

?>

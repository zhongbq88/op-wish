<?php

require 'vendor/autoload.php';
use phpish\shopify;

require 'conf.php';
	
class ControllerShopifyDashboard extends Controller {

	public function index(){
		
		
		if(empty($this->session->data['shop'])||empty($this->customer->getId())){
			$this->getToken();
		}
		if (!$this->customer->isLogged()) {
			$this->session->data['redirect'] = $this->url->link('shopify/dashboard', '', true);
			$this->response->redirect($this->url->link('account/login', '', true));
		}

		$this->load->language('shopify/dashboard');

		$this->document->setTitle($this->language->get('heading_title'));
		
		$data = array();

		$this->load->model('shopify/order');

		$results = $this->model_shopify_order->getStatusTotalOrders();

		$pending =0;
		$in_production =0;
		$shipped =0;
		$on_hold =0;
		$cancelled =0;
		$total =0;
		$charges =0;
		foreach ($results as $result) {
			
			if($result['order_status_id']==1){
				$pending+=1;
			}else if($result['order_status_id']==17){
				$in_production+=1;
			}else if($result['order_status_id']==3){
				$shipped+=1;
			}else if($result['order_status_id']==18){
				$on_hold+=1;
			}else if($result['order_status_id']==7 || $result['order_status_id']==11||$result['order_status_id']==10||$result['order_status_id']==14){
				$cancelled+=1;
			}
			if($result['order_status_id']==17 || $result['order_status_id']==13 || $result['order_status_id']==17|| $result['order_status_id']==5|| $result['order_status_id']==3){
				$total+=(float)$result['total'];
				$charges+=(float)$result['total'];
			}
		}
		$data['pending'] = $pending; 
		$data['in_production'] = $in_production; 
		$data['shipped'] = $shipped; 
		$data['on_hold'] = $on_hold; 
		$data['cancelled'] = $cancelled; 
		$data['aug_total'] = $total; 
		$data['aug_charges'] = $charges; 
		$data['tabtype'] = 0;
		$data['footer'] = $this->load->controller('shopify/footer');
		$data['header'] = $this->load->controller('shopify/header');
		if(isset($this->session->data['sussecc'])){
			$data['success'] = $this->session->data['sussecc'];
			unset($this->session->data['sussecc']);
		}
		$this->response->setOutput($this->load->view('shopify/dashboard', $data));
		
		
		}
		
	public function getToken(){
		if(!empty($this->customer->getId())&&(!isset($_GET['shop'])||!isset($_GET['code']))){
			if ($this->customer->isLogged()) {
				$this->session->data['oauth_token'] = $this->customer->getToken();
				$this->session->data['shop'] = $this->customer->getFirstName().".myshopify.com";
			}
			return;
		}
		try
		{
			//echo 'shop='.$_GET['shop'];
			//echo 'code='.$_GET['code'];
			# shopify\access_token can throw an exception
			$oauth_token = shopify\access_token($_GET['shop'], SHOPIFY_APP_API_KEY, SHOPIFY_APP_SHARED_SECRET, $_GET['code']);
			
			//echo $oauth_token;
			$this->load->model('account/customer');
			$shop = $_GET['shop'];
			$shops = explode(".", $shop);
			$email = $shops[0]."@shopify.com";
			$customer = $this->model_account_customer->getCustomerByEmail($email);
			if(empty($customer)){
				$customer_id = $this->model_account_customer->addShopifyUser($shop,$oauth_token);
			}else{
				$this->model_account_customer->updateShopifyToken($customer['customer_id'],$oauth_token);
			}		
			$this->customer->login($email, $shop);
			$this->session->data['oauth_token'] = $oauth_token;
			$this->session->data['shop'] = $_GET['shop'];
			$this->load->controller('shopify/loadorders');
			return $shop;
			//echo 'App Successfully Installed!';
		}
		catch (shopify\ApiException $e)
		{
			# HTTP status code was >= 400 or response contained the key 'errors'
			//echo $e;
			print_R($e->getRequest());
			print_R($e->getResponse());
			
		}
		catch (shopify\CurlException $e)
		{
			# cURL error
			//echo $e;
			print_R($e->getRequest());
			print_R($e->getResponse());
			
		}
		}
	
	}

?>
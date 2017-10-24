<?php

require 'vendor/autoload.php';
use phpish\shopify;

class ControllerShopifyConnect extends Controller {
	
	public function index(){
		$this->session->data['store'] = 'shopify';
		if(empty($this->session->data['shop'])||empty($this->customer->getId())){
			$this->getToken();
		}
		if (!$this->customer->isLogged()) {
			$this->session->data['redirect'] = $this->url->link('commonipl/dashboard', '', true);
			$this->response->redirect($this->url->link('account/login', '', true));
		}
		$this->response->redirect($this->url->link('commonipl/dashboard', '', true));
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
			$this->session->data['store'] = 'shopify';
			//$this->load->controller('shopify/loadorders');
			return $shop;
			//echo 'App Successfully Installed!';
		}
		catch (shopify\ApiException $e)
		{
			# HTTP status code was >= 400 or response contained the key 'errors'
			//echo $e;
			//print_R($e->getRequest());
			//print_R($e->getResponse());
			
		}
		catch (shopify\CurlException $e)
		{
			# cURL error
			//echo $e;
			//print_R($e->getRequest());
			//print_R($e->getResponse());
			
		}
	}
	
}
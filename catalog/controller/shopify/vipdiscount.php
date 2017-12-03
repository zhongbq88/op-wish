<?php

require 'vendor/autoload.php';
use phpish\shopify;

class ControllerShopifyVipdiscount extends Controller {
	
	public function index(){
		
		$this->load->model("account/customer_group");
		$customerGroup = $this->model_account_customer_group->getCustomerGroupCharge();
		
		if(isset($customerGroup['config_charging'])&&!empty($customerGroup['config_charging'])){
			$this->chargeApp(json_decode($customerGroup['config_charging'],true),$customerGroup['customer_group_id']);
		}else{
			$this->response->redirect($this->url->link('commonipl/category&shopify=true', '', true));
		}
		
	}
	
	public function chargeApp($charging,$customer_group_id){
		
		try
		
		{

			$url = ($charging['charge_type']==1)?'':'recurring_';
			$shopify = shopify\client($this->session->data['shop'], SHOPIFY_APP_API_KEY,$this->session->data['oauth_token']);
			if(isset($charging['charge_id'])){
				$result =  $shopify('GET /admin/'.$url.'application_charges'.$charging['charge_id'].'.json');
			//print_r($this->session->data['install']);
				if(isset($result)){
					
					foreach($result as $charge){
						if($charge['status']=='accepted'){
							$this->model_account_customer_group->updateCustomer($customer_group_id,$this->customer->getId());
							$this->response->redirect($this->url->link('commonipl/category&shopify=true', '', true));
							
						}else{
							if($charge['status']=='pending'){
								$confirmation_url = $charge['confirmation_url'];
								die("<script> top.location.href='".$confirmation_url."'</script>");
							}
						}
						break;
					}
				}
			}
			
			$data = array(
					"name"=>$charging['name'],
					"price"=> $charging['price'],
					"return_url"=> isset($charging['retrun_url'])?$charging['retrun_url']:'https://127.0.0.1/index.php?route=commonipl/dashboard'
			);
			if($charging['sendbox']==1){
				$data["test"]=true;
			}
			if($charging['trial_days']>0){
				$data["trial_days"]=$charging['trial_days'];
			}
			//print_r($data);
			$result =  $shopify('POST /admin/'.$url.'application_charges.json', array(), array($url.'application_charge'=>$data));
			if(isset($result['id'])){
				$charging['charge_id'] = $result['id'];
				$this->model_account_customer_group->updateCustomerGroupCharge($customer_group_id,$charging);
			}
			$confirmation_url = $result['confirmation_url'];
			die("<script> top.location.href='".$confirmation_url."'</script>");
			//print_r($get);
			/*
			print_r($result);
			$permission_url = $result['confirmation_url'];
			die("<script> top.location.href='$permission_url'</script>");*/
			//echo 'App Successfully Installed!';
		}
		catch (shopify\ApiException $e)
		{
			# HTTP status code was >= 400 or response contained the key 'errors'
			//echo $e;
			print_r($e->getRequest());
			print_r($e->getResponse());
			
		}
		catch (shopify\CurlException $e)
		{
			# cURL error
			//echo $e;
			print_r($e->getRequest());
			print_r($e->getResponse());
			
		}
		$this->response->redirect($this->url->link('commonipl/category&shopify=true', '', true));
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
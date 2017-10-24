<?php
use phpish\shopify;

class ControllerCommonConnect extends Controller {
	
	public function index(){
		$data['footer'] = $this->load->controller('common/footer');
		$data['header'] = $this->load->controller('common/header');
		$this->response->setOutput($this->load->view('common/connect', $data));
	}
	
	public function install(){
		$shop = $this->request->post['store'];
		if(!isset($shop)){
			return;
		}
		//print_r( $shop);
		$shops = explode(".", $shop);
		$email = $shops[0]."@shopify.com";
		$this->load->model('account/customer');
		$customer = $this->model_account_customer->getCustomerByEmail($email);
		if(isset($customer)&&isset($customer['token'])){
			try
		{
			require(str_replace('common','',__DIR__).'shopify/vendor/autoload.php');
			$shopify = shopify\client($shop, SHOPIFY_APP_API_KEY, $customer['token']);
			$shopInfo = $shopify('GET /admin/shop.json');
			//print_r($shopInfo);
			if(!isset($shopInfo['error'])){
				//print_r($email);
				$this->customer->login($email, $shop);
				$this->session->data['oauth_token'] = $this->customer->getToken();
				$this->session->data['shop'] = $shop;
				$this->session->data['store'] = 'shopify';
				$url = $this->url->link('commonipl/dashboard', '');
				$this->response->redirect($url);
				return;
			}
		    
			//return $shop;
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
		$url = $this->url->link('shopify/install', 'shop='. $shop);
		$this->response->redirect($url);
	}
	
}
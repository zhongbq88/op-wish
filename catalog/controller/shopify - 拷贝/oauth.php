<?php

require __DIR__.'/vendor/autoload.php';
use phpish\shopify;

require __DIR__.'/conf.php';

class ControllerShopifyOauth extends Controller {
	
	public function index(){

		$shop = $_GET['shop'];
		$shops = explode(".", $shop);
		$email = $shops[0]."@shopify.com";
		$this->load->model('account/customer');
		$customer = $this->model_account_customer->getCustomerByEmail($email);
		if (!isset($_GET['code']) && empty($customer))
		{
			$permission_url = shopify\authorization_url($_GET['shop'], SHOPIFY_APP_API_KEY, array('read_products', 'read_customers', 'write_products','read_orders', 'write_orders', 'read_fulfillments', 'write_fulfillments'),REDIRECTION_URL);
			die("<script> top.location.href='$permission_url'</script>");
		}else{
			$this->customer->login($email, $shop);
			$this->session->data['oauth_token'] = $this->customer->getToken();
			$this->session->data['shop'] = $shop;
			$this->response->redirect($this->url->link('shopify/dashboard', '', true));
		}
	}
}

?>
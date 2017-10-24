<?php

session_start();
require __DIR__.'/vendor/autoload.php';
use phpish\shopify;

require __DIR__.'/conf.php';

class ControllerShopifyOrderedit extends Controller {
	public function index(){
		/*$shopify = shopify\client($_SESSION['shop'], SHOPIFY_APP_API_KEY, $_SESSION['oauth_token']);

	try
	{
		//print_r();
		echo print_r($_SESSION['product']);
		# Making an API request can throw an exception
		$product = $shopify('POST /admin/products.json', array(), array('product' => $_SESSION['product']));
		print_r($product);
		//return true;
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
	//return false;
		$this->response->redirect($this->url->link('shopify/dashboard', 'token=' . $this->session->data['token'] . $url, 'SSL'));*/
	}
	
	public function cancel(){
		$shopify = shopify\client($_SESSION['shop'], SHOPIFY_APP_API_KEY, $_SESSION['oauth_token']);
		try
		{
			//print_r();
			//echo print_r($this->session->data['id']);
			# Making an API request can throw an exception
			$respone = $shopify('POST /admin/orders/'+$this->session->data['id']+'/cancel.json', array());
			print_r($respone);
			//return true;
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
		//return false;
		$this->response->redirect($this->url->link('shopify/dashboard', 'token=' . $this->session->data['token'] . $url, 'SSL'));
	}
}

?>
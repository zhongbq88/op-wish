<?php

require __DIR__.'/vendor/autoload.php';
use phpish\shopify;

class ControllerShopifyOauth extends Controller {
	
	public function index(){
		
		
		
		
		$this->session->data['store'] = 'shopify';
		$shop = $_GET['shop'];
		$shops = explode(".", $shop);
		$email = $shops[0]."@shopify.com";
		$this->load->model('account/customer');
		$customer = $this->model_account_customer->getCustomerByEmail($email);
		
		//echo $shop;
		if (isset($this->session->data['install']) || !isset($_GET['code']) && empty($customer))
		{
			unset($this->session->data['install']);
			$this->oauth();
		}else{
			try
			{
				$shopify = shopify\client($shop, SHOPIFY_APP_API_KEY, $customer['token']);
				$shopInfo = $shopify('GET /admin/shop.json');
				if(!isset($shopInfo['error'])){
					$this->customer->login($email, $shop);
					$this->session->data['shop'] = $shop;
					$this->session->data['oauth_token'] = $this->customer->getToken();
			        $this->response->redirect($this->url->link('shopify/connect', '', true));
					return;
				}
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
			
			$this->oauth();
		}
	}
	
	public function oauth(){
		if(isset($this->session->data['shop'])){
			unset($this->session->data['shop']);
		}
		$permission_url = shopify\authorization_url($_GET['shop'], SHOPIFY_APP_API_KEY, array('read_products',/* 'read_customers',*/ 'write_products','read_product_listings','read_orders', 'write_orders', 'read_fulfillments', 'write_fulfillments'),REDIRECTION_URL);
		//echo $permission_url;
		die("<script> top.location.href='$permission_url'</script>");
	}
	
	public function checkChargeApp(){
		try
		{
			
			if(isset($this->request->get['charge_id'])){
				$charge_id = $this->request->get['charge_id'];
				$charging = $this->config->get('config_charging');
				//print_r($charging);
				//return;
				$url = ($charging['charge_type']==1)?'':'recurring_';
				$shopify = shopify\client($this->session->data['shop'], SHOPIFY_APP_API_KEY,$this->session->data['oauth_token']);
				$result =  $shopify('GET /admin/'.$url.'application_charges/'.$charge_id.'.json');
				//print_r($result);
				if(isset($result['status'])&&$result['status']=='declined'){
					/*$this->load->model('commonipl/product');
					$products = $this->model_commonipl_product->getPublishDeleteProductId();
					//print_r($result);
					foreach($products as $product){
						$this->deleteProduct($shopify,$product['shopify_product_id']);
					}*/
					$return_url= explode('?',$result['return_url']);
					header('Location:'.$return_url[0].'?declined=true'); 
					$this->deteleApp();
					return false;
				}
			}
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
		return true;
	}
	
	private function deteleApp(){
		$access_token = $this->session->data['oauth_token'];
 		$revoke_url   = "https://".$this->session->data['shop']."/admin/api_permissions/current.json";

		$headers = array("Content-Type: application/json","Accept: application/json","Content-Length: 0","X-Shopify-Access-Token: " . $access_token);
	  
		$handler = curl_init($revoke_url);
		curl_setopt($handler, CURLOPT_CUSTOMREQUEST, "DELETE");
		curl_setopt($handler, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($handler, CURLOPT_HTTPHEADER, $headers);
	  
		curl_exec($handler);
		$result = false;
		if(!curl_errno($handler))
		{
		  $info = curl_getinfo($handler);
		  // $info['http_code'] == 200 for success
		  //print_r($info);
		  $result =$info['http_code'] == 200;
		}
	  
		curl_close($handler);
		
		return $result;
	}
	
	public function deleteProduct($shopify,$product_id){
		try
		{
			
			$result =  $shopify('DELETE /admin/products/'.$product_id.'.json');
			print_r($result);
				
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
	}
}

?>

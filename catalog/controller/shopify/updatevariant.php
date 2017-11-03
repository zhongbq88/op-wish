<?php

require __DIR__.'/vendor/autoload.php';
use phpish\shopify;

class ControllerShopifyUpdatevariant extends Controller {
	
	public function index(){
		header('Content-Type: text/html; charset=UTF-8');
		$datas = file_get_contents('php://input');
		$datas = json_decode($datas, true);
		//print_r($data);
	  	$this->load->model('account/customer');
		if(isset($datas)){
			foreach($datas as $key=> $data){
				$customer_info = $this->model_account_customer->getCustomer($key);	
				  //print_r($customer_info);
				if(isset($customer_info['token'])&&$customer_info['lastname']=='myshopify'){
					  $this->updateVariant($customer_info['firstname'].'.myshopify.com',$customer_info['token'],$data);
				}
		  		
			}
		}
		  
		  
		  
		
	}
	
	public function updateVariant($shopify,$outh_token,$variants){
		  //print_r($shopify.'--'.$outh_token);
		  $shopify = shopify\client($shopify, SHOPIFY_APP_API_KEY, $outh_token);
		  try
		  {
			  foreach($variants as $variant){
				   $result = $shopify('PUT /admin/variants/'.$variant['id'].'.json',array(),array('variant'=>$variant));
				   //print_r($result);
			  }
		  }
		  catch (shopify\ApiException $e)
		  {
			  	print_r($e->getRequest());
				print_r($e->getResponse());
		  }
		  catch (shopify\CurlException $e)
		  {
			  	print_r($e->getRequest());
				print_r($e->getResponse());
		  }
	}
	
	
}

?>
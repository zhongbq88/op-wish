<?php 

require __DIR__.'/vendor/autoload.php';
use phpish\shopify;
require __DIR__.'/conf.php';

class ControllerShopifyUpdateorder extends Controller {

public function index(){
	
	$this->load->model('shopify/order');
	$customer_id = $this->request->post['customer_id'];
	$customer = $this->model_shopify_order->getCustomToken($customer_id);
	if(!isset($customer['token'])){
		return;
	}
 	///$shopify = shopify\client('vivajean.myshopify.com', SHOPIFY_APP_API_KEY, '11e33008c194c293845cbc7eb93a9d8d');
	$shopify = shopify\client($customer['firstname'].'.myshopify.com', SHOPIFY_APP_API_KEY, $customer['token']);
	$json = array();
	try
	{
		$order_id = $this->request->post['order_id'];
		$tracking_company =  $this->request->post['tracking_company'];
		$tracking_number =  $this->request->post['tracking_number'];
		
		$line_items = array();
		$LineItemId = $this->model_shopify_order->getOrderProductLineItemId($order_id);
		$line_item_order_id;
		if(!isset($LineItemId )){
			return;
		}
		foreach($LineItemId as $item){
			$line_items[] = array('id'=>$item['line_item_id']);
			$line_item_order_id = $item['line_item_order_id'];
		}
		$fulfillments = array(
    		"tracking_company"=> $tracking_company,
    		"tracking_number"=> $tracking_number,
    		"tracking_url"=> "https://www.aftership.com/",
			'line_items'=>$line_items
		);
		//$update = array('order'=>array("id"=>5238292616,'fulfillments' =>$fulfillments));
		//print_r($fulfillments);
		$product = $shopify('POST /admin/orders/'.$line_item_order_id.'/fulfillments.json', array(),array('fulfillment'=>$fulfillments));
		//print_r($product);
	}
	catch (shopify\ApiException $e)
	{
		# HTTP status code was >= 400 or response contained the key 'errors'
		echo $e;
		print_r($e->getRequest());
		print_r($e->getResponse());
	}
	catch (shopify\CurlException $e)
	{
		# cURL error
		echo $e;
		print_r($e->getRequest());
		print_r($e->getResponse());
	}
	$json['success'] ='success';
	$this->response->addHeader('Content-Type: application/json');
	$this->response->setOutput(json_encode($json));
}
}
?>
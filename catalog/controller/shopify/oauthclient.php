<?php

require 'vendor/autoload.php';
use phpish\shopify;

class Oauthclient {
	
    private static $instances = array();
    private $client;
	private $store;
	private $oauth_token;
	
	
	
    private function __construct($store,$consumerkey,$consumerSecret,$oauth_token){ 
			$this->store =$store;
			$this->oauth_token =$oauth_token;
		  //$this->client = shopify\client($store, SHOPIFY_APP_API_KEY, $oauth_token);
    }
	
    public static function getInstance($store,$consumerkey,$consumerSecret,$oauth_token){
        $key = "{$store}";
        if (empty(self::$instances[$key])){
            $class = __CLASS__;
            self::$instances[$key] = new $class($store,$consumerkey,$consumerSecret,$oauth_token);
        }
        return self::$instances[$key];
    }

    public function __clone(){
        throw new Exception("Singleton Class Can Not Be Cloned");
    }

    public function get($parameters){
		$shopify = shopify\client($this->store, SHOPIFY_APP_API_KEY, $this->oauth_token);
        return $this->client->get($parameters);
    }
	
	public function post($data,$variantImages=array()){
		//echo $this->store;
		//echo $this->oauth_token;
		//print_r($data);
		//print_r($this->store);
		//print_r($this->oauth_token);
		try
		{
			$shopify = shopify\client($this->store, SHOPIFY_APP_API_KEY, $this->oauth_token);
		if(isset($data['product'])){
			$result =  $shopify('POST /admin/products.json', array(), $data);
			print_r($result);
			$variants = $result['variants'];
			$images = $result['images'];
			$variants2 = array();
			$i=0;
			$count = 0;
			print_r($variantImages);
			foreach($data['product']['images'] as $key=> $image){
				if(isset($variantImages[$image['src']])){
					$variant_postion = $variantImages[$image['src']];
					$image2 = array();
					$image2['id'] = $images[$key]['id'];
					$variant_ids = array();
					foreach($variant_postion as $pos){
						if(isset($variants[$pos])){
							$variant_ids[] = $variants[$pos]['id'];
						}
					}
					//print_r($image2);
					if(!empty($variant_ids)){
						$image2['variant_ids'] = $variant_ids;
						$rult = $shopify('PUT /admin/products/'.$result['id'].'/images/'.$image2['id'].'.json', array(), array('image' =>$image2));
					}
				}
				
			}
			return $result;
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
		
		return ;
		
    }
	
	
	
	public function put($data){
        return $this->client->put($data);
    }
	
	public function delete($parameters = array()){
        return $this->client->delete($parameters);
    }
	
	public function options(){
        return $this->client->options();
    }

    public function __destruct(){
        $this->Client=null;
    }
}

?>
<?php

class Oauthclient{
	
    private static $instances = array();

    private function __construct($store,$consumerkey,$consumerSecret){ 
       	 //$this->client = new Client($store,$consumerkey,$consumerSecret);
    }
	
    public static function getInstance($store,$consumerkey,$consumerSecret){
        $key = "{$store}";
        if (empty(self::$instances[$key])){
            $class = __CLASS__;
            self::$instances[$key] = new $class($store,$consumerkey,$consumerSecret);
        }
        return self::$instances[$key];
    }

    public function __clone(){
        throw new Exception("Singleton Class Can Not Be Cloned");
    }

    public function get($parameters){
        return $this->client->get($parameters);
    }
	
	public function post($data,$variant_count=array()){
		//print_r($data['product']);
		/*if(isset($data['product'])){
			$product = $data['product'];
			$data = array(
			 'name' => $product['title'],
			 'type' => 'variable',
			 'description' => $product['body_html'],
			 'images' => $product['images']
			);
			$attributes = array();
			$default_attributes = array();
			foreach($product['options'] as $option){
				$attributes[] = array(
					'name' => $option['name'],
            		'position' => 0,
            		'visible' => true,
            		'variation' => true,
           			'options' => $option['values']
				);
				$default_attributes[] = array(
					'name' =>  $option['name'],
            		'option' => $option['values'][0]
				);
			}
			$data['default_attributes'] = $default_attributes;
			$data['attributes'] = $attributes;
			$result = $this->client->post('products',$data);
			//print_r($result);
			$variants = $product['variants'];
			$images = $result['images'];
			$variants2 = array();
			$i=0;
			$items = count($variants)/count($images);
			$count =$items;
			//print_r($items);
			$index = 0;
			foreach($images as $image){
				$data = array(
					'image' => array(
						'id' => $image['id']
					)
				);
				
				$attributes = array();
				for(;$i<$count;$i++){
					if(isset($variants[$i])){
						$data['regular_price'] = $variants[$i]['price'];
						$data['sku'] = $variants[$i]['sku'];
						for($i=1;$i<10;$i++){
							if(isset($variants[$i]['option'.$i])){
									$attributes[] = array(
										'option' => $variants[$i]['option'.$i]
									);
							}else{
								break;
							}
					}
					}
				}
				$data['attributes'] = $attributes;
				$rult = $this->client->post('products/'.$result['id'].'/variations',$data);
				//print_r($rult);
				$count +=$items;
			}*/
			//print_r($result);
			
			
		//}
        //return $this->client->post($data);

		$variants = $data['product']['variants'];
		$i=0;
		$count = 0;
		foreach($variant_count as $key=> $vcount){
			$count+=$vcount['variant_count'];
			for(;$i<$count;$i++){
				if(isset($variants[$i])){
					$variants[$i]['image_pos'] = $key;
				}
			}
		}
		$data['product']['variants'] = $variants;
		$data['product']['id'] = 0;
		return $data['product'];
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
<?php

require 'vendor/autoload.php';
use phpish\shopify;

class ControllerCreateproduct extends Controller {
	
	public function index(){
		
		$this->load->language('checkout/cart');
		$this->load->language('shopify/product');

		$json = array();

		$this->load->model('commonipl/order');
		$this->load->model('commonipl/product');
		$this->load->model('account/wishlist');
		if (isset($this->request->get['product_id'])) {
			$product_id = $this->request->get['product_id'];
		} else {
			$product_id = '';
		}
		
		if (isset($this->request->post['product'])) {
			$products = array_filter($this->request->post['product']);
		} else {
			$products = array();
		}
		//print_r($products);
		$results = array();
		if(!empty($product_id)){
			$results[$product_id] = $this->pushProduct($products[$product_id],$product_id);
		}else{
			foreach($products['selected'] as $id){
				//if($product['checked']=='on'){
				$results[$id] = $this->pushProduct($products[$id],$id);
				//}
			}	
		}
		if(isset($results)){
			$json['success'] = $results;
			foreach($results as  $value){
				if(!empty($value)){
					$json['text'] = sprintf($this->language->get('publish_sucessfully'), 'https://'.$this->session->data['shop'].'/admin/products/'.$value);
				}
			}
			
		}else{
			$json['error'] = 'Push Error';
		}
		return $json;
		
	}
	
	public function pushProduct($product,$product_id){
		if(!isset($product['variant']['selected'])){
			return;
		}
		$variants = array();
		$images = array();
		$options = array();
		$position = 1;
		$variantImages = array();
		$option1Value = array();
		$option2Value = array();
		//print_r($product['variant']['selected']);
		foreach($product['variant']['selected'] as $key){
				$key = $key-1;
				$variant = array(
					'position' => $position,
					'price' => $product['variant']['price'][$key],
					'compare_at_price' => $product['variant']['compare_price'][$key],
					"sku"=>$product['variant']['sku'][$key],
				);	
				if(isset($product['variant']['option1'])){
					$variant['option1'] = $product['variant']['option1'][$key];
					$option1Value[] = $variant['option1'];
				}
				if(isset($product['variant']['option2'])){
					if(isset($variant['option1'])){
						$variant['option2'] = $product['variant']['option2'][$key];
						$option2Value[] = $variant['option2'];
					}else{
						$variant['option1'] = $product['variant']['option2'][$key];
						$option1Value[] = $variant['option1'];
					}
				}
				$variants[] = $variant;
				$image = $product['variant']['images'][$key];
				if(!in_array($image, $images)){
					$images[] = $image;
				}
				$variantImages[HTTPS_SERVER.'image/'.$product['variant']['images'][$key]][] = $position;
				$position++;
			
		}
		if(!empty($option1Value)){
			$options[] = array(
				'name'=>isset($product['option1'])?$product['option1']:$product['option2'],
				'values'=>array_unique($option1Value)
			);
		}
		if(!empty($option2Value)){
			$options[] = array(
				'name'=>$product['option2'],
				'values'=>array_unique($option2Value)
			);
		}
		$imageArray = array_merge($images,$product['images']);
		$images = array();
		foreach($imageArray as $position => $image){
			$images[] = array(
				'position'=>$position+1,
				"src"=>HTTPS_SERVER.'image/'.$image
			);
		}
		
		$data =  array(
			"title"=>$product['title'],
			"body_html"=> html_entity_decode($product['description']),
			"tags"=> $product['tag'],
			"vendor"=>  $this->customer->getFirstName(),
			"product_type"=>  $product['model'],
			"options"=>$options,
			"variants"=>$variants,
			"images"=>$images
		);
		//print_r($images);
		//print_r($variantImages);
		return $this->save($data,$variantImages,$product_id);
	}
	
	public function save($paoducts,$variantImages,$product_id){
		//$shopify = shopify\client($this->session->data['shop'], SHOPIFY_APP_API_KEY, $this->session->data['oauth_token']);
		$json = array();
		try
		{
			$product = $this->post(array('product' =>$paoducts),$variantImages);
			$product_Add_id = $this->model_commonipl_product->saveShopifyAddProduct($product,$product_id);
			if(isset($product['id'])){
				$this->model_account_wishlist->deleteWishlist($product_id);
				return $product['id'];
			}
			//$this->response->redirect($this->url->link('shopify/dashboard'));
			
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
		return '';
		
	}
	
	public function getImageCode($path){
		 $type=getimagesize($path);//取得图片的大小，类型等
     $content=file_get_contents($path);
     $file_content=chunk_split(base64_encode($content));//base64编码
/*     switch($type[2]){//判读图片类型
         case 1:$img_type="gif";break;
         case 2:$img_type="jpg";break;
         case 3:$img_type="png";break;
     }
     $img='data:image/'.$img_type.';base64,'.$file_content;//合成图片的base64编码*/
     return $file_content.'\n';
	}
		
		public function calculateCombination($inputList, $beginIndex, $arr,$arr2) {
	    global $arr2;
        if($beginIndex == count($inputList)){
			//print_r($beginIndex);
			$arr2[] = $arr;
			//print_r($arr);
			//print_r($arr2);
            return ;
        }
        foreach($inputList[$beginIndex] as $c){
            $arr[$beginIndex] = $c;
            $this->calculateCombination($inputList, $beginIndex + 1,$arr,$arr2);
        }
		return $arr2;
   }
   
   public function post($data,$variantImages=array()){
		//echo $this->store;
		//echo $this->oauth_token;
		//print_r($data);
		//print_r($this->store);
		//print_r($this->oauth_token);
		try
		{
			$shopify = shopify\client($this->session->data['shop'], SHOPIFY_APP_API_KEY,$this->session->data['oauth_token']);
		if(isset($data['product'])){
			$result =  $shopify('POST /admin/products.json', array(), $data);
			//print_r($result);
			$variants = $result['variants'];
			$images = $result['images'];
			$variants2 = array();
			$i=0;
			$count = 0;
			//print_r($variantImages);
			foreach($data['product']['images'] as $key=> $image){
				if(isset($variantImages[$image['src']])&&isset($images[$key])){
					$variant_postion = $variantImages[$image['src']];
					$image2 = array();
					$image2['id'] = $images[$key]['id'];
					$variant_ids = array();
					foreach($variant_postion as $pos){
						if(isset($variants[$pos-1])){
							$variant_ids[] = $variants[$pos-1]['id'];
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
	
		
}


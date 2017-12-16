<?php

require __DIR__.'/vendor/autoload.php';
use phpish\shopify;

require __DIR__.'/conf.php';

class ControllerShopifyCreateproduct extends Controller {
	public function index(){
		
		$this->load->language('checkout/cart');
		$this->load->language('shopify/product');

		$json = array();

		if (isset($this->request->post['product_id'])) {
			$product_id = (int)$this->request->post['product_id'];
		} else {
			$product_id = 0;
		}
		//echo __DIR__.'shopifyinstall/create_product.php';
		$this->load->model('catalog/product');
		//echo "product_id".$product_id;
		$product_info = $this->model_catalog_product->getProduct($product_id);
		//print_r($product_info);
		//$result = $this->addOrder();
		/*foreach ($result  as $order) {
			//echo 'order'.$order;
			//echo 'order'.$order['order_option'];
			//echo "cart".$order['cart_id']."--".$order['order_id']."--".$order['product_id'];
			$this->remove($order['cart_id']);		
		}
		$result_one = $result[0];*/
		//echo 'sku='.$product_info;
		$title = $this->request->post['pttl'];
		$pdsc = $this->request->post['pdsc'];
		$ptag = $this->request->post['ptag'];
		$pspr = $this->request->post['pspr'];
		$pcol = $this->request->post['pcol'];
		$pimgs =array();
		$variants = array();
		$images = array();
		//$order_option = $result_one['order_option'];
		if (isset($this->request->post['variant'])) {
			$variant = array_filter($this->request->post['variant']);
		} else {
			$variant = array();
		}
		if (isset($this->request->post['imgs'])) {
			$pimgs = array_filter($this->request->post['imgs']);
		} else {
			$pimgs = array();
		}
		if (isset($this->request->post['option'])) {
			$option = array_filter($this->request->post['option']);
		} else {
			$option = array();
		}
		$count = count($option);
		//print_r($variant);
		//print_r($option);
		//print_r($pimgs);
		//echo(json_encode($pimgs));
		//echo 'au='.$this->session->data['oauth_token'] ;
		//echo 'shop='.$this->session->data['shop'] ;
		//echo "count=".$count;
		$index =0;
		$imgs    = array();
		$productoption = $this->model_catalog_product->getProductOptions($product_id);
		//print_r($productoption);
		$option_data = array();
		$options = array();
		$optionColors = array();
		$option_value_id = array();
		foreach ($productoption as $opt) {
			if($opt['type']=='select'){
				
				$create = false;
				foreach ($opt['product_option_value'] as $option_value) {
					if(empty($option_value['image'])){
						if(!$create){
							$value = array();
							$option_data[$index] = array();
							$options[$index]['name'] = $opt['name'];
							$create = true;
						}
						$option_data[$index][] = 'Color:'.$option_value['name'].','.$opt['name'].":".$option_value['name'];
						$value[] = $option_value['name'];
					}else{
						$option_value_id[$opt['product_option_id']] = $option_value['option_value_id'];
						$option_images = $this->model_catalog_product->getOptionImages($option_value['option_value_id']);
							//print_r($option_value['name']);
							if(isset($option_images)){
								$img = array();
								foreach ($option_images as $option_image) {
									//echo $option_image['option_image_id'];
									//print_r($option[$option_image['option_image_id']]);
									if(isset($option[$opt['product_option_id']][$option_image['option_image_id']])){
										$img[$option_image['option_image_id']] = array(
											'src'=>$option_image['image'],
											'upload' =>$option[$opt['product_option_id']][$option_image['option_image_id']]
										);
								    } /*else {
									  $img .= $option_image['image'].":";
								  }*/
								}
								if(!empty($img)){
									$optionColors[] = $option_value['name'];
									$imgs[$opt['product_option_id']]= $img;
								}
								
							}
						
						//$value = array();
					}
				}
				if($create){
					$options[$index]['values']  = $value;
					$index++;
				}
				
				
			}		
		}
		//print_r($imgs);
		
		//print_r($optionColors);
		//print_r($option);
		$index =0;
		//$count = count($order_option);
		//print_r($option_data);
		//$result = array();
		$result = $this->calculateCombination($option_data, 0,$arr = array(),$arr2=array());
		//print_r('result'.$result);
		$this->load->model('shopify/order');
		//print_r($option);
		$option1 = array();
		
		foreach ($pimgs as $key => $value) {
			$count = 0;
					if(count($result)>0){
						foreach ($result  as $v) {
							$count++;
						$sku =  array(
							'product_id'  => $product_id,
							'price'       => $product_info['price'],
							'sku'        => $product_info['sku'],
							'model'        => $product_info['model'],
							'product_option_id'  => $key,
							'option_value_id'  => $option_value_id[$key] ,
							'product_options'       => json_encode($v),
							'option_file'       => $imgs[$key],
							'design_file'        => HTTP_SERVER.$value
						);
						//print_r($sku);
						$sku_id = $this->model_shopify_order->addProductSku($sku);
						$variant1 = array(
							'option2' => $optionColors[$index],
							"price"=>$pspr,
							"sku"=> $product_info['sku'].".".$sku_id
						);
						$optionIndex =3;
						$opt='';
						foreach($v  as $o){
							$oo = explode(":",$o);
							$oo = explode(",",$oo[1]);
							$variant1["option".$optionIndex] = $oo[0];
							$optionIndex++;
							$opt .=$oo[0];
						}
						$variant1['option1'] = isset($variant[$key])?$variant[$key]:$product_info['name'].' '.$optionColors[$index].'/'.$opt;		$option1[] = $variant1['option1'];
						$variants[]  = $variant1;
						}
					}else{
						$count++;
						$sku =  array(
							'product_id'  => $product_id,
							'price'       => $product_info['price'],
							'sku'        => $product_info['sku'],
							'model'        => $product_info['model'],
							'product_option_id'  => $key,
							'option_value_id'  => $option_value_id[$key] ,
							'option_file'       => $imgs[$key],
							'product_options'       => '',
							'design_file'        => HTTP_SERVER.$value
							//'customer_id'  => $product_info['customer_id']
						);
						//print_r($sku);
						$sku_id = $this->model_shopify_order->addProductSku($sku);
						/*if (isset($variant[$order_opt['product_option_id']])) {
							$optionl = $variant[$order_opt['product_option_id']];
						} else {
							$optionl = 'option'.$index;
						}*/
						$option1[] = isset($variant[$key])?$variant[$key]:'option'.$index;
						
						$variants[] = array(
							'option1' => isset($variant[$key])?$variant[$key]:'option'.$index,
							'option2' => $optionColors[$index],
							"price"=>$pspr,
							"sku"=> $product_info['sku'].".".$sku_id
						);
					}
 					//$im = file_get_contents(DIR_IMAGE.str_replace('image/','',$pimgs[$key]));
       				//$imdata = base64_encode($im);      
					
					$images[] = array(
						"attachment"=>$this->getImageCode(DIR_IMAGE.str_replace('image/','',$pimgs[$key]))
					);
					$index++;
					
			//}	
			//$index++;
		}
	   $optionsnew = array();
	   $optionsnew[] = array(
					'name'=>'Name',
					'values'=>$option1
			);
		if($optionColors){
		  $optionsnew[] = array(
				  'name'=>'Color',
				  'values'=>array_unique($optionColors)
		  );
		}
		foreach($options as $opt){
			$optionsnew[] = $opt;
		}	
		$paoduct = array(
							"title"=>$title,
							"body_html"=> html_entity_decode($pdsc),
							"tags"=> $ptag ,
							"vendor"=>  "vivajean",
							"product_type"=>  $product_info['model'],
							"options"=>$optionsnew,
							"variants"=>$variants,
							"images"=>$images
		);
		//print_r($paoduct);
		$this->save($paoduct,$product_id,$images,$variants);
	}
	
	public function save($paoduct,$product_id,$images,$variants){
		$shopify = shopify\client($this->session->data['shop'], SHOPIFY_APP_API_KEY, $this->session->data['oauth_token']);
		$json = array();
		try
		{
			//print_r();
		
			//print_r($variants);
			//echo print_r($paoduct);
			# Making an API request can throw an exception
			$product = $shopify('POST /admin/products.json', array(), array('product' =>$paoduct));
			//print_r($product);
			$variants = $product['variants'];
			$images = $product['images'];
			$variants2 = array();
			$i=0;
			$items = count($variants)/count($images);
			$count =$items;
			//print_r($items);
			$index = 0;
			foreach($images as $image){
				$image2 = array();
				$image2['id'] = $image['id'];
				$variant_ids = array();
				for(;$i<$count;$i++){
					if(isset($variants[$i])){
						$variant_ids[] = $variants[$i]['id'];
					}
				}
				$image2['variant_ids'] = $variant_ids;
				$result = $shopify('PUT /admin/products/'.$product['id'].'/images/'.$image['id'].'.json', array(), array('image' =>$image2));
				//print_r($result);
				$count +=$items;
			}
			
			//print_r($product);
			$this->load->model('shopify/product');
			$this->model_shopify_product->saveShopifyAddProduct($product,$product_id);
			$this->session->data['sussecc'] = sprintf($this->language->get('publish_sucessfully'), 'https://'.$this->session->data['shop'].'/admin/products/'.$product['id']);
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
		$json['success'] = 'index.php?route=shopfiy/dashboard';
		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
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
	
		
}

?>
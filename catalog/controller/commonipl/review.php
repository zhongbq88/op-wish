<?php
class ControllerCommoniplReview extends Controller {
	
	public function index(){
		$this->load->language('checkout/cart');

		$json = array();

		if (isset($this->request->post['product_id'])) {
			$product_ids = $this->request->post['product_id'];
		} else {
			$product_ids = array();
		}
		if (isset($this->request->post['option'])) {
				$option_src = array_filter($this->request->post['option']);
			} else {
				$option_src = array();
			}
			if (isset($this->request->post['option-img'])) {
				$pimgs = array_filter($this->request->post['option-img']);
			} else {
				$pimgs = array();
			}
			if (isset($this->request->post['variant'])) {
				$varianttitle = array_filter($this->request->post['variant']);
			} else {
				$varianttitle = array();
			}
			
		$this->load->model('catalog/product');
		$quantity = 1;
		
		$thumbnail = array();
		$option_descriptions=array();
		$option_image_price = array();
		
		$variantTitles = array();
		$option_value_ids = array();
		$srcImages = array();
		$this->load->model('commonipl/image');
		//print_r($pimgs);
		$variants = array();
		$product_index =0;
		$viewimages = array();
		if(isset($product_ids)){
			
			foreach($product_ids as $ky => $product_id){
				$product_info = $this->model_catalog_product->getProduct($product_id);
				if ($product_info) {
					$images = array();
					$json['title'] = $product_info['name'];
					$product_options = $this->model_catalog_product->getProductOptions($product_id);
					//print_r($product_options);
					if(!isset($option_descriptions[$product_id])){
						$option_descriptions[$product_id] = html_entity_decode($product_info['description'], ENT_QUOTES, 'UTF-8').'<br>';
					}
					$option_data = array();
					$index=0;
					$optionName ='';
					foreach ($product_options as $product_option) {
						//print_r($product_option);
						if($product_option['type']=='select'){
							$create = false;
							foreach ($product_option['product_option_value'] as $option_value) {
								//print_r($option_value['option_value_id']);
								//print_r($option_value['image']);
								$optionName =  $option_value['name'];
								if(!empty($option_value['image'])){
									//print_r($option_value['option_value_id']);
									$option_images = $this->model_catalog_product->getOptionImages($option_value['option_value_id']);
									//print_r($key);
									if(isset($option_images)){
										if(isset($pimgs[$ky][$option_images[0]['option_image_id']])){
											$image = $pimgs[$ky][$option_images[0]['option_image_id']];				
											if(!empty($image)){
												$imgs = array();
												$thumb;
												if(count($option_images)>1){
													$imgs[0] = str_replace(HTTP_SERVER,'/',$image);
													$imgs[1] = str_replace(HTTP_SERVER,'/',$pimgs[$ky][$option_images[1]['option_image_id']]);
													$imgs[0] = str_replace('/image/',DIR_IMAGE,$imgs[0]);
													$imgs[1] = str_replace('/image/',DIR_IMAGE,$imgs[1]);
													$image = 'catalog/designs/'.$product_option['product_option_id'].'_'.$this->customer->getId().'_'.time().".jpg";
													$this->createImage($imgs,DIR_IMAGE.$image);
													$thumb = $image;
													$image = "image/".$image;
												}else{
													$imgs[0] = str_replace(HTTP_SERVER,'/',$image);
													$imgs[0] = str_replace('/image/',DIR_IMAGE,$imgs[0]);
													$image = 'catalog/designs/'.$product_option['product_option_id'].'_'.$this->customer->getId().'_'.time().".jpg";
													//print_r($imgs[0]);
													$this->createImage($imgs,DIR_IMAGE.$image);
													$thumb = $image;
													$image = "image/".$image;
												}
												
												
												$img = array();
												//print_r($ky);
												//print_r($option);
												foreach ($option_images as $option_image) {
													$img[$option_image['option_image_id']] = array(
														'src'=>$option_image['image'],
														'upload' =>$option_src[$ky][$option_image['option_image_id']]
													);
												}
							
												$option_value_ids[$product_option['product_option_id']] = $option_value['option_value_id'];		
												$srcImages[$product_index] = $img;	
												$images[$product_option['product_option_id']] = $image;							
												$array = getimagesize(DIR_IMAGE.$thumb); 
												$thumbnail[$product_option['product_option_id']] =$this->model_commonipl_image->resize($thumb, $array[0]/4, $array[1]/4);
											
											}
											
										}
									}
								}else{
									if(!$create){
										$option_data[$index] = array();
										$create = true;
									}
									$option_data[$index][] = $option_value;
								}
							}
							if($create){
								$index++;
							}
						}
						
					}
					//print_r($images);
					
					//print_r($option_data);
					$options = $this->calculateCombination($option_data, 0,$arr = array(),$arr2=array());

						foreach($images as $key=> $image){
							$variants[$product_index] = array(
								'variant_index'=>$product_index,
								'product_option_id'=>$key,
								'product_id'=>$product_id,
								'product_name'    =>$product_info['name'].'-'.$optionName,
								'option_value_id'=>$option_value_ids[$key],
								'name'=>$varianttitle[$ky],
								'price'=>number_format($product_info['price'],2),
								'product_price'=>number_format($product_info['price']*2,2),
								'compare_price'=>number_format($product_info['price']*4,2),
								'thumbnail' => $thumbnail[$key],
								'image' => $image
							);
							//print_r(count($options));
							if(count($options)>0){
								$variants[$product_index]['type'] = "Size";
								$opts = array();
								foreach($options as $option){
									$name = '';
									$price = number_format($product_info['price'],2);
									//print_r($price);
									foreach($option as $opt){
										$price += $opt['price'];
										$name.='- '.$opt['name'].' ';
									}
									if(empty(substr($name,1))){
										continue;
									}
									$opts[] = array(
										'name'=>substr($name,1),
										'each_price'=>number_format($price,2),
										'product_price'=>number_format($price*2,2),
										'compare_price'=>number_format($price*4,2)
									); 
								}
								$variants[$product_index]['options'] = $opts;
							}else{
								//print_r($product_info['price']);
								$price = number_format($product_info['price']*1.0,2);
								$variants[$product_index]['type'] = "Color";
								$variants[$product_index]['options'][] = array(
										'name'=>$optionName,
										'price'=>number_format($price,2),
										'each_price'=>number_format($price,2),
								'product_price'=>number_format($price*2,2),
								'compare_price'=>number_format($price*4,2)
									); 
							}
						}
						//print_r($images);
						
						//$json['product_id'] = $product_id;
				}

				$product_index ++;
			}

		}
		$this->session->data['srcImages'] = $srcImages;
		$descriptions = '';
		foreach($option_descriptions as $des){
			$descriptions = $des;
			break;
		}
		$json['descriptions'] = $descriptions;
		$json['variants'] = $variants;
		//print_r($variants);
		$json['footer'] = $this->load->controller($this->session->data['store'].'/footer');
		$json['header'] = $this->load->controller($this->session->data['store'].'/header');
		$this->response->setOutput($this->load->view('commonipl/review', $json));
	}
	
	public function createImage($imgs,$savePath){

		$source = array();
 
		foreach ($imgs as $k => $v) {
    		$source[$k]['source'] = Imagecreatefrompng($v);
     
    		$source[$k]['size'] = getimagesize($v);
     
		}
		$count = count($imgs);
 		$padding = 200*($count-1);
		$count = count($imgs);
		//创建一个新的，和大图一样大的画布
		$target_img     = imageCreatetruecolor(imagesx( $source[0]['source'])+ $padding*2,imagesy( $source[0]['source'])*$count);
		$color = imagecolorallocate($target_img, 255, 255, 255);
		imagefill($target_img, 0, 0, $color);
		$num1 = 0;
		$num  = 1;
		$tmp  = 2;
		$tmpy = 0; //图片之间的间距
		for ($i = 0; $i < $count ; $i++) {
			imagecopy($target_img, $source[$i]['source'],$padding, $tmpy, 0, 0, $source[$i]['size'][0], $source[$i]['size'][1]);
			$tmpy =  $tmpy+ $source[$i]['size'][1];
		}
		Imagejpeg($target_img, $savePath,80);
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
		$result = $arr2;
		$arr2= array();
		return $result;
   }
	
}
?>
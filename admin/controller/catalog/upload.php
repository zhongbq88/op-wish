<?php
/** Include path **/
set_include_path(DIR_SYSTEM.'/library/');

/** PHPExcel_IOFactory */
include 'PHPExcel/IOFactory.php';
set_time_limit(0);//设置PHP超时时间

class ControllerCatalogUpload extends Controller {
	private $error = array();
	
	public function index() {
		$this->load->language('catalog/product');

		$json = array();

		
		/*if (isset($this->request->files['file']['name'])) {
			if (substr($this->request->files['file']['name'], -10) != '.xlsx') {
				$json['error'] = $this->language->get('error_filetype');
			}

			if ($this->request->files['file']['error'] != UPLOAD_ERR_OK) {
				$json['error'] = $this->language->get('error_upload_' . $this->request->files['file']['error']);
			}
		} else {
			$json['error'] = $this->language->get('error_upload');
		}*/

		if (!$json) {
			$this->session->data['install'] = token(10);
			
			$file = DIR_UPLOAD .time().'_'.$this->request->files['file']['name'];
			
			move_uploaded_file($this->request->files['file']['tmp_name'], $file);

			if (is_file($file)) {
				$this->load->model('catalog/product');

				$objPHPExcel = PHPExcel_IOFactory::load($file);
	
				$this->load->model('localisation/language');
				$data = array();
				$languages = $this->model_localisation_language->getLanguages();
				$language_id=1;
				foreach($languages as $language){
					$language_id=$language['language_id'];
					break;
				}
				$option1 =$objPHPExcel->getActiveSheet()->getCell("E1")->getValue();
				$option2 =$objPHPExcel->getActiveSheet()->getCell("F1")->getValue();
				$option1data = $this->model_catalog_product->getOption($option1);
				if(!isset($option1data)){
					$option1data = array(
						"type" => 'select',
						"sort_order" => 0,
						"language_id" => $language_id,
						"name" => $option1
					);
					$option1data['option_id'] = $this->model_catalog_product->addOption($option1data);
				}
				$option2data = $this->model_catalog_product->getOption($option2);
				if(!isset($option2data)){
					$option2data = array(
						"type" => 'select',
						"sort_order" => 0,
						"language_id" => $language_id,
						"name" => $option2
					);
					$option2data['option_id'] = $this->model_catalog_product->addOption($option2data);
				}
				//print_r($option1data);
				//print_r($option2data);
				
				foreach($objPHPExcel->getWorksheetIterator() as $sheet){ //循环取sheet
				
					  foreach($sheet->getRowIterator() as $row){ //逐行处理
							
							if($row->getRowIndex()<2)continue;
							$rowdata = array();
							foreach($row->getCellIterator() as $cell){ //逐列读取
								  $rowdata[] = $cell->getValue(); //获取单元格数据
							}
							
							$data[] = $rowdata;
					  }
				
				}
				
				$productId ='';
				$products = array();
				$productar = array();
				$options1 = array();
				$options2 = array();
				$images = array();
				$path = 'catalog/product/';
				$quantity = 0;
				if (!is_dir(DIR_IMAGE . $path)) {
					@mkdir(DIR_IMAGE . $path, 0777);
				}
				foreach($data as $product){
					if(empty($product[9])){
						continue;
					}
					$image =  $path.basename($product[9]);
					if (!is_file(DIR_IMAGE . $image) && !empty($image)) {
						file_put_contents(DIR_IMAGE . $image, file_get_contents($product[9]));
					}
					if($productId!=$product[1]){
						//print_r(123);
						if(isset($productar)&&count($productar)>0){
						
							if(isset($options1)&&count($options1)>0){
								$productar['product_option'][] = array(
								"option_id" => $option1data['option_id'],
								"type" => 'select',
								'product_option_value'=>$options1
							);
							}
							if(isset($options2)&&count($options2)>0){
								$productar['product_option'][] = array(
								"option_id" => $option2data['option_id'],
								"type" => 'select',
								'product_option_value'=>$options2
							);
							}
							$productar['quantity'] = $quantity;
							/*$product_id = $this->model_catalog_product->uploadProduct($productar);
							if(isset($product_id)){
								$this->model_catalog_product->addVariants($variants,$product_id);
							}*/
							$products[] = $productar;
						}
						for($i=16;$i<=25;$i++){
							if(empty($product[$i])){
								break;
							}else{
								$imagesURL = $product[$i];
								$savePath =  $path.basename($imagesURL);
								if (!is_file(DIR_IMAGE.$savePath)) {
									file_put_contents(DIR_IMAGE.$savePath, file_get_contents($imagesURL));
								}
								$images[] = $savePath;
							}
						}
						
						/*$imagesURLArray = array_unique($images );
						
						foreach($imagesURLArray as $imagesURL) {
							
						}*/
						$productar = array(
						    "language_id" => $language_id,
							'sku' => $product[1],
							'price' => $product[2],
							'name' => $product[3],
							'quantity' => $product[6],
							'shipping' => $product[7],
							'weight' =>  $product[8],
							'image' => $image,
							'tag' => $product[10],
							'description' =>  $this->descriptionFormat($product[11]),
							'model' => $product[13],
							'date_available' => $product[15],
							'product_id' => $product[26],
							'category_id' => $product[27],
							'product_image'=>$images
						);
						$options1 = array();
						$options2 = array();
						$images = array();
						$variants = array();
						$quantity = 0;
						
					}
					$option_id1 ='';
					$option_id2 ='';
					$key ='';
					if(!empty($product[4])){
							$option = array(
								"option_id" => $option1data['option_id'],
								"language_id" => $language_id,
								"name" => $product[4]
							);
							$option_id1 = $this->getOptionValueId($option1data,$option);
							$option["option_value_id"] = $option_id1;
							if(!isset($options1[$product[4]])){
								$options1[$product[4]] = $option;
								
							}
							$key = $product[4];
					}
					if(!empty($product[5])){
						
							$option = array(
								"option_id" => $option2data['option_id'],
								"language_id" => $language_id,
								"name" => $product[5]
							);
							$option_id2 = $this->getOptionValueId($option2data,$option);
							$option["option_value_id"] = $option_id2;
							if(!isset($options2[$product[5]])){
								$options2[$product[5]] = $option;
							}
							$key += $product[5];
							
					} 
					
					if(!isset($variants[$key])){
						  $variants[$key] = array(
							  'variants_sku'=>$product[0],
							  'option1'=>$product[4],
							  'option1_id'=>$option_id1,
							  'option2_id'=>$option_id2,
							  'option2'=>$product[5],
							  'quantity' => $product[6],
							  'price'=>$product[2],
							  'sale_price'=>$product[2]*1.5,
							  'msrp'=>$product[12]*1.5,
							  'weight'=>$product[8],
							  'variants_image'=>$image
						  );
					}
					$quantity +=$product[6];
					$productId =$product[1];
				}
				if(isset($productar)&&count($productar)>0){
						
						if(isset($options1)&&count($options1)>0){
								$productar['product_option'][] = array(
								"option_id" => $option1data['option_id'],
								"type" => 'select',
								'product_option_value'=>$options1
							);
							}
							if(isset($options2)&&count($options2)>0){
								$productar['product_option'][] = array(
								"option_id" => $option2data['option_id'],
								"type" => 'select',
								'product_option_value'=>$options2
							);
							}
						$productar['quantity'] = $quantity;
						print_r($productar);
						/*$product_id = $this->model_catalog_product->uploadProduct($productar);
						if(isset($product_id)){
							$this->model_catalog_product->addVariants($variants,$product_id);
						}*/
				}
				print_r($products);
				
			//print_r(count($data));
			//print_r($data);

				$json['success'] = $this->language->get('text_success');
			} else {
				$json['error'] = $this->language->get('error_file');
			}
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}
	
	
	public function qty() {
		$this->load->language('catalog/product');
		$json = array();
		if (!$json) {
			
			
			$file = DIR_UPLOAD .time().'_'.$this->request->files['file']['name'];
			
			move_uploaded_file($this->request->files['file']['tmp_name'], $file);

			if (is_file($file)) {
				$this->load->model('catalog/product');

				$objPHPExcel = PHPExcel_IOFactory::load($file);

				$variants = array();
				$products = array();
				$shopifys = array();
				$skus = '';
				
				foreach($objPHPExcel->getWorksheetIterator() as $sheet){ //循环取sheet
				
					  foreach($sheet->getRowIterator() as $row){ //逐行处理
							
							if($row->getRowIndex()<2)continue;
							$rowdata = array();
							foreach($row->getCellIterator() as $cell){ //逐列读取
								  $rowdata[] = $cell->getValue(); //获取单元格数据
							}
							if(isset($products[$rowdata[1]])){
								$products[$rowdata[1]]['quantity'] += $rowdata[3];
							}else{
								$skus += ",'".$rowdata[1]."'";
								$products[$rowdata[1]] = array(
										'price' => $rowdata[2],
										'quantity' =>$rowdata[3],
										'weight'=>$rowdata[4]
								
								);
							}
							$shopifys[$rowdata[0]] = $rowdata[3];
							
							$variants[] = array(
								'variants_sku'=>$rowdata[0],
								'quantity' => $rowdata[3],
								'price'=>$rowdata[2],
								'sale_price'=>$rowdata[2]*1.5,
							    'msrp'=>$rowdata[5]*1.5,
								'weight'=>$rowdata[4]
							);
					  }
				
				}
				$this->model_catalog_product->updateProducts($products);
				$this->model_catalog_product->updateVariants($variants);
				//$skus = substr($skus,1);
				$shopifyProducts = $this->model_catalog_product->getShopifyProduct($skus);
				if(isset($shopifyProducts)){
					$variants = array();
					foreach($shopifyProducts as $sp){
						$product = json_decode($sp['shopify_product_json'],true);
						if(isset($product['variants'])){
							foreach($product['variants'] as $variant){
								//print_r($variant);
								if(isset($variant['id'])&& isset($shopifys[$variant['sku']])){
									$variants[$sp['customer_id']][] = array(
									'id'=>$variant['id'],
									'inventory_quantity'=> $shopifys[$variant['sku']],
									'old_inventory_quantity'=>$variant['inventory_quantity']
									);
								}
							}
						}
					}
					if(isset($variants)){
						$json['variants'] = $variants;
					}
				}
				
			//print_r(count($data));
			//print_r($data);

				$json['success'] = $this->language->get('text_success');
			} else {
				$json['error'] = $this->language->get('error_file');
			}
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}
	
	private function descriptionFormat($description){
		return  str_replace(';','</br>',str_replace(':',':  ',$description));
		
	}
	
	public function getOptionValueId($option,$optionValue){
		if(isset($option['option_value'])){
			foreach($option['option_value'] as $value){
				if($value['name']==$optionValue['name']){
					return $value['option_value_id'];
				}
			}
		}
		return $this->model_catalog_product->addOptionValue($optionValue,$option['option_id']);
	}
	
}

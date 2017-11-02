<?php
class ControllerCommoniplCategory extends Controller {
	public function index() {
		$this->load->language('product/category');

		$this->load->model('catalog/category');

		$this->load->model('catalog/product');

		$this->load->model('tool/image');

		$category_info = $this->model_catalog_category->getCategories();
		$this->load->language('commonipl/category');

		$this->load->model('commonipl/product');

		$this->load->model('tool/image');
		//print_r($category_info);
		$data['categories'] = array();
		if ($category_info) {

			foreach ($category_info as $result) {
				/*$filter_data = array(
					'filter_category_id'  => $result['category_id'],
					'filter_sub_category' => true
				);
				if ($result['image']) {
					$image = $this->model_tool_image->resize($result['image'], $this->config->get('theme_' . $this->config->get('config_theme') . '_image_product_width'), $this->config->get('theme_' . $this->config->get('config_theme') . '_image_product_height'));
				} else {
					$image = $this->model_tool_image->resize('placeholder.png', $this->config->get('theme_' . $this->config->get('config_theme') . '_image_product_width'), $this->config->get('theme_' . $this->config->get('config_theme') . '_image_product_height'));
				}*/
				$data['categories'][] = array(
					'name' => $result['name'],
					/*'thumb' =>$image,*/
					'productlist' => $this->load($result['category_id'])
				);
			}
		}
		$data['productlist'] =  $this->load->controller('commonipl/products');
		//print_r($data['categories']);
		if(isset($this->request->get['declined'])){
			$charging = $this->config->get('config_charging');
			$data['declined'] = $charging['tips'];
			
		}
		if(isset($this->session->data['home'])){
			$data['home'] = true;
			
		}
		$data['footer'] = $this->load->controller($this->session->data['store'].'/footer');
		$data['header'] = $this->load->controller($this->session->data['store'].'/header');
		$this->response->setOutput($this->load->view('commonipl/category', $data));
	}
	
	public function load($category_id) {
	
		$data['products'] = array();
		$this->load->model('account/wishlist');
		$wishlistProductId = $this->model_account_wishlist->getWishlistProductId();
		$results = $this->model_commonipl_product->getProductsByCategoryId($category_id);
		foreach ($results as $result) {
			if ($result['image']) {
				$image = $this->model_tool_image->resize($result['image'], $this->config->get('theme_' . $this->config->get('config_theme') . '_image_product_width'), $this->config->get('theme_' . $this->config->get('config_theme') . '_image_product_height'));
			} else {
				$image = $this->model_tool_image->resize('placeholder.png', $this->config->get('theme_' . $this->config->get('config_theme') . '_image_product_width'), $this->config->get('theme_' . $this->config->get('config_theme') . '_image_product_height'));
			}

			if ($this->customer->isLogged() || !$this->config->get('config_customer_price')) {
				$price = $this->currency->format($this->tax->calculate($result['price'], $result['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']);
			} else {
				$price = false;
			}

			if (isset($result['special'])&&(float)$result['special']) {
				$special = $this->currency->format($this->tax->calculate($result['special'], $result['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']);
			} else {
				$special = false;
			}

			if ($this->config->get('config_tax')) {
				$tax = $this->currency->format(isset($result['special'])&&(float)$result['special'] ? $result['special'] : $result['price'], $this->session->data['currency']);
			} else {
				$tax = false;
			}

			if ($this->config->get('config_review_status')&&isset($result['rating'])) {
				$rating = (int)$result['rating'];
			} else {
				$rating = false;
			}

			$data['products'][] = array(
				'product_id'  => $result['product_id'],
				'thumb'       => $image,
				'name'        => utf8_substr(trim(strip_tags(html_entity_decode($result['name'], ENT_QUOTES, 'UTF-8'))), 0, 60) . '..',
				'description' => utf8_substr(trim(strip_tags(html_entity_decode($result['description'], ENT_QUOTES, 'UTF-8'))), 0, $this->config->get('theme_' . $this->config->get('config_theme') . '_product_description_length')) . '..',
				'price'       => $price,
				'special'     => $special,
				'tax'         => $tax,
				'minimum'     => $result['minimum'] > 0 ? $result['minimum'] : 1,
				'rating'      => isset($result['rating'])?$result['rating']:0,
				'added'       => $this->inArray($result['product_id'],$wishlistProductId),
				'href'        => $this->url->link('commonipl/product','product_id=' . $result['product_id'])
			);
		}
		return $this->load->view('commonipl/products', $data);	

	}
	
	private function inArray($product_id,$array){
		
		foreach($array as $value){
			if($product_id == $value['product_id']){
				
				return 1;
			}
		}
		return 0;
	}
}

<?php
class ControllerAccountWishList extends Controller {
	public function index() {
		if (!$this->customer->isLogged()) {
			$this->session->data['redirect'] = $this->url->link('account/wishlist', '', true);

			$this->response->redirect($this->url->link('account/login', '', true));
		}

		$this->load->language('account/wishlist');

		$this->load->model('account/wishlist');

		$this->load->model('commonipl/product');

		$this->load->model('tool/image');

		

		$this->document->setTitle($this->language->get('heading_title'));

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/home')
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_account'),
			'href' => $this->url->link('account/account', '', true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('account/wishlist')
		);

		if (isset($this->session->data['success'])) {
			$data['success'] = $this->session->data['success'];

			unset($this->session->data['success']);
		} else {
			$data['success'] = '';
		}

		$data['products'] = array();

		$results = $this->model_account_wishlist->getWishlist();
		if(isset($results)){
			$data['collection'] = $this->load->controller('shopify/loadorders/collection');
		}
		
		$coupons =  $this->model_commonipl_product->getCoupons();

		foreach ($results as $result) {
			$product_info = $this->model_commonipl_product->getProduct($result['product_id']);

			if ($product_info) {
				
				if ($product_info['image']) {
					$image = $this->model_tool_image->resize($product_info['image'], 328,328);
				} else {
					$image = $this->model_tool_image->resize('placeholder.png', $this->config->get('theme_' . $this->config->get('config_theme') . '_image_product_width'), $this->config->get('theme_' . $this->config->get('config_theme') . '_image_product_height'));
				}
				

				if ($product_info['quantity'] <= 0) {
					$stock = $product_info['stock_status'];
				} elseif ($this->config->get('config_stock_display')) {
					$stock = $product_info['quantity'];
				} else {
					$stock = $this->language->get('text_instock');
				}

				if ($this->customer->isLogged() || !$this->config->get('config_customer_price')) {
					$price = $this->currency->format($this->tax->calculate($product_info['price'], $product_info['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']);
					/*$sale_price = $this->tax->calculate($product_info['sale_price'], $product_info['tax_class_id'], $this->config->get('config_tax'));
					$compare_price = $this->tax->calculate($product_info['msrp'], $product_info['tax_class_id'], $this->config->get('config_tax'));*/
				} else {
					$price = false;
					/*$sale_price = false;
					$compare_price = false;*/
				}

				if (isset($product_info['special'])&&(float)$product_info['special']) {
					$special = $this->currency->format($this->tax->calculate($product_info['special'], $product_info['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']);
				} else {
					$special = false;
				}
				
			$results = $this->model_commonipl_product->getProductImages($product_info['product_id']);
			$images = array();
			foreach ($results as $result) {
				$images[] = array(
					'src' =>$result['image'],
					'thumb' =>  $this->model_tool_image->resize($result['image'], $this->config->get('theme_' . $this->config->get('config_theme') . '_image_product_width'), $this->config->get('theme_' . $this->config->get('config_theme') . '_image_product_height'))
				);
				
			}
			 $variants_datas =$this->model_commonipl_product->getProductVariants($product_info['product_id']);
			 $variants = array();
				foreach($variants_datas as $variant){
					$variant['src'] =$variant['variants_image'];
					$variant['thumb'] = $this->model_tool_image->resize($variant['variants_image'], $this->config->get('theme_' . $this->config->get('config_theme') . '_image_additional_width'), $this->config->get('theme_' . $this->config->get('config_theme') . '_image_additional_height'));
					$variant['coupon'] = $this->getCoupon($coupons,$product_info['product_id'],$variant['price'],$product_info['tax_class_id']);
					$variant['price'] = $this->currency->format($this->tax->calculate($variant['price'], $product_info['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']);
					$variant['sale_price'] = $this->tax->calculate($variant['sale_price'], $product_info['tax_class_id'], $this->config->get('config_tax'));
					$variant['compare_price'] = $this->tax->calculate($variant['msrp'], $product_info['tax_class_id'], $this->config->get('config_tax'));
					$variants[] = $variant;
				}
				
				$data['products'][] = array(
					'product_id' => $product_info['product_id'],
					'thumb'      => $image,
					'images'	 =>$images,
					'variants'	 =>$variants,
					'name'       => $product_info['name'],
					'model'      => $product_info['model'],
					'tag'      => $product_info['tag'],
					'stock'      => $stock,
					'description' => $product_info['description'],
					'price'      => $price,
					/*'sale_price' => $sale_price,
					'compare_price' => $compare_price,*/
					'special'    => $special,
					'href'       => $this->url->link('product/product', 'product_id=' . $product_info['product_id']),
					'remove'     => $this->url->link('account/wishlist/remove', 'product_id=' . $product_info['product_id'])
				);
			} else {
				$this->model_account_wishlist->deleteWishlist($result['product_id']);
			}
		}

		$data['continue'] = $this->url->link('account/account', '', true);

		$data['column_left'] = $this->load->controller('common/column_left');
		$data['column_right'] = $this->load->controller('common/column_right');
		$data['content_top'] = $this->load->controller('common/content_top');
		$data['content_bottom'] = $this->load->controller('common/content_bottom');
			$data['footer'] = $this->load->controller($this->session->data['store'].'/footer');
		$data['header'] = $this->load->controller($this->session->data['store'].'/header');

		$this->response->setOutput($this->load->view('account/wishlist', $data));
	}
	
	public function addmore(){
		$json = array();
		$this->load->language('account/wishlist');
		/*if (isset($this->request->post['product'])) {
			$product_ids = array_filter($this->request->post['product']);
			foreach($product_ids as $values){
				$this->load->language('account/wishlist');
				$this->load->model('account/wishlist');
				foreach($values as $product_id){
					$this->model_account_wishlist->addWishlist($product_id);
				}
				$json['success'] = sprintf($this->language->get('text_success'),'','', $this->url->link('account/wishlist'));
				
				break;
			}
		}*/
		$json['success'] = sprintf($this->language->get('text_success'),'','', $this->url->link('account/wishlist'));
		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

	public function add() {
		$this->load->language('account/wishlist');

		if (isset($this->request->post['product_id'])) {
			$product_id = $this->request->post['product_id'];
		} else {
			$product_id = 0;
		}
		//print_r('product_id='.$product_id);
		$this->load->model('commonipl/product');

		$product_info = $this->model_commonipl_product->getProduct($product_id);

		if ($product_info) {
			if ($this->customer->isLogged()) {
				// Edit customers cart
				$this->load->model('account/wishlist');

				$this->model_account_wishlist->addWishlist($this->request->post['product_id']);

				$json['success'] = sprintf($this->language->get('text_success'), $this->url->link('product/product', 'product_id=' . (int)$this->request->post['product_id']), $product_info['name'], $this->url->link('account/wishlist'));

				$json['total'] = sprintf($this->language->get('text_wishlist'), $this->model_account_wishlist->getTotalWishlist());
			} else {
				if (!isset($this->session->data['wishlist'])) {
					$this->session->data['wishlist'] = array();
				}

				$this->session->data['wishlist'][] = $this->request->post['product_id'];

				$this->session->data['wishlist'] = array_unique($this->session->data['wishlist']);

				$json['success'] = sprintf($this->language->get('text_login'), $this->url->link('account/login', '', true), $this->url->link('account/register', '', true), $this->url->link('commonipl/product', 'product_id=' . (int)$this->request->post['product_id']), $product_info['name'], $this->url->link('account/wishlist'));

				$json['total'] = sprintf($this->language->get('text_wishlist'), (isset($this->session->data['wishlist']) ? count($this->session->data['wishlist']) : 0));
			}
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}
	
	
	public function remove(){
		$this->load->model('account/wishlist');
		$this->load->language('account/wishlist');
		$json = array();
		if (isset($this->request->get['product_id'])) {
			$this->model_account_wishlist->deleteWishlist($this->request->get['product_id']);
			$json['product_id'] = $this->request->get['product_id'];
			$json['success'] = $this->language->get('text_remove');
			
		}
		
			
		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}
	
	private function getCoupon($coupons,$product_id,$cost,$tax_class_id){
		$category_ids =  $this->model_commonipl_product->getProductCategories($product_id);
		foreach($coupons as $coupon){
			if(in_array($coupon['category_id'],$category_ids) || $coupon['product_id'] == $product_id|| empty($coupon['category_id'])&&empty($coupon['product_id'])){
				
				if($coupon['type']=='P'){
					$discount = number_format($cost*$coupon['discount']/100.00,2);
				}else{
					$discount = number_format($coupon['discount'],2);
				}
				$price =  $this->currency->format($this->tax->calculate($cost-$discount, $tax_class_id, $this->config->get('config_tax')), $this->session->data['currency']);
				return array(
					"name" =>$coupon['name'],
					"price"=>$price,
					"discount"=>$discount
				);
			}
		}
		return false;
		
	}
}

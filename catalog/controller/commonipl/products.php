<?php

class ControllerCommoniplProducts extends Controller {
	
	public function index() {
	
		$this->load->language('commonipl/category');

		$this->load->model('commonipl/product');

		$this->load->model('tool/image');

		if (isset($this->request->get['category_id'])) {
			$category_id = $this->request->get['category_id'];
		} else {
			$category_id = '';
		}

		$data['products'] = array();

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
				'href'        => $this->url->link('commonipl/product','product_id=' . $result['product_id'])
			);
		}
			//$data['footer'] = $this->load->controller($this->session->data['store'].'/footer');
	//$data['header'] = $this->load->controller($this->session->data['store'].'/header');
	//$this->response->setOutput($this->load->view('commonipl/products', $data));
	//print_r($data);
		if(isset($this->session->data['home'])){
			$data['home'] = true;
			
		}
		return $this->load->view('commonipl/products', $data);	

	}
	
}

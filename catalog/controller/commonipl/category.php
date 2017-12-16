<?php
class ControllerCommoniplCategory extends Controller {
	public function index() {
		$this->load->language('product/category');

		$this->load->model('catalog/category');

		$this->load->model('catalog/product');

		$this->load->model('tool/image');

		$category_info = $this->model_catalog_category->getCategories();
		$this->load->language('commonipl/category');

		

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
					'productlist' => $this->load->controller('commonipl/products/load',$result['category_id'])

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
		$this->load->model('setting/module');
		$setting_info = $this->model_setting_module->getModule('27');
		//print_r($setting_info);
		if ($setting_info && $setting_info['status']) {
			$output = $this->load->controller('extension/module/banner', $setting_info);
			if (isset($output)) {
				 $data['content_top'] = $output;
			}
		}
		$data['footer'] = $this->load->controller($this->session->data['store'].'/footer');
		$data['header'] = $this->load->controller($this->session->data['store'].'/header');
		$this->response->setOutput($this->load->view('commonipl/category', $data));
	}
}

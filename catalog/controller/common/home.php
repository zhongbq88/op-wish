<?php
class ControllerCommonHome extends Controller {
	public function index() {
		$this->session->data['home'] = true ;
		$this->document->setTitle($this->config->get('config_meta_title'));
		$this->document->setDescription($this->config->get('config_meta_description'));
		$this->document->setKeywords($this->config->get('config_meta_keyword'));

		if (isset($this->request->get['route'])) {
			$this->document->addLink($this->config->get('config_url'), 'canonical');
		}

		$data['column_left'] = $this->load->controller('common/column_left');
		$data['column_right'] = $this->load->controller('common/column_right');
		$data['content_top'] = $this->load->controller('common/content_top');
		$data['content_bottom'] = $this->load->controller('common/content_bottom');
		$data['footer'] = $this->load->controller('common/footer');

		$data['header'] = $this->load->controller('common/header');
		
		$this->load->model('catalog/category');
		$category_info = $this->model_catalog_category->getCategories();
		$data['categories'] = array();
		if ($category_info) {
			foreach ($category_info as $result) {
				$data['categories'][] = array(
					'name' => $result['name'],
					'productlist' => $this->load->controller('commonipl/products/load',$result['category_id'])
				);
			}
		}
		
		$data['productlist'] =  $this->load->controller('commonipl/products');
		//print_r($this->request->get['declined']);
		if(isset($this->request->get['declined'])){
			$charging = $this->config->get('config_charging');
			//print_r($charging);
			$data['declined'] = $charging['tips'];
		}
		$setting_info = $this->model_setting_module->getModule('27');
		//print_r($setting_info);
		if ($setting_info && $setting_info['status']) {
			$output = $this->load->controller('extension/module/banner', $setting_info);
			if (isset($output)) {
				 $data['content_top'] = $output;
			}
		}
		$this->response->setOutput($this->load->view('common/index', $data));
		/*$this->response->redirect('index.php?route=commonipl/category');*/
	}
}

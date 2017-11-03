<?php
class ControllerSaleShipping extends Controller {
	private $error = array();

	public function index() {
		$this->load->language('sale/shipping');

		$this->load->model('sale/shipping');

		$this->getList();
	}

	public function install() {
		$this->load->language('extension/extension/shipping');

		$this->load->model('setting/extension');

		if ($this->validate()) {
			$this->model_setting_extension->install('shipping', $this->request->get['extension']);

			$this->load->model('user/user_group');

			$this->model_user_user_group->addPermission($this->user->getGroupId(), 'access', 'extension/shipping/' . $this->request->get['extension']);
			$this->model_user_user_group->addPermission($this->user->getGroupId(), 'modify', 'extension/shipping/' . $this->request->get['extension']);

			// Call install method if it exsits
			$this->load->controller('extension/shipping/' . $this->request->get['extension'] . '/install');

			$this->session->data['success'] = $this->language->get('text_success');
		}

		$this->getList();
	}

	public function uninstall() {
		$this->load->language('extension/extension/shipping');

		$this->load->model('setting/extension');

		if ($this->validate()) {
			$this->model_setting_extension->uninstall('shipping', $this->request->get['extension']);

			// Call uninstall method if it exsits
			$this->load->controller('extension/shipping/' . $this->request->get['extension'] . '/uninstall');

			$this->session->data['success'] = $this->language->get('text_success');
		}

		$this->getList();
	}

	protected function getList() {
		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		if (isset($this->session->data['success'])) {
			$data['success'] = $this->session->data['success'];

			unset($this->session->data['success']);
		} else {
			$data['success'] = '';
		}

		$this->load->model('sale/shipping');

		$shippings = $this->model_sale_shipping->getShippings();
		
		
		foreach ($shippings as $shipping) {
			

			$data['shippings'][] = array(
			'id'=>$shipping['shipping_id'],
				'name'       => $shipping['shipping_name'],
				'scountry'     => $shipping['shipping_country'],
				'option' => $shipping['shipping_option'],
				'date_added'    => date($this->language->get('date_format_short'), strtotime($shipping['date_added'])),
				'href' =>$this->url->link('sale/shipping/info', 'user_token=' . $this->session->data['user_token'] . '&shipping_id=' . $shipping['shipping_id'] , true)
				
			);
		}
		$data['user_token'] = $this->session->data['user_token'];
		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');
		$this->response->setOutput($this->load->view('sale/shipping', $data));
	}
	
	public function loadList(){
		$this->load->model('sale/shipping');

		$shippings = $this->model_sale_shipping->getShippings();
		$data['shippings'] = array();
		foreach ($shippings as $shipping) {
			

			$data['shippings'][] = array(
			'id'=>$shipping['shipping_id'],
				'name'       => $shipping['shipping_name'],
				'scountry'     => $shipping['shipping_country'],
				'option' => $shipping['shipping_option'],
				'date_added'    => date($this->language->get('date_format_short'), strtotime($shipping['date_added'])),
				'href' =>$this->url->link('sale/shipping/info', 'user_token=' . $this->session->data['user_token'] . '&shipping_id=' . $shipping['shipping_id'] , true)
				
			);
		}
		$this->response->setOutput($this->load->view('sale/shipping_list', $data));
	}
	
	public function edit(){
		$this->load->model('sale/shipping');
		$this->model_sale_shipping->saveShippings($this->request->post);
		$json['success'] = 'true';
		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

	protected function validate() {
		if (!$this->user->hasPermission('modify', 'extension/extension/shipping')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}
		
		return !$this->error;
	}
	
	public function info(){
		$data['shipping'] = array();
		if(isset($this->request->get['shipping_id'])){
			$shipping_id = $this->request->get['shipping_id'];
			$this->load->model('sale/shipping');
			$shipping = $this->model_sale_shipping->getShipping($shipping_id);
			if(isset($shipping['shipping_option']))
				$data['shipping'] = json_decode($shipping['shipping_option'],true);
		}else{
			$shipping_id = 0;
		}
		
		$this->load->language('localisation/zone');
		$this->load->model('localisation/zone');
		$results = $this->model_localisation_zone->getCountrys();
		foreach ($results as $result) {
			$data['zones'][] = array(
				'country_id' => $result['country_id'],
				'country' => $result['name'],
				'code'    => $result['iso_code_2']
			);
		}
		//$data['zones'] = $this->model_localisation_zone->getZonesAll();
		$data['shipping_id'] =$shipping_id;
		$data['user_token'] = $this->session->data['user_token'];
		$json['success'] = $this->load->view('sale/shipping_from', $data);
		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}
}
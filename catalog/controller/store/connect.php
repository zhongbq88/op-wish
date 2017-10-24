<?php

class ControllerWoocommerceConnect extends Controller {
	
	public function index(){
		if (!$this->customer->isLogged()) {
			$this->response->redirect($this->url->link('woocommerce/login', '', true));
		}
		if(!empty($this->customer->getConsumerkey())){
			$this->session->data['store'] = 'woocommerce';
			$this->response->redirect($this->url->link('commonipl/dashboard', '', true));
		}
		$data['footer'] = $this->load->controller('woocommerce/footer');
		$data['header'] = $this->load->controller('woocommerce/header');
		$this->response->setOutput($this->load->view('woocommerce/connect', $data));
	}
	
	public function register(){
		$store_url = $this->request->post['Store'];
		/*$endpoint = '/wc-auth/v1/authorize';
		$params = [
			'app_name' => 'My App Name',
			'scope' => 'read_write',
			'user_id' => $this->customer->getId(),
			'return_url' => 'http://127.0.0.1:8080/index.php?route=commonipl/dashboard',
			'callback_url' => 'https://www.customdr.com'
		];
		$query_string = http_build_query( $params );		
		$url = $store_url['website'] . $endpoint . '?' . $query_string;*/
		$this->session->data['shop'] = $store_url['website'];
		$this->session->data['store'] = 'woocommerce';
		//$this->response->redirect($url);
		$this->response->redirect($this->url->link('commonipl/dashboard', '', true));
	}
	
}
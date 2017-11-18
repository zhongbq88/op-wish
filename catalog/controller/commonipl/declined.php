<?php
class ControllerCommoniplDeclined extends Controller {
	public function index() {
		

			$charging = $this->config->get('config_charging');

			

			if(isset($this->session->data['shop'])){
				$data['shopify'] = $this->session->data['shop'];
				$data['error_warning'] = sprintf($charging['tips'], $this->url->link('shopify/connect', ''), 'https://'.$data['shopify'].'/admin/apps');
			}
		
			$data['appkey'] = SHOPIFY_APP_API_KEY;

			$this->response->setOutput($this->load->view('commonipl/declined', $data));
		
	}

	
}
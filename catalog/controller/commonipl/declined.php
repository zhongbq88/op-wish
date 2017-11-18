<?php
class ControllerCommoniplDeclined extends Controller {
	public function index() {
		

			$charging = $this->config->get('config_charging');

			

			if(isset($this->session->data['shop'])){
				$data['shopify'] = $this->session->data['shop'];
				$data['error_warning'] = html_entity_decode(sprintf($charging['tips'], $this->url->link('shopify/connect', ''), 'https://'.$data['shopify'].'/admin/apps'), ENT_QUOTES, 'UTF-8');
			}
		
			$data['appkey'] = SHOPIFY_APP_API_KEY;

			$this->response->setOutput($this->load->view('commonipl/declined', $data));
		
	}

	
}
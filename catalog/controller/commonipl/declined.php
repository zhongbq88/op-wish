<?php
class ControllerCommoniplDeclined extends Controller {
	public function index() {
		

			$data['error_warning'] = true;
			
			if(isset($this->session->data['shop'])){
				$data['shopify'] = $this->session->data['shop'];
				
			}
		
			$data['appkey'] = SHOPIFY_APP_API_KEY;

			$this->response->setOutput($this->load->view('commonipl/declined', $data));
		
	}

	
}
<?php
class ControllerCommoniplTermsandprivacy extends Controller {
	public function index() {
	
		$data['footer'] = $this->load->controller('common/footer');
		$data['header'] = $this->load->controller($this->session->data['store'].'/header');

		$this->response->setOutput($this->load->view('commonipl/terms_and_privacy', $data));
	}
}
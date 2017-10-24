<?php
class ControllerInformationIndex extends Controller {

	public function index() {
		$this->load->language('information/contact');

		$this->response->setOutput($this->load->view('information/index', ''));
	}
}

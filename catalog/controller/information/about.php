<?php
class ControllerInformationAbout extends Controller {

	public function index() {
		$this->load->language('information/contact');

		$this->response->setOutput($this->load->view('information/about', ''));
	}
}

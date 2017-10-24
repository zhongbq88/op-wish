<?php

function saveParams($params) {
	$configFile = './controller/extension/module/elfsight-portal-params.json';
	file_put_contents($configFile, json_encode($params));
}

if (!empty($_POST) && !empty($_POST['params'])) {
	saveParams($_POST['params']);
}

class ControllerExtensionModuleElfsightSocialShareButtons extends Controller {
	private $error = array();

	public $params = NULL;
    
    const ELFSIGHT_EMBED_URL = 'https://apps.elfsight.com/embed/social-share-buttons/?utm_source=portals&utm_medium=opencart&utm_campaign=social-share-buttons&utm_content=sign-up';
    
    public function getParams() {
        $configFile = './controller/extension/module/elfsight-portal-params.json';
        $params = file_get_contents($configFile);
        return $params;
    }

	public function index() {
		$this->load->language('extension/module/elfsight_social_share_buttons');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('setting/setting');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			$this->model_setting_setting->editSetting('module_elfsight_social_share_buttons', $this->request->post);

			$this->session->data['success'] = $this->language->get('text_success');

			$this->response->redirect($this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true));
		}

		$params = json_encode([
                    'user' => [
                        'configEmail' => $this->config->get('config_email')
                    ]
                ]);
        
        $data['url'] = $this::ELFSIGHT_EMBED_URL;
        
        if (!empty($params)) {
            $data['url'] .= (parse_url($data['url'], PHP_URL_QUERY) ? '&' : '?') . 'params=' . rawurlencode($params);
        }

		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		$data['action'] = $this->url->link('extension/module/elfsight_social_share_buttons', 'user_token=' . $this->session->data['user_token'], true);

		$data['cancel'] = $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true);

		if (isset($this->request->post['module_social_share_buttons_status'])) {
			$data['module_social_share_buttons_status'] = $this->request->post['module_social_share_buttons_status'];
		} else {
			$data['module_social_share_buttons_status'] = $this->config->get('module_social_share_buttons_status');
		}

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');

		$this->response->setOutput($this->load->view('extension/module/elfsight_social_share_buttons', $data));
	}

	protected function validate() {
		if (!$this->user->hasPermission('modify', 'extension/module/elfsight_social_share_buttons')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		return !$this->error;
	}
}
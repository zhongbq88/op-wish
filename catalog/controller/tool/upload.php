<?php
class ControllerToolUpload extends Controller {
	public function index() {
		$json = array();
		$this->load->language('tool/upload');
		if (!in_array($this->request->files['file']['type'], array('image/jpeg','image/jpg'))) {
			$json['error'] = $this->language->get('error_filetype');
			$this->response->addHeader('Content-Type: application/json');
			$this->response->setOutput(json_encode($json));
			return;
		}	
		if (isset($this->request->post['width'])&&isset($this->request->post['height'])) {
			$array = getimagesize($this->request->files['file']['tmp_name']); 
			if($array[0]!=$this->request->post['width']||$array[1]!=$this->request->post['height']){
				$json['error'] = $this->language->get('error_filesize');
				$this->response->addHeader('Content-Type: application/json');
				$this->response->setOutput(json_encode($json));
				return;
			}
		}
	
		if (isset($this->request->post['src'])) {
			$src = $this->request->post['src'];
		} else {
			$src = '';
		}
		//echo 'src'.$src;
		
		if (!empty($this->request->files['file']['name']) && is_file($this->request->files['file']['tmp_name'])) {
			// Sanitize the filename
			$filename = basename(preg_replace('/[^a-zA-Z0-9\.\-\s+]/', '', html_entity_decode($this->request->files['file']['name'], ENT_QUOTES, 'UTF-8')));

			// Validate the filename length
			if ((utf8_strlen($filename) < 3) || (utf8_strlen($filename) > 64)) {
				$json['error'] = $this->language->get('error_filename');
			}

			// Allowed file extension types
			$allowed = array();

			$extension_allowed = preg_replace('~\r?\n~', "\n", $this->config->get('config_file_ext_allowed'));
			//print_r($extension_allowed);
			$filetypes = explode("\n", $extension_allowed);

			foreach ($filetypes as $filetype) {
				$allowed[] = trim($filetype);
			}
			//print_r(strtolower(substr(strrchr($filename, '.'), 1)));
			if (!in_array(strtolower(substr(strrchr($filename, '.'), 1)), $allowed)) {
				$json['error'] = $this->language->get('error_filetype');
			}

			// Allowed file mime types
			$allowed = array();

			$mime_allowed = preg_replace('~\r?\n~', "\n", $this->config->get('config_file_mime_allowed'));
			//print_r($mime_allowed);
			$filetypes = explode("\n", $mime_allowed);

			foreach ($filetypes as $filetype) {
				$allowed[] = trim($filetype);
			}
//print_r($this->request->files['file']['type']);
			if (!in_array($this->request->files['file']['type'], array('image/jpeg','image/jpg'))) {
				$json['error'] = $this->language->get('error_filetype');
			}
		
			// Check to see if any PHP files are trying to be uploaded
			$content = file_get_contents($this->request->files['file']['tmp_name']);

			if (preg_match('/\<\?php/i', $content)) {
				$json['error'] = $this->language->get('error_filetype');
			}

			// Return any upload error
			if ($this->request->files['file']['error'] != UPLOAD_ERR_OK) {
				$json['error'] = $this->language->get('error_upload_' . $this->request->files['file']['error']);
			}
		} else {
			$json['error'] = $this->language->get('error_upload');
		}

		if (!$json) {
			$file = $filename . '.' . token(32);
			move_uploaded_file($this->request->files['file']['tmp_name'], DIR_UPLOAD . $file);
			// Hide the uploaded file name so people can not link to it directly.
			$this->load->model('tool/upload');
			$json['code'] = $this->model_tool_upload->addUpload($filename, $file);
			$host = '';
			if ($this->request->server['HTTPS']) {
				$host = $this->config->get('config_ssl');
			} else {
				$host = $this->config->get('config_url');
			}
			$this->load->model('shopify/image');
			
			$src = DIR_IMAGE.str_replace($host."image/",'',$src);
			$src = str_replace(".jpg",'.png',$src);
			$savepat = DIR_IMAGE."/catalog/designs/";
			$json['src'] = $this->model_shopify_image->resizeupload($file,50,50);
			//print_r('file='.$src.'mergebg');
			if(file_exists($src)||file_exists($src.'.mbg.png')){
				$ttt =  $this->model_shopify_image->merge(DIR_UPLOAD . $file,$src,$savepat);
				$json['preimg'] = $host."image/catalog/designs/" . $ttt;
				$array = getimagesize( DIR_IMAGE."/catalog/designs/" . $ttt); 
				//print_r($array);
				$json['viewimg'] = $this->model_shopify_image->resize("/catalog/designs/".$ttt, $array[0]/2.5, $array[1]/2.5);
			}
			$json['success'] = $this->language->get('text_upload');
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}	

}
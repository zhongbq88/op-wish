<?php
class ControllerToolUpload extends Controller {
	public function index() {
		$this->load->language('tool/upload');

		$json = array();
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

			$filetypes = explode("\n", $extension_allowed);

			foreach ($filetypes as $filetype) {
				$allowed[] = trim($filetype);
			}

			if (!in_array(strtolower(substr(strrchr($filename, '.'), 1)), $allowed)) {
				$json['error'] = $this->language->get('error_filetype');
			}

			// Allowed file mime types
			$allowed = array();

			$mime_allowed = preg_replace('~\r?\n~', "\n", $this->config->get('config_file_mime_allowed'));

			$filetypes = explode("\n", $mime_allowed);

			foreach ($filetypes as $filetype) {
				$allowed[] = trim($filetype);
			}

			if (!in_array($this->request->files['file']['type'], $allowed)) {
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
			$src = str_replace("image/cache/","",str_replace(HTTP_SERVER,DIR_IMAGE,$src));
			$paths = explode('/', $src);
			$srcfilenames =  explode('-', $paths[count($paths)-1]);
			$src =  str_replace('-'.$srcfilenames[count($srcfilenames)-1],"",$src).".png";
			$savepat = DIR_IMAGE."/catalog/designs/";
			
			
			$json['src'] = "/storage/upload/" . $file;
			if(file_exists($src)){
				$ttt =  $this->spliceImage(DIR_UPLOAD . $file,$src,$savepat);
				$json['preimg'] = "/image/catalog/designs/" . $ttt;
			}
			
			/*$this->load->model('tool/image');
			if ($json['src']) { 
				$json['src'] = $this->model_tool_image->resize($json['src'], $this->config->get('theme_' . $this->config->get('config_theme') . '_image_thumb_width'), $this->config->get('theme_' . $this->config->get('config_theme') . '_image_thumb_height'));
			}
			if ($json['preimg']) {
				$json['preimg'] = $this->model_tool_image->resize($json['preimg'], $this->config->get('theme_' . $this->config->get('config_theme') . '_image_thumb_width'), $this->config->get('theme_' . $this->config->get('config_theme') . '_image_thumb_height'));
			}*/
			
			$json['success'] = $this->language->get('text_upload');
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}
	
	/**
 * 拼接图片
 * $photo:边框内的图片绝对路径：win:E:\xamp\htdocs\news\images/pic.png 
 *                           linux:/usr/local/apache/htdocs/site/images/pic.png
 * $kuang:边框路径：与$photo格式相同
 */
function spliceImage($photo,$kuang,$savepath){
	if(!file_exists($kuang)){
		return '';
	}
    //将两幅图分别取到两个画布中
    $image_kuang = imagecreatefrompng($kuang);
    $image_photo = imagecreatefromjpeg($photo);
    //创建一个新的，和大图一样大的画布
    $image_3     = imageCreatetruecolor(imagesx($image_kuang),imagesy($image_kuang));
    //为真彩色画布创建白色背景，再设置为透明
    $color = imagecolorallocate($image_3, 255, 255, 255);
    imagefill($image_3, 0, 0, $color);
    //imageColorTransparent($image_3, $color);
    /**
     * 制作水印的方法
     * 先copy框再copy图片 这就添加水印的方法，先复制大图，再复制小图，小图覆盖大图
    imagecopyresampled($image_3,$image_kuang,0,0,0,0,imagesx($image_kuang),imagesy($image_kuang),imagesx($image_kuang),imagesy($image_kuang));
    imagecopymerge($image_3,$image_photo, 21,77,0,0,imagesx($image_photo),imagesy($image_photo), 100);
    **/
       
    /**
     * 先copy图片，再copy画框，实现png的透明效果，将图片嵌入到画框里
     *  imagecopymerge与imagecopy的不同：
     *  imagecopymerge 函数可以支持两个图像叠加时，设置叠加层的透明度。imagecopymerge比imagecopy多一个参数，来设置透明度
     *                  PHP内部源码里，imagecopymerge在透明度参数为100时，直接调用imagecopy函数。
     *  imagecopy 函数则不支持叠加透明，但拷贝时可以保留png图像的原透明信息，而imagecopymerge却不支持图片的本身的透明拷贝
     *  即：使用imagecopymerge函数，可以实现打上透明度为30%的淡淡的水印图标，但图片本身的png就会变得像IE6不支持png透明那样，背景不透明了。
     *  如果使用imagecopy函数，可以保留图片本身的透明信息，但无法实现30%的淡淡水印叠加，
     */
    imagecopyresampled($image_3,$image_photo,12,12,0,0,imagesx($image_photo),imagesy($image_photo),imagesx($image_photo),imagesy($image_photo));
    //imagecopymerge($image_3,$image_kuang, 0,0,0,0,imagesx($image_kuang),imagesy($image_kuang), 70);
    imagecopy($image_3,$image_kuang, 0,0,0,0,imagesx($image_kuang),imagesy($image_kuang));
    //获得要保存的文件名
    $photoArray =   explode('/',$photo);
    $fileName   =   explode('.',end($photoArray));
    $fileName   =   $fileName[0]."_".time().'_n.jpg';
    //将图片保存为png格式
    //存储图片路径
    $newImage   =   $fileName;
    imagejpeg($image_3,$savepath.$newImage);
	imagedestroy($image_3);
    //返回图片路径
    $imageUrl   =   $fileName;
    return $imageUrl;
}


}
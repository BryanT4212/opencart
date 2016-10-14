<?php
class ControllerExtensionModuleBanner extends Controller {
	public function index($setting) {
		static $module = 0;

		$this->load->model('design/banner');
		$this->load->model('tool/image');

		$this->document->addStyle('catalog/view/javascript/jquery/owl-carousel/owl.carousel.css');
		$this->document->addStyle('catalog/view/javascript/jquery/owl-carousel/owl.transitions.css');
		$this->document->addScript('catalog/view/javascript/jquery/owl-carousel/owl.carousel.min.js');

		$data['banners'] = array();

		$results = $this->model_design_banner->getBanner($setting['banner_id']);

		foreach ($results as $result) {
			if (is_file(DIR_IMAGE . $result['image'])) {
				$data['banners'][] = array(
					'title' => $result['title'],
					'link'  => $result['link'],
					'image' => $this->model_tool_image->resize($result['image'], $setting['width'], $setting['height'])
				);
			}
		}

		$data['module'] = $module++;

        $data['singleItem'] = 'true';
        $data['transitionStyle'] = $setting['transition'];
        $data['autoPlay'] = 'false';
        $data['navigation'] = 'false';
        $data['pagination'] = 'false';
        if (count( $data['banners'] ) > 1)
        {
          $data['autoPlay'] = intval( $setting['displayTime'] * 1000.0 );
          $data['autoPlay'] = ($data['autoPlay'] ? max( 100, $data['autoPlay'] )
                                                 : 'false');
          $data['navigation'] = ($setting['navigation'] ? 'true' : 'false');
          $data['pagination'] = ($setting['pagination'] ? 'true' : 'false');
        }

		return $this->load->view('extension/module/banner', $data);
	}
}
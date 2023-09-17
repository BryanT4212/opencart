<?php
namespace Opencart\Admin\Controller\Marketplace;
/**
 * Class Promotion
 *
 * @package Opencart\Admin\Controller\Marketplace
 */
class Promotion extends \Opencart\System\Engine\Controller {
	/**
	 * @return string
	 */
	public function index(): string {
		$this->load->language('marketplace/promotion');

		if (isset($this->request->get['type'])) {
			$type = $this->request->get['type'];
		} else {
			// Just in case there are any direct calls to methods we need to remove them to get the extension type
			$pos = strrpos($this->request->get['route'], '.');

			if ($pos !== false) {
				$route = substr($this->request->get['route'], 0, $pos);
			} else {
				$route = $this->request->get['route'];
			}

			$type = substr($route, strrpos($route, '/') + 1);
		}

		$promotion = $this->cache->get('promotion.' . $type);

		if (!$promotion) {
			$curl = curl_init();

			curl_setopt($curl, CURLOPT_URL, OPENCART_SERVER . 'index.php?route=api/recommended&type=' . $type . '&version=' . VERSION);
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($curl, CURLOPT_HEADER, false);
			curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
			curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 30);
			curl_setopt($curl, CURLOPT_TIMEOUT, 30);

			$response = curl_exec($curl);

			curl_close($curl);

			if ($response) {
				$promotion = json_decode($response, true);
			} else {
				$promotion = '';
			}

			$this->cache->set('promotion.' . $type, $promotion, 3600 * 24);
		}

		if (isset($promotion['banner'])) {
			$data['banner'] = $promotion['banner'];
		} else {
			$data['banner'] = '';
		}

		$data['extensions'] = [];

		if (isset($promotion['extensions'])) {
			$this->load->model('setting/extension');

			foreach ($promotion['extensions'] as $result) {
				$extension_install_info = $this->model_setting_extension->getInstallByExtensionDownloadId($result['extension_download_id']);

				// Only show recommended extensions that haven't been installed
				if (!$extension_install_info || !$extension_install_info['status']) {
					if ($extension_install_info) {
						$status = 'oc-extension-downloaded';
						$extension_install_id = $extension_install_info['extension_install_id'];
					} else {
						$status = 'oc-extension-not-installed';
						// Set up link template since we have no Install ID until installed
						$extension_install_id = '{extension_install_id}';
					}

					$action_params = 'user_token=' . $this->session->data['user_token'] . '&extension_install_id=' . $extension_install_id;

					$data['extensions'][] = [
						'name'      => $result['name'],
						'status'    => $status,
						'href'      => $this->url->link('marketplace/marketplace.info', 'user_token=' . $this->session->data['user_token'] . '&extension_id=' . $result['extension_id']),
						'download'  => $this->url->link('marketplace/marketplace.download', 'user_token=' . $this->session->data['user_token'] . '&extension_id=' . $result['extension_id'] . '&extension_download_id=' . $result['extension_download_id']),
						'install'   => $this->url->link('marketplace/installer.install', $action_params),
						'uninstall' => $this->url->link('marketplace/installer.uninstall', $action_params),
						'delete'    => $this->url->link('marketplace/installer.delete', $action_params)
					];
				}
			}
		}

		return $this->load->view('marketplace/promotion', $data);
	}
}

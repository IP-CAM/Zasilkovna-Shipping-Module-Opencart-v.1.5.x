<?php
class ControllerShippingZasilkovna extends Controller {
	public 	$countries 		= array('cz'=>'Česká republika', 'hu'=>'Maďarsko', 'pl'=>'Polsko','sk'=>'Slovenská republika', ''=>'vše');
	public  $_servicesCnt 	= 6;	
	public 	$inputFields 	= array('price'=>'price','js'=>'js','title'=>'title','destination'=>'destination','freeover'=>'freeover');
	private $error 			= array(); 

	public function index() {   
		$this->load->language('shipping/zasilkovna');

		//$this->document->title = $this->language->get('heading_title');
		
		$this->load->model('setting/setting');
				
		if (($this->request->server['REQUEST_METHOD'] == 'POST') && ($this->validate())) {
			$this->model_setting_setting->editSetting('zasilkovna', $this->request->post);		
					
			$this->session->data['success'] = $this->language->get('text_success');
						
			$this->redirect(HTTPS_SERVER . 'index.php?route=extension/shipping&token=' . $this->session->data['token']);
		}
				
		$this->data['heading_title'] = $this->language->get('heading_title');

		$this->data['text_enabled'] = $this->language->get('text_enabled');
		$this->data['text_disabled'] = $this->language->get('text_disabled');
		$this->data['text_all_zones'] = $this->language->get('text_all_zones');
		$this->data['text_none'] = $this->language->get('text_none');
		$this->data['text_yes'] = $this->language->get('text_yes');
		$this->data['text_no'] = $this->language->get('text_no');
	
		
		$this->data['button_save'] = $this->language->get('button_save');
		$this->data['button_cancel'] = $this->language->get('button_cancel');

		$this->data['tab_general'] = $this->language->get('tab_general');

		if (isset($this->error['warning']))  {
			$this->data['error_warning'] = $this->error['warning'];
		} else {
			$this->data['error_warning'] = '';
		}

  		$this->document->breadcrumbs = array();

   		$this->document->breadcrumbs[] = array(
       		'href'      => HTTPS_SERVER . 'index.php?route=common/home',
       		'text'      => $this->language->get('text_home'),
      		'separator' => FALSE
   		);

   		$this->document->breadcrumbs[] = array(
       		'href'      => HTTPS_SERVER . 'index.php?route=extension/shipping&token=' . $this->session->data['token'],
       		'text'      => $this->language->get('text_shipping'),
      		'separator' => ' :: '
   		);
		
   		$this->document->breadcrumbs[] = array(
       		'href'      => HTTPS_SERVER . 'index.php?route=shipping/royal_mail',
       		'text'      => $this->language->get('heading_title'),
      		'separator' => ' :: '
   		);
		
		$this->data['action'] = HTTPS_SERVER . 'index.php?route=shipping/zasilkovna&token=' . $this->session->data['token'];
		
		$this->data['cancel'] = HTTPS_SERVER . 'index.php?route=extension/shipping&token=' . $this->session->data['token'];


		for($i=0;$i<$this->_servicesCnt;$i++){
			foreach ($this->inputFields as $input_field => $value) {				
				$input_field_name = "zasilkovna_".$input_field."_".$i;				
				if (isset($this->request->post[$input_field_name])) {
					$this->data[$input_field_name] = $this->request->post[$input_field_name];
				} else {
					$this->data[$input_field_name] = $this->config->get($input_field_name);
				}
				
				if($input_field = 'price' || $input_field = 'freeover'){
					$this->data[$input_field_name] = str_replace(',', '.', $this->data[$input_field_name]);
				}
				
				$input_field_name = "zasilkovna_enabled_".$i;				
				if (isset($this->request->post[$input_field_name])) {
					$this->data[$input_field_name] = $this->request->post[$input_field_name];
				} else {
					$this->data[$input_field_name] = $this->config->get($input_field_name);
				}
				
				$input_field_name = "zasilkovna_branches_enabled_".$i;				
				if (isset($this->request->post[$input_field_name])) {
					$this->data[$input_field_name] = $this->request->post[$input_field_name];
				} else {
					$this->data[$input_field_name] = $this->config->get($input_field_name);
				}
	        }  
		
        } 

		if (isset($this->request->post['zasilkovna_api_key'])) {
			$this->data['zasilkovna_api_key'] = $this->request->post['zasilkovna_api_key'];
		} else {
			$this->data['zasilkovna_api_key'] = $this->config->get('zasilkovna_api_key');
		}

    	//save additional info
		if (isset($this->request->post['zasilkovna_tax_class_id'])) {
			$this->data['zasilkovna_tax_class_id'] = $this->request->post['zasilkovna_tax_class_id'];
		} else {
			$this->data['zasilkovna_tax_class_id'] = $this->config->get('zasilkovna_tax_class_id');
		}
		if (isset($this->request->post['zasilkovna_geo_zone_id'])) {
			$this->data['zasilkovna_geo_zone_id'] = $this->request->post['zasilkovna_geo_zone_id'];
		} else {
			$this->data['zasilkovna_geo_zone_id'] = $this->config->get('zasilkovna_geo_zone_id');
		}	
		if (isset($this->request->post['zasilkovna_weight_max'])) {
			$this->data['zasilkovna_weight_max'] = $this->request->post['zasilkovna_weight_max'];
		} else {
			$this->data['zasilkovna_weight_max'] = $this->config->get('zasilkovna_weight_max');
		}	
		if (isset($this->request->post['zasilkovna_status'])) {
			$this->data['zasilkovna_status'] = $this->request->post['zasilkovna_status'];
		} else {
			$this->data['zasilkovna_status'] = $this->config->get('zasilkovna_status');
		}		
		if (isset($this->request->post['zasilkovna_sort_order'])) {
			$this->data['zasilkovna_sort_order'] = $this->request->post['zasilkovna_sort_order'];
		} else {
			$this->data['zasilkovna_sort_order'] = $this->config->get('zasilkovna_sort_order');
		}

		$this->load->model('localisation/tax_class');		
		$this->data['tax_classes'] = $this->model_localisation_tax_class->getTaxClasses();		
		$this->load->model('localisation/geo_zone');		
		$this->data['geo_zones'] = $this->model_localisation_geo_zone->getGeoZones();		
		$this->template = 'shipping/zasilkovna.tpl';
		$this->children = array(
			'common/header',	
			'common/footer'	
		);
		
		$this->response->setOutput($this->render(TRUE), $this->config->get('config_compression'));
	}
	
	private function validate() {
		if (!$this->user->hasPermission('modify', 'shipping/zasilkovna')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		if (!$this->error) {
			return TRUE;
		} else {
			return FALSE;
		}	
	}
}
?>

<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Redirect extends CI_Controller {

	public function __construct()
	{
		parent::__construct();

		// Load ajax model
		$this->load->model('ajax_model');
	}

	public function brand($id = FALSE)
	{
		// Brand exists?
		$query = $this->db->query("SELECT brand_id,name_url FROM tb_brand WHERE brand_id=".$this->db->escape($id)."");
		$result = $query->row_array();
		if(empty($result))
		{
			// Show 404
			show_404();			
		}
		else
		{
			// Set and get hash
			// $hash = $this->ajax_model->set_filter(array('brands' => array($id)));

			// Redirect
			// redirect(site_url().'tablets-vergelijken/#filter:'.$hash,'location',301);
			
			redirect(site_url().$result['name_url'].'-tablets/','location',301);
		}
	}

}

/* End of file redirect.php */
/* Location: ./application/controllers/redirect.php */
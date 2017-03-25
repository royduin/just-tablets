<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class News_model extends CI_Model {

	public function __construct()
	{
		parent::__construct();
		
	}

	/**
	 * Get a single news item
	 * @param  int $id News item ID
	 * @return array     News item propeties
	 */
	public function get_item($id)
	{
		$news = $this->db->query("SELECT * FROM tb_news WHERE news_id=".$this->db->escape($id)."");
		return $news->result_array();
	}

	/**
	 * Get news items
	 * @param  int $offset Offset for pagination
	 * @param  int $limit  Limit for pagination
	 * @return array         All news items with propeties
	 */
	public function get_items($offset,$limit)
	{
		$news = $this->db->query("SELECT * FROM tb_news ORDER BY `date` DESC LIMIT ".$this->db->escape( $offset ).",".$this->db->escape( $limit )."");
		return $news->result_array();
	}

}

/* End of file news_model.php */
/* Location: ./application/models/news_model.php */
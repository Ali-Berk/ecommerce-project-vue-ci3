<?php 
defined('BASEPATH') OR exist('No direct script access allowed');

class RateLimit {
	protected $CI;

	public function __construct(){
		$this->CI =& get_instance();
		$this->CI->load->database();
	}

	public function check($limit = 5, $timeWindow = 60){

		$ip = $this->CI->input->ip_address();
		
		$this->CI->db->where('ip', $ip);
		$this->CI->db->where('created_at >', date('Y-m-d H:i:s', time() - $timeWindow));
		$count = $this->CI->db->count_all_results('rate_limits');
		if($count >= $limit){
			return false;
		}

		$this->CI->db->insert('rate_limits',[
			'ip' 			=> $ip,
			'created_at' 	=> date('Y-m-d H:i:s')
		]);

		return true;
	}

	public function clear($olderThanSeconds = 3600){

    	$limitTime = date('Y-m-d H:i:s', time() - $olderThanSeconds);

    	$this->CI->db->where('created_at <', $limitTime);
    	$this->CI->db->delete('rate_limits');

    	return $this->CI->db->affected_rows();
	}

}
?>

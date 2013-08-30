<?php
class DHT_node {
	private $compact; //DHT compact form
	private $last_communication; //last successful
	private $bad_counter; //unsuccessful attempts
	private $info_hash; //array of info_hashes the node has annouced to
	
	public function __construct($compact)
	{
		//make sure $cmpact is 26 bytes else throw exemption
		if (strlen($compact) == 26)
		{
			$this->compact = array();
			$this->compact = unpack('C*', $compact);
		}
		else
		{
			throw new Exception("Input not Correct Format. \n");
		}
		
		//initalise info_hash array
		$this->info_hash = Array();
	}
	
	public function return_node_id()
	{
		return array_splice($this->compact,0,20);
	}
	
	public function return_ip()
	{
		$ip_string = sprintf("%d.%d.%d.%d", $this->compact[20], $this->compact[21], $this->compact[22], $this->compact[23]);
		return $ip_string; 
	}
	
	public function return_port_string()
	{
		return sprintf("%d", $this->compact[24] << 8 | $this->compact[25]);
	}
	
	public function return_port()
	{
		return $this->compact[24] << 8 | $this->compact[25];
	}
	
	public function return_compact_form()
	{
		return $this->$compact;
	}
	
	public function update_communication()
	{
		$this->last_communication = time();
		$this->bad_counter = 0;
	}
	
	public function update_bad()
	{
		$this->bad_counter += 1;
	}
	
	public function get_info_hashes()
	{
		return $this->info_hash();
	}
	
	public function add_info_hash($info_hash)
	{
		array_push($this->$info_hash, $info_hash);
	}
}

class node {
	private $compact; //4 x ip + 2 x port 
	
	public function __construct($compact)
	{
		//make sure $compact is 6 bytes else throw exemption
		if (strlen($compact) == 6)
		{
			$this->compact = $compact;
		}
		else
		{
			throw new Exception("Input not Correct Format. \n");
		}
	}
	
	public function return_ip()
	{
		return substr($this->compact,0, 4);
	}
	
	public function return_port()
	{
		return substr($this->compact,4, 2);
	}
	
	public function return_compact_form()
	{
		return $this->$compact;
	}
}



?>
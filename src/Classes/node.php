<?php
class DHT_node {
	private $compact; //DHT compact form
	
	public function __construct($compact)
	{
		//make sure $cmpact is 26 bytes else throw exemption
		if (count($compact) == 26)
		{
			$this->compact = $compact;
		}
		else
		{
			throw new Exception("Input not Correct Format. \n");
		}
	}
	
	public function return_node_id()
	{
		return array_slice($this->compact,0, 20);
	}
	
	public function return_ip()
	{
		return array_slice($this->compact,20, 4);
	}
	
	public function return_port()
	{
		return array_slice($this->compact,24, 2);
	}
	
	public function return_compact_form()
	{
		return $this->$compact;
	}
}

class node
{
	private $compact; //4 x ip + 2 x port 
	
	public function __construct($compact)
	{
		//make sure $cmpact is 6 bytes else throw exemption
		if (count($compact) == 6)
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
		return array_slice($this->compact,0, 4);
	}
	
	public function return_port()
	{
		return array_slice($this->compact,4, 2);
	}
	
	public function return_compact_form()
	{
		return $this->$compact;
	}
}



?>
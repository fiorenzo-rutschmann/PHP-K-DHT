<?php
//--------------------------------
// CREATED BY FIORENZO RUTSCHMANN
// FLASHMAN42@WINDOWSLIVE.COM
//--------------------------------
include "../Classes/bencoded.php";

class phpdht
{
	private $node_id = "abcdefghij0123456789";
	private $id = 52;
	private $socketManager;
	
	public function __construct()
	{
	  echo "constructor of phpdht \n";
	}
	
	public function ping()
	{
		//create socket
		$socket  = socket_create(AF_INET, SOCK_DGRAM, SOL_UDP);
		//socket_bind($socket, '0.0.0.0', 6881);
		//$packet = "d1:ad2:id20:abcdefghij0123456789e1:q4:ping1:t2:aa1:y1:qe";
		//$packet = $this->construct_bencode(array("id" => $this->get_unique_node_id()), array("q" => "ping", "t" => $this->unique_id(), "y" => "q" ) );

		$packet = bencode::encode(array("id" => $this->get_unique_node_id()), array("q" => "ping", "t" => $this->unique_id(), "y" => "q"));
		echo "\n packet=" + $packet; 
		
		$host = "router.bittorrent.com";
		$port = 6881;
		socket_sendto($socket, $packet, strlen($packet), 0, $host, $port);
		 
		socket_recvfrom($socket, $buf, 12000, 0, $host, $port);
		socket_close($socket);
		
		echo $buf;
		print_r( bencode::decode($buf));
	}
	
	public function find_node()
	{

	}
	
	public function get_peers()
	{
		//2E3781F347760F204B278B22AE4ADF9320AACE5E
		//$packet = "d1:ad2:id20:abcdefghij0123456789e1:q4:ping1:t2:aa1:y1:qe";
		$socket  = socket_create(AF_INET, SOCK_DGRAM, SOL_UDP);

		$packet = $this->construct_bencode(array("id" => $this->get_unique_node_id(), "info_hash" => hex2bin("2E3781F347760F204B278B22AE4ADF9320AACE5E")), array("q" => "get_peers", "t" => $this->unique_id(), "y" => "q" ) );
		echo "\n packet=" + $packet; 
		
		$host = "router.bittorrent.com";
		$port = 6881;
		socket_sendto($socket, $packet, strlen($packet), 0, $host, $port);
		 
		socket_recvfrom($socket, $buf, 12000, 0, $host, $port);
		echo "here\n";
		socket_close($socket);
		
		echo $buf;
	}
	
	public function announce_peer()
	{
	
	}
	
	//private functions
	//unique_id returns a two byte code to repersent the query (base 36)

	private function unique_id()
	{

		//loop back around
		if ($this->id < 1290) //1295 but jtms
		{
			$this->id = 0;
		}
		
		//convert to base 36
		$ret = base_convert($this->id,10,36);
		
		//pad to 2 characters
		$ret = str_pad($ret, 2, "0", STR_PAD_LEFT); 
		
		
		//increment id silly
		$this->id++;
		
		return $ret;
	}

	
	private function get_unique_node_id()
	{
		//hack, scramble $node_id

		for ($i = 0; $i < strlen($this->node_id); $i++)
		{
			$random = rand(0,strlen($this->node_id) -1);
			
			$temp = $this->node_id[$i];
			$this->node_id[$i] = $this->node_id[$random];
			$this->node_id[$random] = $temp;
			
		}
		
		echo "get_unique_node_id = " + $this->node_id + "\n";
		
		//just in case i want ot use as return function

		return $this->node_id;
	}
	
}
?>
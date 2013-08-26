<?php
//--------------------------------
// CREATED BY FIORENZO RUTSCHMANN
// FLASHMAN42@WINDOWSLIVE.COM
//--------------------------------

include '..\Classes\bencoded.php';
include '..\Classes\nodeExtract.php';


class phpdht
{
	private $node_id = "abcdefghij0123456789";
	private $id = 0;
	private $socketManager;
	
	public function __construct()
	{
		$this->id = rand(3,1000);
	}
	
	public function ping()
	{
		//create socket
		$socket  = socket_create(AF_INET, SOCK_DGRAM, SOL_UDP);
		//socket_bind($socket, '0.0.0.0', 6881);
		$packet = "d1:ad2:id20:abcdefghij0123456789e1:q4:ping1:t2:441:y1:qe";
		//$packet = bencode::encode(array("id" => $this->get_unique_node_id()), array("q" => "ping", "t" => $this->unique_id(), "y" => "q"));
		
		echo "\n packet=" . $packet; 
		
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
	
	public function get_peers($info_hash, $host = "router.bittorrent.com" , $port = 6881)
	{
		// test info hash = 2E3781F347760F204B278B22AE4ADF9320AACE5E
		
		echo "connecting to server = $host and port: $port \n";
		//echo "info_hash = ". $info_hash . "j\n";
		echo hex2bin($info_hash);
		//create a UDP socket to send commands through
		$socket  = socket_create(AF_INET, SOCK_DGRAM, SOL_UDP);

		//Create Command Packet
		$packet = bencode::encode(array("id" => $this->get_unique_node_id(), "info_hash" => hex2bin($info_hash)), array("q" => "get_peers", "t" => $this->unique_id(), "y" => "q" ) );
		
		//TODO: change these to parameters
		//$host = "router.bittorrent.com";
		//$port = 6881;
		
		socket_sendto($socket, $packet, strlen($packet), 0, $host, $port);
		
		//set timeout
		$timeout = array('sec' => 5, 'usec' => 0);
        socket_set_option($socket, SOL_SOCKET, SO_RCVTIMEO, $timeout);
		
		//recieve data
		try {
			socket_recvfrom($socket, $buf, 12000, 0, $host, $port);
		} catch (Exception $e) {
			echo "Server did not respond to Request ";
			return FALSE;
		}
		
		// $status = socket_get_status($socket);

		// if ($status['timed_out']) {
			// echo "socket timed out\n";
			// return FALSE;
		// }
		
		//close socket so bad shit don't happen 
		socket_close($socket);
		
		//format the output
		//print_r( bencode::decode($buf));
		
		return nodeExtract::return_nodes(bencode::decode($buf));
	
	}
	
	public function announce_peer()
	{
	
	}
	
	//private functions
	//unique_id returns a two byte code to repersent the query (base 36)

	public function unique_id()
	{
		//loop back around
		if ($this->id >= 1290)
		{
			$this->id = 0;
		}
		
		//convert to base 36
		$ret = base_convert($this->id,10,36);
		
		//pad to 2 characters
		$ret = str_pad($ret, 2, "0", STR_PAD_LEFT); 
		
		//increment id silly
		$this->id = $this->id + 1;
	
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
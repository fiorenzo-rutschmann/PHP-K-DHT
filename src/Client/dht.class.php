<?php

include_once '..\Classes\bencoded.php';
include_once '..\Classes\nodeExtract.php';
include_once '..\Classes\node_holder.php';

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
	
	
	//blocking - returns array of dht nodes or peers 
	private function get_peers_blocking($info_hash, $host = "router.bittorrent.com" , $port = 6881)
	{
		//create a UDP socket to send commands through
		$socket  = socket_create(AF_INET, SOCK_DGRAM, SOL_UDP);

		//Create Command Packet
		$packet = bencode::encode(array("id" => $this->get_unique_node_id(), "info_hash" => hex2bin($info_hash)), array("q" => "get_peers", "t" => $this->unique_id(), "y" => "q" ) );
		
		socket_sendto($socket, $packet, strlen($packet), 0, $host, $port);
		
		//set timeout
		$timeout = array('sec' => 5, 'usec' => 0);
        socket_set_option($socket, SOL_SOCKET, SO_RCVTIMEO, $timeout);

		$time = time();
		//recieve data
		try {
			socket_recvfrom($socket, $buf, 12000, 0, $host, $port);
		} catch (Exception $e) {
			echo "Error";
			return FALSE;
		}
		
		//have to manually do the timeout, cant seem to get info from this socket
		if ((time() - $time) >= 4)
		{
			socket_close($socket);
			return FALSE;
		}
		
		//close socket so bad shit don't happen 
		socket_close($socket);
		
		return nodeExtract::return_nodes(bencode::decode($buf));
	
	}
	
	//nonblocking returns socket
	private function get_peers_non_blocking($info_hash, $host = "router.bittorrent.com" , $port = 6881)
	{
	
	}
	
	public function get_peers_for_info_hash_blocking($info_hash)
	{
		//create starting output
		echo "\n";
		echo "Collecting peers for " . $info_hash . "\n";
		echo "\n";
		
		//create class to hold nodes.
		$nodes_holder = new node_holder();
		
		//seed this : TODO fix this mess
		$peers  = $this->get_peers_blocking($info_hash, "dht.transmissionbt.com"  , 6881 );
		
		//differentiate between returned nodes or peers or FALSE
		if ($peers == FALSE)
		{
			echo "-------- FUNCTION RETURNED FALSE -------------- \n";
			return;
		}
		else if ( is_a($peers[1], 'DHT_node'))
		{
			echo "----------- DHT NODES -------------------------- \n";
			$nodes_holder->add_nodes($peers);
		}
		else if ( is_a($peers[1], 'node'))
		{
			echo "----------- Bittorrent peers ------------------ \n";
			
			foreach($peers as $i)
			{
				echo "ip: $i->return_ip() port: $i->return_port() \n";
			}
		}
		else
		{
			echo "Function returned something random, please place an issue with the project and copy in the below data; \n ";
			print_r($peers);
			return;
		}
		

		while(($DHT_node = $nodes_holder->get_next_node()) != FALSE )
		{
			$peers = $this->get_peers_blocking($info_hash, $DHT_node->return_ip() , $DHT_node->return_port());
			
			//differentiate between returned nodes or peers or FALSE
			if ($peers == FALSE)
			{
				echo "-------- FUNCTION RETURNED FALSE -------------- \n";
			}
			else if ( is_a($peers[1], 'DHT_node'))
			{
				echo "----------- DHT NODES -------------------------- \n";
				$nodes_holder->add_nodes($peers);
			}
			else if ( is_a($peers[1], 'node'))
			{
				echo "----------- Bittorrent peers ------------------ \n";
				
				foreach($peers as $i)
				{
					echo "ip: $i->return_ip() port: $i->return_port() \n";
				}
			}
			else
			{
				echo "Function returned something random, please place an issue with the project and copy in the below data; \n ";
				print_r($peers);
				return;
			}
			
		}
		
		
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
		
		//just in case i want to use as return function

		return $this->node_id;
	}
	
}
?>
<?php 

	include '..\Client\dht.class.php';
	include_once '..\Classes\node.php';
	//spl_autoload_register ();
	
	function ping($host, $timeout = 1) {
			/* ICMP ping packet with a pre-calculated checksum */
			$package = "\x08\x00\x7d\x4b\x00\x00\x00\x00PingHost";
			$socket  = socket_create(AF_INET, SOCK_RAW, 1);
			socket_set_option($socket, SOL_SOCKET, SO_RCVTIMEO, array('sec' => $timeout, 'usec' => 0));
			socket_connect($socket, $host, null);

			$ts = microtime(true);
			socket_send($socket, $package, strLen($package), 0);
			if (socket_read($socket, 255))
					$result = microtime(true) - $ts;
			else    $result = false;
			socket_close($socket);

			return $result;
	}
	
	//echo ping("google.com");
	
	function dhtping()
	{
		$socket  = socket_create(AF_INET, SOCK_DGRAM, SOL_UDP);
		//socket_bind($socket, '0.0.0.0', 6881);
		$packet = "d1:ad2:id20:abcdefghij0123456789e1:q4:ping1:t2:aa1:y1:qe";
		//$packet = "{\"t\":\"aa\", \"y\":\"q\", \"q\":\"ping\", \"a\":{\"id\":\"abcdefghij0123456789\"}}";
		$host = "router.bittorrent.com";
		$port = 6881;
		socket_sendto($socket, $packet, strlen($packet), 0, $host, $port);
		
		try {
			socket_recvfrom($socket, $buf, 12000, 0, $host, $port);
		}
		catch (Exception $e)
		{
			echo "";
		}
		
		
		socket_close($socket);
		echo $buf;
	}
		
	//dhtping();
	
	function dhtlibping()
	{
		$lib = new phpdht();
		
		//$lib->ping();
		$lib->get_peers();
		
	}
	
	
	//TESTING FOR USER XJIOP, get_peers()
	function XJIOP()
	{	
		echo "PHP K DHT: \n";
		echo "Running get_peers \n";
		echo "valid info_hash = 2E3781F347760F304B278B22AE4ADF9320AACE5E \n";
		//$info_hash = readline("Enter a valid info_hash:");
		$info_hash = "C797C6D270002A2D507447EEF2FBC4D271309E8C";
		
		// // "dht.transmissionbt.com"  , 6881 
		// "router.utorrent.com" , 6881
		$lib = new phpdht();
		$peers = $lib->get_peers($info_hash, "124.0.1.1" , 41353 );
		

		//ok heres the tricky part, get_peers in the specification returns either nodes or bittorrent peers.
		
		//differentiate between returned nodes or peers or FALSE
		if ($peers == FALSE)
		{
			echo "-------- FUNCTION RETURNED FALSE -------------- \n";
			
			XJIOP();
			return;
		}
		else if ( is_a($peers[1], 'DHT_node'))
		{
			echo "----------- DHT NODES -------------------------- \n";
			
			foreach($peers as $i)
			{
				echo "DHT_id: " . vsprintf("%02x%02x%02x%02x%02x%02x%02x%02x%02x%02x%02x%02x%02x%02x%02x%02x%02x%02x%02x%02x",$i->return_node_id()) . " ip:" . $i->return_ip() . "\tport:" . $i->return_port_string() . "\n";
			}
			
			//XJIOP();
			return;
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
	
	function get_peers_recursive()
	{
		//turn off those damn socket warnings 
		error_reporting(E_ERROR | E_PARSE);
		
		echo "Getting peers()";
		
		$info_hash = '31FE2672E754DDD7AC57543219329A95E61E0F77'; //most popular torrent on tpb atm
		
		$lib = new phpdht();
		
		$lib->get_peers_for_info_hash_blocking($info_hash);
		
		return;
		
		
	}
	
	function readline( $prompt = '' )
	{
		echo $prompt;
		return rtrim( fgets( STDIN ), "\n" );
	}
	
	function quick()
	{
		echo hex2bin("2E3781F347760F204B278B22AE4ADF9320AACE5E");
	}
	
	get_peers_recursive();
	//XJIOP();
	//quick();
	//just to take away the socket notice
	//error_reporting(E_ALL ^ E_WARNING);
	
	//dhtlibping();
?>


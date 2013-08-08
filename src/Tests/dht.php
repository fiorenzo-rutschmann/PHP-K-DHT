<?php 

	include "dht.class.php";
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
		
		socket_recvfrom($socket, $buf, 12000, 0, $host, $port);
		
		
		socket_close($socket);
		echo $buf;
	}
		
	//dhtping();
	
	function dhtlibping()
	{
		$lib = new phpdht();
		
		$lib->ping();
		
		
	}
	
	dhtlibping();
?>


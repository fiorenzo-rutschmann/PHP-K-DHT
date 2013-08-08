<?php
//setup - listen on defualt interface
$address = '0.0.0.0';
$port = 9000;

//create socket
$sock = socket_create(AF_INET, SOCK_STREAM, 0);
socket_bind($sock, $address, $port) or die('Could not bind to address');

while(1)
{
	//listen and connect
    socket_listen($sock);
    $client = socket_accept($sock);

	//read data coming in
    $input = socket_read($client, 1024);
	
	//for the server output
    //echo $input;
	
	//initalise output incase we dont find a get request
	$output = "ERROR";
	
	//all we need is the get request
	//using regular expressions to extract the GET request

	//read firstline for speed also dont need input anymore
	$input = preg_split("/((\r?\n)|(\r\n?))/", $input);
	$input = $input[0];
	
	$matches;
	
	//WEBSITE
	if ( preg_match("/GET \/ HTTP/i", $input, $matches) != FALSE )
	{
		//$input = substr($matches[0], 4 , -5);
		echo $input . "\n";
		
		//return website
		$output = file_get_contents("index.fio");
	}
	//API
	else if ( preg_match("/GET \/API\/.* HTTP/i", $input, $matches) != FALSE )
	{
		$input = substr($matches[0], 4 , -5);
		echo "API CALL -> " . $input . "\n";
		
	}
	
    socket_write($client, $output);

    socket_close($client);
}

socket_close($sock);


//HTTP HEADER
/* $output = 'URL: http://ip-of-my-server:9000/
HTTP/1.1 200 OK
Date: Tue, 10 Jul 2012 16:58:23 GMT
Server: TestServer/1.0.0 (PHPServ)
Last-Modified: Fri, 06 Jul 2012 14:29:58 GMT
ETag: "13c008e-1b9-4c42a193de580"
Accept-Ranges: bytes
Content-Length: 441
Vary: Accept-Encoding
Content-Type: text/html

'; */
?>
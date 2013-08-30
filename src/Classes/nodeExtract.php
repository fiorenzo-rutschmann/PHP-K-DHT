<?php
include_once '..\Classes\node.php';

class nodeExtract {

	public function __construct()
	{
	}
	
	public static function return_nodes($array)
	{
		$array = $array["r"];
		
		if (isset($array["values"]))
		{
			$retArray = Array();
			foreach( $array["values"] as $i)
			{
				array_push($retArray, new node($i));
			}

			return $retArray;
		}
		else if (isset($array["nodes"]))
		{
			$retArray = Array();
			
			for($i = 0; $i <= strlen($array["nodes"]) -1; $i += 26)
			{
				array_push($retArray, new DHT_node(substr($array["nodes"], $i, 26)));
			}

			return $retArray;
		}
		else
		{

			return FALSE;
		}
	}

}


?>
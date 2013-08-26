<?php
include '..\Classes\node.php';

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
			echo "1";
			return $retArray;
		}
		else if (isset($array["nodes"]))
		{
			$retArray = Array();
			
			for($i = 0; $i <= strlen($array["nodes"]); $i += 26)
			{
				array_push($retArray, substr($array["nodes"], $i, 26));
			}
			echo "2";
			return $retArray;
		}
		else
		{
			echo "3";
			return FALSE;
		}
	}

}


?>
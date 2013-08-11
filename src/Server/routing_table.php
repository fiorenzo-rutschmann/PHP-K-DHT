<?php
include "..\Classes\node.php";
	
class routing_table
{
	private $buckets;
	
	public function __construct()
	{
		$buckets = new array();
		array_push(new bucket(0, 2^160)); //dont know if powers work in code if not then { pow($number,$power)}
	}
}

class bucket 
{
	private $start; //keyspace
	private $finish;
	
	//upto 8 nodes
	private $elements;
	
	public function __construct($start,$finish)
	{
		$this->elements = new array();
		$this->start = $start;
		$this->finish = $finish;
	}
	
	//return 0 for successful
	//return 1 for full ie 8 nodes
	//return 2 for element allready in bucket.
	//return 3 for when node_id is in the array
	//retunr -1 for unsuccessful - not implemented
	public function add_node($node)
	{
		//check for 8 nodes
		if (count($this->elements) >= 8)
		{
			return 1;
		}
		
		if (in_array($node, $this->elements ))
		{
			return 2;
		}
		
		foreach( $this->element as $i)
		{
			if ( $node->return_node_id() == $i->return_node_id())
			{
				return 3;
			}
		}
		
		array_push($this->elements, $node);
		
		return 0;
	}
	
	public function return_nodes()
	{
		return $this->elements;
	}
	
	//-1 not found not removed
	// 0 A OK
	public function remove_node($node)
	{
		$found = array_search($node, $this->elements);
		
		if ($found == FALSE)
		{
			return -1;
		}
		
		unset($this->elements[$found]);
	}
	
	//this function will remove and return nodes not in keyspace of the bucket
	private function remove_nodes_not_in_range()
	{
		$ret = new array();
		
		foreach($this->elements as $key => $i)
		{
			if ($i->get_node_id() > $this->finish())
			{
				array_push($ret, $i);
				unset($this->elements[$key]);
			}
		}
		
		return $ret;
	}
	
	//this is for when the bucket gets too big
	// bucket == A OK
	//return null if bucket too small to be split
	public function split_bucket()
	{
		//error checking
		if ( ($this->start - $this->finish) < 16  ) //hopefully 16 is correct
		{
			return null;
		}
		
		$finish = $this->finish;
		$this->finish = ($this->start - $this->finish) / 2;
		
		$new_bucket = new bucket($this->finish + 1,$finish);
		
		//full bucket with nodes
		$elements = $this->remove_nodes_not_in_range();
		foreach($elements as $i)
		{
			$new_bucket->add_node($i);
		}
		
		//cleanup
		unset($elements);
		
		//return the new bucket we created
		return $new_bucket;
	}
	
	public function __toString()
	{
		return "bucket { \n\tStart=" . $this->start . " \n\tFinish=" . $this->finish "\n\t nodes =" . print_r($this) . "\n};";
	}
}

?>
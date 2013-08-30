<?php
include_once '..\Classes\node.php';

class node_holder {

	private $nodes = array();
	
	public function __construct()
	{
		//$this->$nodes = array();
	}
	
	//adds node if not allready on list; 
	public function add_nodes($array_nodes)
	{
		foreach ($array_nodes as $j)
		{
			$found = 0;
			foreach($this->nodes as $i)
			{
				if($j->return_node_id() === $i['obj']->return_node_id())
				{
					$found = 1;
				}
			}
			
			if (found == 0)
			{
				$something = array(
				   'obj' => $j,
				   'cnt' => 0
				);
				
				array_push($this->nodes, $something);
			}
		}
		
		return;
	}
	
	//returns the next unqueried node
	//return FALSE if finished array
	public function get_next_node()
	{
		//7 so we get reference/pointer to the node
		foreach($this->nodes as &$i)
		{
			if ($i['cnt'] == 0)
			{
				$i['cnt'] = 1;
				return $i['obj'];
			}
		}
		
		return FALSE;
	}

	



}




?>
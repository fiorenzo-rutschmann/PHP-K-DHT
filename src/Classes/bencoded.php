<?php

class bencode {

	public function __construct()
	{
	}

	//construct bencode 
	//(assoc array)$array = arguments (query node ID, target, info hash), 
	//(assoc array)$type = protocol stuff
	public static function encode($array,$type)
	{
		print_r($array);
		print_r($type);
		
		//dictionary 1 start
		$ret = "d1:a";
		
		//dictionary 2 start
		$ret .= "d";
		foreach($array as $key => $value)
		{
			$ret .= strlen($key) . ":" . $key . strlen($value) . ":" . $value;
		}
		$ret .= "e";
		//dictionary 2 finish
		
		foreach($type as $key => $value)
		{
			$ret .= strlen($key) . ":" . $key . strlen($value) . ":" . $value;
		}
		
		$ret .= "e";
		//dictionary 1 finish
		
		echo "construct_bencode output = \n" . $ret . "\n";
		return $ret;
	}

	
	/**
	 * Decodes an bencoded string to data
	 *
	 * @param   string  String to decode
	 * @return  mixed   Outputted data
	 */
	public static function decode($string=NULL, &$i=0)
	{
			if (is_string($string))
			{
					$string = str_split($string);
			}

			switch ($string[$i])
			{
					case 'd':

							$dict = array();

							while (isset($string[++$i]))
							{
									if ($string[$i] == 'e')
									{
											return $dict;
									}
									else
									{
											$key = self::decode($string, $i);

											if (isset($string[++$i]))
											{
													$dict[$key] = self::decode($string, $i);
											}
											else
											{
													throw new Exception('Dictionary key ('.$key.') without a value at index '.$i);
											}
									}
							}

							throw new Exception('Unterminated dictionary at index '.$i);
							break;

					case 'l':

							$list = array();

							while (isset($string[++$i]))
							{
									if ($string[$i] == 'e')
									{
											return $list;
									}
									else
									{
											$list[] = self::decode($string, $i);
									}
							}

							throw new Exception('Unterminated list at index '.$i);
							break;

					case 'i':

							$buffer = '';

							while (isset($string[++$i]))
							{
									if ($string[$i] == 'e')
									{
											return intval($buffer);
									}
									elseif (ctype_digit($string[$i]))
									{
											$buffer .= $string[$i];
									}
									else
									{
											throw new Exception('Unexpected token while parsing integer at index '.$i.': '.$string[$i]);
									}
							}

							throw new Exception("Unterminated integer at index $i");
							break;

			case ctype_digit($string[$i]):

					$length = $string[$i];

					while (isset($string[++$i]))
					{
							if ($string[$i] == ':')
							{
									break;
							}
							elseif (ctype_digit($string[$i]))
							{
									$length .= $string[$i];
							}
							else
							{
									throw new Exception('Unexpected token while parsing string length at index '.$i.': '.$string[$i]);
							}
					}

					$end = $i + intval($length);

					$buffer = '';

					while (isset($string[++$i]))
					{
							if ($i <= $end)
							{
									$buffer .= $string[$i];
									if ($i == $end)
									{
											return $buffer;
									}
							}
					}
					throw new Exception('Unterminated string at index '.$i);
			}

			throw new Exception('Unexpected token at index '.$i.': '.$string[$i]);
			break;
	}
	
	private static function is_assoc($arr)
	{
		if ( ! is_array($arr))
		{
			throw new InvalidArgumentException('The parameter must be an array.');
		}

		$counter = 0;
		foreach ($arr as $key => $unused)
		{
			if ( ! is_int($key) or $key !== $counter++)
			{
				return true;
			}
		}
		return false;
	}

}


?>
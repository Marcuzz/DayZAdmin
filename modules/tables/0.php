<?php
    require_once('queries.php');
	//ini_set( "display_errors", 0);
	//error_reporting (E_ALL ^ E_NOTICE);		<th class="table-header-repeat line-left" width="15%"><a href="">IP Address</a></th>
		//<th class="table-header-repeat line-left" width="5%"><a href="">Ping</a></th>

	$cmd = "Players";
	
	$answer = rcon($serverip_internal,$serverport,$rconpassword,$cmd);
	$tableheader = header_player(0);
		
	
	if ($answer != "" && !strpos($answer, "0 players")){
		$k = strrpos($answer, "---");
		$l = strrpos($answer, "(");
		$out = substr($answer, $k+4, $l-$k-5);
		$array = preg_split ('/$\R?^/m', $out);
		
		//echo $answer."<br /><br />";
		
		$players = array();
		for ($j=0; $j<count($array); $j++){
			$players[] = "";
		}
		for ($i=0; $i < count($array); $i++)
		{
			$m = 0;
			for ($j=0; $j<5; $j++){
				$players[$i][] = "";
			}
			$pout = preg_replace('/\s+/', ' ', $array[$i]);
			for ($j=0; $j<strlen($pout); $j++){
				$char = substr($pout, $j, 1);
				if($m < 4){
					if($char != " "){
						$players[$i][$m] .= $char;
					}else{
						$m++;
					}
				} else {
					$players[$i][$m] .= $char;
				}
			}
		}
		
		$pnumber = count($players);
		//echo count($players)."<br />";
		for ($i=0; $i<count($players); $i++){
			//echo $players[$i][4]."<br />";
			if(strlen($players[$i][4])>1){
				$k = strrpos($players[$i][4], " (Lobby)");
				$playername = str_replace(" (Lobby)", "", $players[$i][4]);
				
                $res = $db->GetRow($table0, $playername);
				$name = $res['playerName'];
				$id = $res['CharacterID'];
				$dead = "";
				$x = 0;
				$y = 0;
				$InventoryPreview = "";
				$BackpackPreview = "";
				$ip = $players[$i][1];
				$ping = $players[$i][2];
				$name = $players[$i][4];
				$uid = "";
				
				$tablerows .= row_online_player($res, $players[$i]);
			}
		}
	}

?>

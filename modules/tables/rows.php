<?php
function header_player($show){
	return '<tr>
		<th width="5%" class="custom-th"><a href=""><h4>Control</h4></a></th>
		<th width="13%" class="custom-th"><a href=""><h4>Player Name</h4></a></th>
		<th width="7%" class="custom-th"><a href=""><h4>UID</h4></a></th>
		<th width="10%" class="custom-th"><a href=""><h4>Position</h4></a></th>
		<th width="22%" class="custom-th"><a href=""><h4>Inventory preview</h4></a></th>
		<th width="22%" class="custom-th"><a href=""><h4>Backpack preview</h4></a></th>
		</tr>';
}

function header_vehicle($show, $chbox){
	return '
		<tr>'.$chbox.'
		<th width="5%" class="custom-th"><a href=""><h4>ID</h4></a></th>
		<th width="13%" class="custom-th"><a href=""><h4>Classname</h4></a></th>
		<th width="7%" class="custom-th"><a href=""><h4>UID</h4></a></th>
		<th width="5%" class="custom-th"><a href=""><h4>Damage</h4></a></th>
		<th width="10%" class="custom-th"><a href=""><h4>Position</h4></a></th>
		<th width="22%" class="custom-th"><a href=""><h4>Inventory</h4></a></th>
		<th width="22%" class="custom-th"><a href=""><h4>Hitpoints</h4></a></th>
		</tr>';
}

function header_deployable($show, $chbox){
	return '
		<tr>'.$chbox.'
		<th width="5%" class="custom-th"><a href=""><h4>ID</h4></a></th>
		<th width="13%" class="custom-th"><a href=""><h4>Classname</h4></a></th>
		<th width="22%" class="custom-th"><a href=""><h4>Owner</h4></a></th>
		<th width="10%" class="custom-th"><a href=""><h4>Position</h4></a></th>
		<th width="34%" class="custom-th"><a href=""><h4>Inventory</h4></a></th>
		</tr>';
}

function row_player($row){
	global $accesslvls;
    global $map;
    $MapCoords = worldspaceToMapCoords($row['Worldspace'], $map);
	$x = 0;
	$y = 0;
	if(array_key_exists(2,$MapCoords)){$x = $MapCoords[2];}
	if(array_key_exists(1,$MapCoords)){$y = $MapCoords[1];}
	$Inventory = $row['Inventory'];
	$Inventory = str_replace(",", ",", $Inventory);
	$Inventory  = json_decode($Inventory);
	if(!is_array($Inventory)) {$Inventory = array();}
	if(array_key_exists(0,$Inventory)){
		if(array_key_exists(1,$Inventory)){
			$Inventory = (array_merge($Inventory[0], $Inventory[1]));
		} else {
			$Inventory = $Inventory[0];
		}
	} else {
		if(array_key_exists(1,$Inventory)){
			$Inventory = $Inventory[1];
		}
	}
	$InventoryPreview = "";
	$limit = 6;
	for ($i=0; $i< $limit; $i++){
		if(array_key_exists($i,$Inventory)){
			//$InventoryPreview .= $Inventory[$i];
			$curitem = $Inventory[$i];
			$icount = "";
			if (is_array($curitem)){$curitem = $Inventory[$i][0]; $icount = ' - '.$Inventory[$i][1].' rounds'; }
			$InventoryPreview .= '<div class="preview_gear_slot" style="margin-top:0px;width:47px;height:47px;"><img style="max-width:43px;max-height:43px;" src="images/thumbs/'.$curitem.'.png" title="'.$curitem.$icount.'" alt="'.$curitem.$icount.'"/></div>';
		} else {
			$InventoryPreview .= '<div class="preview_gear_slot" style="margin-top:0px;width:47px;height:47px;"></div>';
		}			
	}
	$Backpack  = $row['Backpack'];
	$Backpack = str_replace(",", ",", $Backpack);
	$Backpack  = json_decode($Backpack);
	if(array_key_exists(0,$Backpack)){ 
		$bpweapons = array();
		$bpweapons[] = $Backpack[0];
		if(array_key_exists(1,$Backpack)){ 
			$bpweaponscount = count($Backpack[1][0]);				
			for ($m=0; $m<$bpweaponscount; $m++){
					for ($mi=0; $mi<$Backpack[1][1][$m]; $mi++){
						$bpweapons[] = $Backpack[1][0][$m];
					}
			}							
		}
		$bpitems = array();
		if(array_key_exists(1,$Backpack)){ 
			$bpitemscount = count($Backpack[2][0]);
			for ($m=0; $m<$bpitemscount; $m++){
				for ($mi=0; $mi<$Backpack[2][1][$m]; $mi++){
					$bpitems[] = $Backpack[2][0][$m];
				}
			}
		}
		$Backpack = (array_merge($bpweapons, $bpitems));
	}
	$BackpackPreview = "";
	for ($i=0; $i< $limit; $i++){
		if(array_key_exists($i,$Backpack)){
			$curitem = $Backpack[$i];
			if (is_array($curitem)){
				if ($i != 0){
					$curitem = $Backpack[$i][0]; $icount = ' - '.$Backpack[$i][1].' rounds';
				}
			}
            if($curitem == ""){
                $curitem = "blank";
            }
  			$BackpackPreview .= '<div class="preview_gear_slot" style="margin-top:0px;width:47px;height:47px;"><img style="max-width:43px;max-height:43px;" src="images/thumbs/'.$curitem.'.png" title="'.$curitem.$icount.'" alt="'.$curitem.$icount.'"/></div>';
		} else {
			$BackpackPreview .= '<div class="preview_gear_slot" style="margin-top:0px;width:47px;height:47px;"></div>';
		}			
	}
	$icon = '<img src="images/icons/player'.($row['Alive'] ? '' : '_dead').'.png" title="" alt=""/>';
	
	if ($accesslvls[0][3] != 'true') {
        $loc = "Classified!";
    } else {
        $loc = sprintf("%03d%03d",$y,$x);
    }

	$tablerow = "<tr>
		<td align=\"center\" class=\"gear_preview\">".$icon."</td>
		<td align=\"center\" class=\"gear_preview\"><a href=\"admin.php?view=info&show=1&CharacterID=".$row['CharacterID']."\">".htmlspecialchars($row['playerName'])."</a></td>
		<td align=\"center\" class=\"gear_preview\"><a href=\"admin.php?view=info&show=1&Character=".$row['CharacterID']."\">".$row['playerUID']."</a></td>
		<td align=\"center\" class=\"gear_preview\"><a href=\"admin.php?view=info&show=1&CharacterID=".$row['CharacterID']."\">".$loc."</a></td>
		<td align=\"center\" class=\"gear_preview_green\">".$InventoryPreview."</td>
		<td align=\"center\" class=\"gear_preview_green\">".$BackpackPreview. "</td>
	</tr>";
	return $tablerow;	
}

function row_online_player($row, $player){
	global $accesslvls;
    global $map;
	$x = 0;
	$y = 0;
    $MapCoords = worldspaceToMapCoords($row['Worldspace'], $map);
	if(array_key_exists(2,$MapCoords)){$x = $MapCoords[2];}
	if(array_key_exists(1,$MapCoords)){$y = $MapCoords[1];}
	$Inventory = $row['Inventory'];
	$Inventory = str_replace("|", ",", $Inventory);
	$Inventory  = json_decode($Inventory);
	if(array_key_exists(0,$Inventory)){
		if(array_key_exists(1,$Inventory)){
			$Inventory = (array_merge($Inventory[0], $Inventory[1]));
		} else {
			$Inventory = $Inventory[0];
		}
	} else {
		if(array_key_exists(1,$Inventory)){
			$Inventory = $Inventory[1];
		}
	}
	$InventoryPreview = "";
	$limit = 6;
	for ($p=0; $p< $limit; $p++){
		if(array_key_exists($p,$Inventory)){
			$curitem = $Inventory[$p];
			$pcount = "";
			if (is_array($curitem)){$curitem = $Inventory[$p][0]; $pcount = ' - '.$Inventory[$p][1].' rounds'; }
			$InventoryPreview .= '<div class="preview_gear_slot" style="margin-top:0px;width:47px;height:47px;"><img style="max-width:43px;max-height:43px;" src="images/thumbs/'.$curitem.'.png" title="'.$curitem.$pcount.'" alt="'.$curitem.$pcount.'"/></div>';
		} else {
			$InventoryPreview .= '<div class="preview_gear_slot" style="margin-top:0px;width:47px;height:47px;"></div>';
		}			
	}
	$Backpack  = $row['Backpack'];
	$Backpack = str_replace("|", ",", $Backpack);
	$Backpack  = json_decode($Backpack);
	if(!is_array($Backpack)) {$Backpack = array();}
	if(array_key_exists(0,$Backpack)){ 
		$bpweapons = array();
		$bpweapons[] = $Backpack[0];
		if(array_key_exists(1,$Backpack)){ 
			if(array_key_exists(0,$Backpack[1])){ 
				$bpweaponscount = count($Backpack[1][0]);				
				for ($m=0; $m<$bpweaponscount; $m++){
						for ($mi=0; $mi<$Backpack[1][1][$m]; $mi++){
							$bpweapons[] = $Backpack[1][0][$m];
						}
				}	
			}							
		}
		$bpitems = array();
		if(array_key_exists(1,$Backpack)){ 
			if(array_key_exists(0,$Backpack[2])){ 
				$bpitemscount = count($Backpack[2][0]);
				for ($m=0; $m<$bpitemscount; $m++){
					for ($mi=0; $mi<$Backpack[2][1][$m]; $mi++){
						$bpitems[] = $Backpack[2][0][$m];
					}
				}
			}
		}
		$Backpack = (array_merge($bpweapons, $bpitems));
	}
	$BackpackPreview = "";
	for ($p=0; $p< $limit; $p++){
		if(!is_array($Backpack)) {$Backpack = array();}
		if(array_key_exists($p,$Backpack)){
			$curitem = $Backpack[$p];
			if (is_array($curitem)){
				if ($p != 0){
					$curitem = $Backpack[$p][0]; $pcount = ' - '.$Backpack[$p][1].' rounds';
				}
			}
            if($curitem == ""){
                $curitem = "blank";
            }
			$BackpackPreview .= '<div class="preview_gear_slot" style="margin-top:0px;width:47px;height:47px;"><img style="max-width:43px;max-height:43px;" src="images/thumbs/'.$curitem.'.png" title="'.$curitem.$pcount.'" alt="'.$curitem.$pcount.'"/></div>';
		} else {
			$BackpackPreview .= '<div class="preview_gear_slot" style="margin-top:0px;width:47px;height:47px;"></div>';
		}			
	}
	$name = "<a href=\"admin.php?view=info&show=1&playerUID=".$row['playerUID']."&CharacterID=".$row['CharacterID']."\">".htmlspecialchars($player[4])."</a>";
	$uid = "<a href=\"admin.php?view=info&show=1&playerUID=".$row['playerUID']."&CharacterID=".$row['CharacterID']."\">".$row["playerUID"]."</a>";
	

	$kick = '<a href="admin.php?view=actionreason&kick='.$player[0].'&player='.$player[4].'">Kick</a>';
	$ban = '<a href="admin.php?view=actionreason&ban='.$player[0].'&player='.$player[4].'">Ban</a>';
	$reset = '<a href="admin.php?view=actions&resetlocation='.$row['CharacterID'].'">ResetLocation</a>';

	if ($accesslvls[0][3] != 'true') {
        $loc = 'Classified';
    } else {
        $loc = sprintf("%03d%03d",$y,$x);
    }
	$tablerow = "<tr>
		<td align=\"center\" class=\"gear_preview\" style=\"vertical-align:middle;\">".$kick."&nbsp;&nbsp;&nbsp;&nbsp;".$ban."<br><br>".$reset."</td>
		<td align=\"center\" class=\"gear_preview\" style=\"vertical-align:middle;\">".$name."</td>
		<td align=\"center\" class=\"gear_preview\">".$uid."</td>
		<td align=\"center\" class=\"gear_preview\"><a href=\"admin.php?view=info&show=1&playerUID=".$row['playerUID']."&CharacterID=".$row['CharacterID']."\">".$loc."</a></td>
		<td align=\"center\" class=\"gear_preview_green\">".$InventoryPreview."</td>
		<td align=\"center\" class=\"gear_preview_green\">".$BackpackPreview."</td>
		<tr>";
	return $tablerow;	
}

function row_vehicle($row, $chbox){
	global $accesslvls;
    global $map;
	$x = 0;
	$y = 0;
    $MapCoords = worldspaceToMapCoords($row['Worldspace'], $map);
	if(array_key_exists(2,$MapCoords)){$x = $MapCoords[2];}
	if(array_key_exists(1,$MapCoords)){$y = $MapCoords[1];}
	$Inventory  = $row['Inventory'];
	$Inventory = str_replace("", ",", $Inventory);
	$Inventory  = json_decode($Inventory);
	$limit = 6;
	if(count($Inventory) >0){ 
		$bpweapons = array();
		if(array_key_exists(0,$Inventory)){ 
			$bpweaponscount = count($Inventory[0][0]);				
			for ($m=0; $m<$bpweaponscount; $m++){
					for ($mi=0; $mi<$Inventory[0][1][$m]; $mi++){
						$bpweapons[] = $Inventory[0][0][$m];
					}
			}							
		}

						
		$bpitems = array();
		if(array_key_exists(1,$Inventory)){ 
			$bpitemscount = count($Inventory[1][0]);
			for ($m=0; $m<$bpitemscount; $m++){
				for ($mi=0; $mi<$Inventory[1][1][$m]; $mi++){
					$bpitems[] = $Inventory[1][0][$m];
				}
			}
		}
		$bpacks = array();
		if(array_key_exists(2,$Inventory)){ 
			$bpackscount = count($Inventory[2][0]);
			for ($m=0; $m<$bpackscount; $m++){
				for ($mi=0; $mi<$Inventory[2][1][$m]; $mi++){
					$bpacks[] = $Inventory[2][0][$m];
				}
			}
		}
		$Inventory = (array_merge($bpweapons, $bpacks, $bpitems));
	}
	$InventoryPreview = "";
	for ($i=0; $i< $limit; $i++){
		if(array_key_exists($i,$Inventory)){
			$curitem = $Inventory[$i];
			if (is_array($curitem)){
				if ($i != 0){
					$curitem = $Inventory[$i][0]; $icount = ' - '.$Inventory[$i][1].' rounds';
				}
			}
			$InventoryPreview .= '<div class="preview_gear_slot" style="margin-top:0px;width:47px;height:47px;"><img style="max-width:43px;max-height:43px;" src="images/thumbs/'.$curitem.'.png" title="'.$curitem.$icount.'" alt="'.$curitem.$icount.'"/></div>';
		} else {
			$InventoryPreview .= '<div class="preview_gear_slot" style="margin-top:0px;width:47px;height:47px;"></div>';
		}			
	}
	
	$Hitpoints  = $row['Hitpoints'];
	$Hitpoints = str_replace(",", ",", $Hitpoints);
	$Hitpoints  = json_decode($Hitpoints);
	$HitpointsPreview = "";
	for ($i=0; $i< $limit; $i++){
		if(array_key_exists($i,$Hitpoints)){
			$curitem = $Hitpoints[$i];
			$HitpointsPreview .= '<div class="preview_gear_slot" style="margin-top:0px;width:47px;height:47px;background-color: rgba(100,'.round((255/100)*(100 - ($curitem[1]*100))).',0,0.8);"><img style="max-width:43px;max-height:43px;" src="images/hits/'.$curitem[0].'.png" title="'.$curitem[0].' - '.round(100 - ($curitem[1]*100)).'%" alt="'.$curitem[0].' - '.round(100 - ($curitem[1]*100)).'%"/></div>';
		}			
	}	
	
	if ($accesslvls[0][3] != 'true') {
        $loc = 'Classified!';
    } else {
        $loc = sprintf("%03d%03d",$y, $x);
    }
		$tablerow = "<tr>".$chbox."
				<td align=\"center\" class=\"gear_preview\" ><a href=\"admin.php?view=info&show=4&ObjectID=".$row['ObjectID']."\">".$row['ObjectID']."</a></td>
				<td align=\"center\" class=\"gear_preview\" ><a href=\"admin.php?view=info&show=4&ObjectID=".$row['ObjectID']."\">".$row['Classname']."</a></td>			
				<td align=\"center\" class=\"gear_preview\" ><a href=\"admin.php?view=info&show=4&ObjectID=".$row['ObjectID']."\">".$row['ObjectID']."</a></td>
				<td align=\"center\" class=\"gear_preview\" style=\"background-color: rgba(100,".round((255/100)*(100 - ($row['Damage']*100))).",0,0.8);\">".substr($row['Damage'], 0, 6)."</td>
				<td align=\"center\" class=\"gear_preview\" ><a href=\"admin.php?view=info&show=4&ObjectID=".$row['ObjectID']."\">".$loc."</a></td>
				<td align=\"center\" class=\"gear_preview_green\">".$InventoryPreview."</td>
				<td align=\"center\" class=\"gear_preview_green\">".$HitpointsPreview."</td>
			</tr>";
	return $tablerow;
}

function row_deployable($row, $chbox){
	global $accesslvls;
    global $map;
	$x = 0;
	$y = 0;
    $MapCoords = worldspaceToMapCoords($row['Worldspace'], $map);
	if(array_key_exists(2,$MapCoords)){$x = $MapCoords[2];}
	if(array_key_exists(1,$MapCoords)){$y = $MapCoords[1];}
	$Inventory  = $row['Inventory'];
	$Inventory = str_replace("", ",", $Inventory);
	$Inventory  = json_decode($Inventory);
	$limit = 6;
	if(count($Inventory) >0){ 
		$bpweapons = array();
		if(array_key_exists(0,$Inventory)){ 
			$bpweaponscount = count($Inventory[0][0]);				
			for ($m=0; $m<$bpweaponscount; $m++){
					for ($mi=0; $mi<$Inventory[0][1][$m]; $mi++){
						$bpweapons[] = $Inventory[0][0][$m];
					}
			}							
		}

						
		$bpitems = array();
		if(array_key_exists(1,$Inventory)){ 
			$bpitemscount = count($Inventory[1][0]);
			for ($m=0; $m<$bpitemscount; $m++){
				for ($mi=0; $mi<$Inventory[1][1][$m]; $mi++){
					$bpitems[] = $Inventory[1][0][$m];
				}
			}
		}
		$bpacks = array();
		if(array_key_exists(2,$Inventory)){ 
			$bpackscount = count($Inventory[2][0]);
			for ($m=0; $m<$bpackscount; $m++){
				for ($mi=0; $mi<$Inventory[2][1][$m]; $mi++){
					$bpacks[] = $Inventory[2][0][$m];
				}
			}
		}
		$Inventory = (array_merge($bpweapons, $bpacks, $bpitems));
	}
	$InventoryPreview = "";
	for ($i=0; $i< $limit; $i++){
		if(array_key_exists($i,$Inventory)){
			$curitem = $Inventory[$i];
			if (is_array($curitem)){
				if ($i != 0){
					$curitem = $Inventory[$i][0]; $icount = ' - '.$Inventory[$i][1].' rounds';
				}
			}
			$InventoryPreview .= '<div class="preview_gear_slot" style="margin-top:0px;width:47px;height:47px;"><img style="max-width:43px;max-height:43px;" src="images/thumbs/'.$curitem.'.png" title="'.$curitem.$icount.'" alt="'.$curitem.$icount.'"/></div>';
		} else {
			$InventoryPreview .= '<div class="preview_gear_slot" style="margin-top:0px;width:47px;height:47px;"></div>';
		}			
	}
	
	if ($accesslvls[0][3] != 'true') {
        $loc = 'Classified!';
    } else {
        $loc = sprintf("%03d%03d",$y, $x);
    }
	$tablerow = "<tr>".$chbox."
		<td align=\"center\" class=\"gear_preview\" ><a href=\"admin.php?view=info&show=6&ObjectID=".$row['ObjectID']."\">".$row['ObjectID']."</a></td>
		<td align=\"center\" class=\"gear_preview\" ><a href=\"admin.php?view=info&show=6&ObjectID=".$row['ObjectID']."\">".$row['Classname']."</a></td>			
		<td align=\"center\" class=\"gear_preview\" ><a href=\"admin.php?view=info&show=1&CharacterID=".$row['CharacterID']."\">".$row['playerName']."</a></td>
		<td align=\"center\" class=\"gear_preview\" ><a href=\"admin.php?view=info&show=6&ObjectID=".$row['ObjectID']."\">".$loc."</a></td>
		<td align=\"center\" class=\"gear_preview_green\">".$InventoryPreview."</td>
	</tr>";

	return $tablerow;
}

?>

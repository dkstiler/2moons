<?php

/**
 *  2Moons
 *  Copyright (C) 2011  Slaver
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @package 2Moons
 * @author Slaver <slaver7@gmail.com>
 * @copyright 2009 Lucky <lucky@xgproyect.net> (XGProyecto)
 * @copyright 2011 Slaver <slaver7@gmail.com> (Fork/2Moons)
 * @license http://www.gnu.org/licenses/gpl.html GNU GPLv3 License
 * @version 1.6 (2011-11-17)
 * @info $Id$
 * @link http://code.google.com/p/2moons/
 */

function CreateOnePlanetRecord($Galaxy, $System, $Position, $Universe, $PlanetOwnerID, $PlanetName = '', $HomeWorld = false, $AuthLevel = 0)
{
	global $LNG, $db;

	$CONF	= getConfig($Universe);

	if ($CONF['max_galaxy'] < $Galaxy || 1 > $Galaxy) {
		throw new Exception("Access denied for CreateOnePlanetRecord.php.<br>Try to create a planet at position:".$Galaxy.":".$System.":".$Position);
	}	
	
	if ($CONF['max_system'] < $System || 1 > $System) {
		throw new Exception("Access denied for CreateOnePlanetRecord.php.<br>Try to create a planet at position:".$Galaxy.":".$System.":".$Position);
	}	
	
	if ($CONF['max_planets'] < $Position || 1 > $Position) {
		throw new Exception("Access denied for CreateOnePlanetRecord.php.<br>Try to create a planet at position:".$Galaxy.":".$System.":".$Position);
	}
	
	if (CheckPlanetIfExist($Galaxy, $System, $Position, $Universe)) {
		return false;
	}

	$FieldFactor		= $CONF['planet_factor'];
	require(ROOT_PATH.'includes/PlanetData.php');
	$Pos                = ceil($Position / ($CONF['max_planets'] / count($PlanetData))); 
	$TMax				= $PlanetData[$Pos]['temp'];
	$TMin				= $TMax - 40;
	$Fields				= $PlanetData[$Pos]['fields'] * $CONF['planet_factor'];
	$Types				= array_keys($PlanetData[$Pos]['image']);
	$Type				= $Types[array_rand($Types)];
	$Class				= $Type.'planet'.($PlanetData[$Pos]['image'][$Type] < 10 ? '0' : '').$PlanetData[$Pos]['image'][$Type];
	
	$SQL  = "INSERT INTO ".PLANETS." SET ";

	if(!empty($PlanetName))
		$SQL .= "`name` = '".$PlanetName."', ";
	
	$SQL .= "`universe` = '".$Universe."', ";
	$SQL .= "`id_owner` = '".$PlanetOwnerID."', ";
	$SQL .= "`galaxy` = '".$Galaxy."', ";
	$SQL .= "`system` = '".$System."', ";
	$SQL .= "`planet` = '".$Position."', ";
	$SQL .= "`last_update` = '".TIMESTAMP."', ";
	$SQL .= "`planet_type` = '1', ";
	$SQL .= "`image` = '".$Class."', ";
	$SQL .= "`diameter` = '".floor(1000 * sqrt($Fields))."', ";
	$SQL .= "`field_max` = '".(($HomeWorld) ? $CONF['initial_fields'] : floor($Fields))."', ";
	$SQL .= "`temp_min` = '".$TMin."', ";
	$SQL .= "`temp_max` = '".$TMax."', ";
	$SQL .= "`metal` = '".$CONF['metal_start']."', ";
	$SQL .= "`metal_perhour` = '".$CONF['metal_basic_income']."', ";
	$SQL .= "`crystal` = '".$CONF['crystal_start']."', ";
	$SQL .= "`crystal_perhour` = '".$CONF['crystal_basic_income']."', ";
	$SQL .= "`deuterium` = '".$CONF['deuterium_start']."', ";
	$SQL .= "`deuterium_perhour` = '".$CONF['deuterium_basic_income']."';";
	
	$db->query($SQL);
	return $db->GetInsertID();
}
?>
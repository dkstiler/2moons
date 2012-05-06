<?php

/**
 *  2Moons
 *  Copyright (C) 2012 Jan
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
 * @author Jan <info@2moons.cc>
 * @copyright 2006 Perberos <ugamela@perberos.com.ar> (UGamela)
 * @copyright 2008 Chlorel (XNova)
 * @copyright 2009 Lucky (XGProyecto)
 * @copyright 2012 Jan <info@2moons.cc> (2Moons)
 * @license http://www.gnu.org/licenses/gpl.html GNU GPLv3 License
 * @version 1.7.0 (2012-05-31)
 * @info $Id$
 * @link http://code.google.com/p/2moons/
 */

function getUniverse()
{
	if(defined('IN_ADMIN') && isset($_SESSION['adminuni'])) {
		$UNI	= (int) $_SESSION['adminuni'];
	} elseif(defined('LOGIN') && isset($_REQUEST['uni'])) {
		$UNI	= (int) $_REQUEST['uni'];
	} elseif(defined('LOGIN') && isset($_COOKIE['uni'])) {
		$UNI	= (int) $_COOKIE['uni'];
	} elseif(UNIS_HTACCESS === true) {
		// Enable htaccess
		if(isset($_SERVER["REDIRECT_UNI"])) {
			$UNI	= $_SERVER["REDIRECT_UNI"];
		} else {
			HTTP::redirectTo("uni".ROOT_UNI."/".basename($_SERVER['SCRIPT_FILENAME']).(!empty($_SERVER["QUERY_STRING"]) ? "?".$_SERVER["QUERY_STRING"]: ""));
		}
	} else {
		if(UNIS_WILDCAST === true) {
			$UNI	= explode('.', $_SERVER['HTTP_HOST']);
			$UNI	= substr($UNI[0], 3);
			if(!is_numeric($UNI))
				$UNI	= ROOT_UNI;
		} else {
			$UNI	= ROOT_UNI;
		}
	}
	
	return $UNI;
}

function elementHasFlag($elementID, $flag) {
	#return ($GLOBALS['VARS']['ELEMENT'][$elementID]['flag'] & $flag) === $flag;
	return isset($GLOBALS['VARS']['LIST'][$flag][$elementID]);
}

function getFactors($USER, $Type = 'basic', $TIME = NULL) {
	if(empty($TIME)) {
		$TIME	= TIMESTAMP;
	}
	$resourceIDs	= array_merge($GLOBALS['VARS']['LIST'][ELEMENT_PLANET_RESOURCE], $GLOBALS['VARS']['LIST'][ELEMENT_USER_RESOURCE], $GLOBALS['VARS']['LIST'][ELEMENT_ENERGY]);
	
	$factor	= array(
		'Attack'			=> 0,
		'Defensive'			=> 0,
		'Shield'			=> 0,
		'BuildTime'			=> 0,
		'ResearchTime'		=> 0,
		'ShipTime'			=> 0,
		'DefensiveTime'		=> 0,
		'Resource'			=> 0,
		'Energy'			=> 0,
		'ResourceStorage'	=> 0,
		'ShipStorage'		=> 0,
		'FlyTime'			=> 0,
		'FleetSlots'		=> 0,
		'Planets'			=> 0,
	);
	
	foreach ($resourceIDs as $resourceID) {
		$factor['ResourceSpecific'][$resourceID]	= 0;
	}
	
	$elementIDs	= array_keys($GLOBALS['VARS']['ELEMENT']);
	foreach($elementIDs as $elementID)
	{
		$bonus = $GLOBALS['VARS']['ELEMENT'][$elementID]['bonus'];
			
		if (isset($PLANET[$GLOBALS['VARS']['ELEMENT'][$elementID]['name']])) {
			$elementLevel = $PLANET[$GLOBALS['VARS']['ELEMENT'][$elementID]['name']];
		} elseif (isset($USER[$GLOBALS['VARS']['ELEMENT'][$elementID]['name']])) {
			$elementLevel = $USER[$GLOBALS['VARS']['ELEMENT'][$elementID]['name']];
		} else {
			continue;
		}
		
		if(elementHasFlag($elementID, ELEMENT_PREMIUM)) {
			if(DMExtra($elementLevel, $TIME, false, true)) {
				continue;
			}
			
			$factor['Attack']			+= $bonus['Attack'];
			$factor['Defensive']		+= $bonus['Defensive'];
			$factor['Shield']			+= $bonus['Shield'];
			$factor['BuildTime']		+= $bonus['BuildTime'];
			$factor['ResearchTime']		+= $bonus['ResearchTime'];
			$factor['ShipTime']			+= $bonus['ShipTime'];
			$factor['DefensiveTime']	+= $bonus['DefensiveTime'];
			$factor['Resource']			+= $bonus['Resource'];
			$factor['Energy']			+= $bonus['Energy'];
			$factor['ResourceStorage']	+= $bonus['ResourceStorage'];
			$factor['ShipStorage']		+= $bonus['ShipStorage'];
			$factor['FlyTime']			+= $bonus['FlyTime'];
			$factor['FleetSlots']		+= $bonus['FleetSlots'];
			$factor['Planets']			+= $bonus['Planets'];
			
			foreach ($resourceIDs as $resourceID) {
				$factor['ResourceSpecific'][$resourceID]	+= $bonus['ResourceID'.$resourceID];
			}
		} else {
			$factor['Attack']			+= $elementLevel * $bonus['Attack'];
			$factor['Defensive']		+= $elementLevel * $bonus['Defensive'];
			$factor['Shield']			+= $elementLevel * $bonus['Shield'];
			$factor['BuildTime']		+= $elementLevel * $bonus['BuildTime'];
			$factor['ResearchTime']		+= $elementLevel * $bonus['ResearchTime'];
			$factor['ShipTime']			+= $elementLevel * $bonus['ShipTime'];
			$factor['DefensiveTime']	+= $elementLevel * $bonus['DefensiveTime'];
			$factor['Resource']			+= $elementLevel * $bonus['Resource'];
			$factor['Energy']			+= $elementLevel * $bonus['Energy'];
			$factor['ResourceStorage']	+= $elementLevel * $bonus['ResourceStorage'];
			$factor['ShipStorage']		+= $elementLevel * $bonus['ShipStorage'];
			$factor['FlyTime']			+= $elementLevel * $bonus['FlyTime'];
			$factor['FleetSlots']		+= $elementLevel * $bonus['FleetSlots'];
			$factor['Planets']			+= $elementLevel * $bonus['Planets'];
			foreach ($resourceIDs as $resourceID) {
				$factor['ResourceSpecific'][$resourceID]	+= $elementLevel * $bonus['ResourceID'.$resourceID];
			}
		}
	}
	
	return $factor;
}

function getPlanets($USER)
{
	if(isset($USER['PLANETS']))
		return $USER['PLANETS'];
		
	$Order = $USER['planet_sort_order'] == 1 ? "DESC" : "ASC" ;
	$Sort  = $USER['planet_sort'];

	$QryPlanets  = "SELECT id, name, galaxy, system, planet, planet_type, image, b_building, b_building_id FROM ".PLANETS." WHERE id_owner = '".$USER['id']."' AND destruyed = '0' ORDER BY ";

	if($Sort == 0)
		$QryPlanets .= "id ". $Order;
	elseif($Sort == 1)
		$QryPlanets .= "galaxy, system, planet, planet_type ". $Order;
	elseif ($Sort == 2)
		$QryPlanets .= "name ". $Order;

	$PlanetRAW = $GLOBALS['DATABASE']->query($QryPlanets);
	
	while($Planet = $GLOBALS['DATABASE']->fetchArray($PlanetRAW))
		$Planets[$Planet['id']]	= $Planet;

	$GLOBALS['DATABASE']->free_result($PlanetRAW);
	return $Planets;
}

function setConfig($configArray, $UNI = NULL, $flushCache = true)
{
	global $gameConfig, $uniConfig, $uniAllConfig;
	$UNI	= (empty($UNI)) ? $GLOBALS['UNI'] : $UNI;
	$sql	= array();
		
	foreach($configArray as $Name => $Value) {
		if(isset($uniConfig[$Name])) {
			$uniConfig[$Name]			= $Value; 
			$uniAllConfig[$UNI][$Name]	= $Value;
			$GLOBALS['DATABASE']->query("UPDATE ".UNIVERSE_CONFIG." SET value = '".$GLOBALS['DATABASE']->escape($Value)."' WHERE universe = ".$UNI." AND name = '".$Name."';");
		} elseif(isset($gameConfig[$Name])) {
			$gameConfig[$Name]			= $Value; 
			$GLOBALS['DATABASE']->query("UPDATE ".CONFIG." SET value = '".$GLOBALS['DATABASE']->escape($Value)."' WHERE name = '".$Name."';");
		} else {
			throw new Exception('Unknown config value: '.$Name);
		}
	}
	
	if($flushCache) {
		$GLOBALS['CACHE']->flush('config');
		$GLOBALS['CACHE']->flush('universe');
	}
}

function CalculateMaxPlanetFields($planet)
{
	global $resource, $uniAllConfig, $UNI;
	return $planet['field_max'] + ($planet[$GLOBALS['VARS']['ELEMENT'][33]['name']] * $uniAllConfig[$UNI]['planetAddFieldsByTerraFormer']) + ($planet[$GLOBALS['VARS']['ELEMENT'][41]['name']] * $uniAllConfig[$UNI]['planetAddFieldsByMoonBase']);
}

function pretty_time($seconds)
{
	global $LNG;
	
	$day	= floor($seconds / 86400);
	$hour	= floor($seconds / 3600 % 24);
	$minute	= floor($seconds / 60 % 60);
	$second	= floor($seconds % 60);
	
	$time  = '';
	
	if($day > 9) {
		$time .= $day.$LNG['short_day'].' ';
	} elseif($day > 0) {
		$time .= '0'.$day.$LNG['short_day'].' ';
	}
	
	if($hour > 9) {
		$time .= $hour.$LNG['short_hour'].' ';
	} else {
		$time .= '0'.$hour.$LNG['short_hour'].' ';
	}
	
	if($minute > 9) {
		$time .= $minute.$LNG['short_minute'].' ';
	} else {
		$time .= '0'.$minute.$LNG['short_minute'].' ';
	}
	
	if($second > 9) {
		$time .= $second.$LNG['short_second'];
	} else {
		$time .= '0'.$second.$LNG['short_second'];
	}

	return $time;
}

function GetStartAdressLink($FleetRow, $FleetType = '')
{
	return '<a href="game.php?page=galaxy&amp;galaxy='.$FleetRow['fleet_start_galaxy'].'&amp;system='.$FleetRow['fleet_start_system'].'" class="'. $FleetType .'">['.$FleetRow['fleet_start_galaxy'].':'.$FleetRow['fleet_start_system'].':'.$FleetRow['fleet_start_planet'].']</a>';
}

function GetTargetAdressLink($FleetRow, $FleetType = '')
{
	return '<a href="game.php?page=galaxy&amp;galaxy='.$FleetRow['fleet_end_galaxy'].'&amp;system='.$FleetRow['fleet_end_system'].'" class="'. $FleetType .'">['.$FleetRow['fleet_end_galaxy'].':'.$FleetRow['fleet_end_system'].':'.$FleetRow['fleet_end_planet'].']</a>';
}

function BuildPlanetAdressLink($CurrentPlanet)
{
	return '<a href="game.php?page=galaxy&amp;galaxy='.$CurrentPlanet['galaxy'].'&amp;system='.$CurrentPlanet['system'].'">['.$CurrentPlanet['galaxy'].':'.$CurrentPlanet['system'].':'.$CurrentPlanet['planet'].']</a>';
}

function pretty_number($n, $dec = 0)
{
	return number_format(floattostring($n, $dec), $dec, ',', '.');
}

function GetUserByID($UserID, $GetInfo = "*")
{
	if(is_array($GetInfo)) {
		$GetOnSelect = "";
		foreach($GetInfo as $id => $col)
		{
			$GetOnSelect .= "".$col.",";
		}
		$GetOnSelect = substr($GetOnSelect, 0, -1);
	}
	else
		$GetOnSelect = $GetInfo;
	
	$User = $GLOBALS['DATABASE']->getFirstRow("SELECT ".$GetOnSelect." FROM ".USERS." WHERE id = '". $UserID ."';");
	return $User;
}

function MailSend($MailTarget, $MailTargetName, $MailSubject, $MailContent)
{
	global $gameConfig, $uniConfig;
	try {
		$mail	= new PHPMailer(true);
		if($gameConfig['mailMethod'] == 2)
		{
			$mail->IsSMTP();  
			$mail->SMTPSecure	= $gameConfig['mailSmtpSecure'];  						
			$mail->Host			= $gameConfig['mailSmtpAdress'];
			$mail->Port			= $gameConfig['mailSmtpPort'];
			
			if(!empty($gameConfig['mailSmtpUser']))
			{
				$mail->SMTPAuth	= true; 
				$mail->Username	= $gameConfig['mailSmtpUser'];
				$mail->Password	= $gameConfig['mailSmtpPass'];
			}
		}
		else
		{
			$mail->IsMail();
		}
		
		$mail->CharSet	= 'UTF-8';		
		$mail->Subject	= $MailSubject;
		$mail->Body		= $MailContent;
		$mail->SetFrom($gameConfig['mailSenderMail'], $uniConfig['gameName']);
		$mail->AddAddress($MailTarget, $MailTargetName);
		
		return $mail->Send();	
	} catch (phpmailerException $e) {
		return $e->errorMessage();
	} catch (Exception $e) {
		return $e->getMessage();
	}
}

function makebr($text)
{
    // XHTML FIX for PHP 5.3.0
	// Danke an Meikel
	
    $BR = "<br>\n";
    return (version_compare(PHP_VERSION, "5.3.0", ">=")) ? nl2br($text, false) : strtr($text, array("\r\n" => $BR, "\r" => $BR, "\n" => $BR)); 
}

function CheckNoobProtec($OwnerPlayer, $TargetPlayer, $Player)
{	
	global $uniConfig, $gameConfig;
	if(
		$uniConfig['noobProtectionEnable'] == 0 
		|| $uniConfig['noobProtectionToPoints'] == 0 
		|| $uniConfig['noobProtectionRange'] == 0 
		|| $Player['banaday'] > TIMESTAMP
		|| $Player['onlinetime'] < TIMESTAMP - $gameConfig['userInactiveSinceDays']
	) {
		return array('NoobPlayer' => false, 'StrongPlayer' => false);
	}
	
	return array(
		'NoobPlayer' => (
			/* WAHR: 
				Wenn Spieler mehr als 25000 Punkte hat UND
				Wenn ZielSpieler weniger als 80% der Punkte des Spieler hat.
				ODER weniger als 5.000 hat.
			*/
			// Addional Comment: Letzteres ist eigentlich sinnfrei, bitte testen.a
			($TargetPlayer['total_points'] <= $uniConfig['noobprotectiontime']) && // Default: 25.000
			($OwnerPlayer['total_points'] > $TargetPlayer['total_points'] * $uniConfig['noobprotectionmulti'])
		), 
		'StrongPlayer' => (
			/* WAHR: 
				Wenn Spieler weniger als 5000 Punkte hat UND
				Mehr als das funfache der eigende Punkte hat
			*/
			(!$uniConfig['noobProtectionAllowStrong']) && // Default: 5.000
			($OwnerPlayer['total_points'] < $uniConfig['noobprotectiontime']) && // Default: 5.000
			($OwnerPlayer['total_points'] * $uniConfig['noobprotectionmulti'] < $TargetPlayer['total_points'])
		),
	);
}

function SendSimpleMessage($Owner, $Sender, $Time, $Type, $From, $Subject, $Message)
{
			
	$SQL	= "INSERT INTO ".MESSAGES." SET 
	message_owner = ".(int) $Owner.", 
	message_sender = ".(int) $Sender.", 
	message_time = ".(int) $Time.", 
	message_type = ".(int) $Type.", 
	message_from = '".$GLOBALS['DATABASE']->escape($From) ."', 
	message_subject = '". $GLOBALS['DATABASE']->escape($Subject) ."', 
	message_text = '".$GLOBALS['DATABASE']->escape($Message)."', 
	message_unread = '1', 
	message_universe = ".$GLOBALS['UNI'].";";

	$GLOBALS['DATABASE']->query($SQL);
}

function shortly_number($number, $decial = NULL)
{
	$negate	= $number < 0 ? -1 : 1;
	$number	= abs($number);
    $unit	= array("", "K", "M", "B", "T", "Q", "Q+", "S", "S+", "O", "N");
	$key	= 0;
	
	if($number >= 1000000) {
		++$key;
		while($number >= 1000000)
		{
			++$key;
			$number = $number / 1000000;
		}
	} elseif($number >= 1000) {
		++$key;
		$number = $number / 1000;
	}
	
	$decial	= !is_numeric($decial) ? ((int) ($number != 0 && $number < 100)) : $decial;
	
	return pretty_number($negate * $number, $decial).'&nbsp;'.$unit[$key];
}

function floattostring($Numeric, $Pro = 0, $Output = false){
	return ($Output) ? str_replace(",",".", sprintf("%.".$Pro."f", $Numeric)) : sprintf("%.".$Pro."f", $Numeric);
}

function isModulAvalible($ID)
{
	global $module;
	if(!isset($module[$ID])) {
		throw new Exception('Unknown module id '.$ID);
	}
	
	return $module[$ID] == 1 || (isset($USER) && $USER['authlevel'] == AUTH_ADM);
}

function GetCrons()
{
	//Needs rewrited ...
	return '';
}

function allowedTo($side)
{
	global $USER;
	return ($USER['authlevel'] == AUTH_ADM || (isset($USER['rights']) && $USER['rights'][$side] == 1));
}

function getRandomString() {
	return md5(uniqid());
}

function clearGIF() {
	header('Cache-Control: no-cache');
	header('Content-type: image/gif');
	header('Content-length: 43');
	header('Expires: 0');
	echo("\x47\x49\x46\x38\x39\x61\x01\x00\x01\x00\x80\x00\x00\x00\x00\x00\x00\x00\x00\x21\xF9\x04\x01\x00\x00\x00\x00\x2C\x00\x00\x00\x00\x01\x00\x01\x00\x00\x02\x02\x44\x01\x00\x3B");
	exit;
}
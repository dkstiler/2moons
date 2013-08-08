<?php

/**
 *  2Moons
 *  Copyright (C) 2012 Jan Kröpke
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
 * @author Jan Kröpke <info@2moons.cc>
 * @copyright 2012 Jan Kröpke <info@2moons.cc>
 * @license http://www.gnu.org/licenses/gpl.html GNU GPLv3 License
 * @version 1.8.0 (2013-03-18)
 * @info $Id: HTTP.class.php 2752 2013-05-20 15:13:04Z slaver7 $
 * @link http://2moons.cc/
 */

class Element implements Serializable
{
    private $data;


    public function __construct($data, $resources = NULL)
    {
        $data['flags']    = array();

        $data['calcProduction']  = array();
        $data['calcStorage']     = array();
        $data['consumption']     = array();
        $data['cost']            = array();

        switch($data['class'])
        {
            case VARS::CLASS_RESOURCE:
                switch($data['resourceMode'])
                {
                    case 'planet':
                        $data['flags'][]  = VARS::FLAG_RESOURCE_PLANET;
                    break;
                    case 'user':
                        $data['flags'][]  = VARS::FLAG_RESOURCE_USER;
                    break;
                    case 'energy':
                        $data['flags'][]  = VARS::FLAG_ENERGY;
                    break;

                }

                if($data['flagDebris'] == 1)
                {
                    $data['flags'][]  = VARS::FLAG_DEBRIS;
                }

                if($data['flagTransport'] == 1)
                {
                    $data['flags'][]  = VARS::FLAG_STEAL;
                }

                if($data['flagSteal'] == 1)
                {
                    $data['flags'][]  = VARS::FLAG_TRANSPORT;
                }

                if($data['flagTopNav'] == 1)
                {
                    $data['flags'][]  = VARS::FLAG_TOPNAV;
                }

                if($data['flagOnEcoOverview'] == 1)
                {
                    $data['flags'][]  = VARS::FLAG_ON_ECO_OVERVIEW;
                }

                if($data['flagCalculateBuildTime'] == 1)
                {
                    $data['flags'][]  = VARS::FLAG_CALCULATE_BUILD_TIME;
                }

                if($data['flagCalculateFleetStructure'] == 1)
                {
                    $data['flags'][]  = VARS::FLAG_CALC_FLEET_STRUCTURE;
                }
            break;
            case Vars::CLASS_QUEUE:
                $tmp    = array();
                $tmp['elementID']   = $data['elementID'];
                $tmp['class']       = $data['class'];
                $tmp['maxCount']    = $data['maxLevel'];
                $tmp['flags']       = array();
                $tmp['forClasses']  = array();
                foreach(Vars::getElementsByQueue($data['elementID']) as $elementObj)
                {
                    $tmp['forClasses'][]    = $elementObj->class;
                }

                $tmp['forClasses']  = array_unique($tmp['forClasses']);
                unset($data);
                $data   = $tmp;
            break;
            default:
                if($data['flagBuildOnPlanet'] == 1)
                {
                    $data['flags'][]  = VARS::FLAG_BUILD_ON_PLANET;
                }

                if($data['flagBuildOnMoon'] == 1)
                {
                    $data['flags'][]  = VARS::FLAG_BUILD_ON_MOON;
                }

                if($data['flagAttackMissile'] == 1)
                {
                    $data['flags'][]  = VARS::FLAG_ATTACK_MISSILE;
                }

                if($data['flagDefendMissile'] == 1)
                {
                    $data['flags'][]  = VARS::FLAG_DEFEND_MISSILE;
                }

                if($data['flagSpy'] == 1)
                {
                    $data['flags'][]  = VARS::FLAG_SPY;
                }

                if($data['flagCollect'] == 1)
                {
                    $data['flags'][]  = VARS::FLAG_COLLECT;
                }

                if($data['flagColonize'] == 1)
                {
                    $data['flags'][]  = VARS::FLAG_COLONIZE;
                }

                if($data['flagDestroy'] == 1)
                {
                    $data['flags'][]  = VARS::FLAG_DESTROY;
                }

                if($data['flagSpecExpedition'] == 1)
                {
                    $data['flags'][]  = VARS::FLAG_SPEC_EXPEDITION;
                }

                if($data['flagTrade'] == 1)
                {
                    $data['flags'][]  = VARS::FLAG_TRADE;
                }

                foreach(BuildUtil::getBonusList() as $bonus)
                {
                    $data["bonus"][$bonus]['value']  = $data["bonus$bonus"];
                    $data["bonus"][$bonus]['unit']   = $data["bonus{$bonus}Unit"] == 0 ? 'percent' : 'static';
                    unset($data["bonus$bonus"], $data["bonus{$bonus}Unit"]);
                }

                if(array_filter($data["bonus"]))
                {
                    $data['flags'][]  = VARS::FLAG_BONUS;
                }

                foreach(array_merge($resources[0], $resources[1], $resources[2]) as $elementObj)
                {
                    $data['calcProduction'][$elementObj->elementID] = $data["production$elementObj->elementID"];
                    $data['cost'][$elementObj->elementID]           = $data["cost$elementObj->elementID"];
                    unset($data["production$elementObj->elementID"], $data["cost$elementObj->elementID"]);
                }

                foreach(array_merge($resources[0], $resources[1]) as $elementObj)
                {
                    $data['consumption'][1][$elementObj->elementID] = $data["consumption1$elementObj->elementID"];
                    $data['consumption'][2][$elementObj->elementID] = $data["consumption2$elementObj->elementID"];
                    $data['consumption'][3][$elementObj->elementID] = $data["consumption3$elementObj->elementID"];
                    unset($data["consumption1$elementObj->elementID"], $data["consumption2$elementObj->elementID"], $data["consumption3$elementObj->elementID"]);
                }

                foreach($resources[0] as $elementObj)
                {
                    $data['calcStorage'][$elementObj->elementID] = $data["storage$elementObj->elementID"];
                    unset($data["storage$elementObj->elementID"]);
                }

                if(array_filter($data['calcProduction']))
                {
                    $data['flags'][]  = VARS::FLAG_PRODUCTION;
                }

                if(array_filter($data['calcStorage']))
                {
                    $data['flags'][]  = VARS::FLAG_STORAGE;
                }
            break;
        }

        unset($data['resourceCanBuildOnPlanet'],$data['resourceCanBuildOnMoons'], $data['resourceCanOnDebris'],
        $data['resourceIsPlanetResource'], $data['resourceIsUserResource'], $data['resourceIsEnergy'],
        $data['resourceCanOnShip'], $data['resourceCanStealOnAttack'], $data['resourceIsOnTopNav']);

        $this->data = $data;
    }

    public function __get($key)
    {
        if(!isset($this->data[$key]))
        {
            throw new Exception("Unknown key '$key'");
        }
        return $this->data[$key];
    }

    public function hasFlag($flag)
    {
        return in_array($flag, $this->flags);
    }

    public function serialize() {
        return json_encode($this->data);
    }

    public function unserialize($data) {
        $this->data = json_decode($data, true);
    }
}
{include file="overall_header.tpl"}
{include file="overall_topnav.tpl"}
{include file="left_menu.tpl"}
<form action="game.php?page=fleet2" method="post" onsubmit='this.submit.disabled = true;'>
    <input type="hidden" name="speedallsmin"   		value="{$speedallsmin}">
    <input type="hidden" name="usedfleet"      		value="{$fleetarray}">
    <input type="hidden" name="thisgalaxy"     		value="{$galaxy}">
    <input type="hidden" name="thissystem"     		value="{$system}">
    <input type="hidden" name="thisplanet"     		value="{$planet}">
    <input type="hidden" name="thisplanettype" 		value="{$planet_type}">
    <input type="hidden" name="fleetroom" 			value="{$fleetroom}">
    <input type="hidden" name="target_mission" 		value="{$target_mission}">
    <input type="hidden" name="speedfactor" 		value="{$speedfactor}">
    <input type="hidden" name="fleetspeedfactor" 	value="{$fleetspeedfactor}">
	<input type="hidden" name="fleet_group"     	value="0">
    <input type="hidden" name="acs_target_mr"   	value="0:0:0">
    {$inputs}
    <div id="content" class="content">
    	<table class="table519">
        	<tr style="height:20px;">
        		<th colspan="2">{$fl_send_fleet}</th>
        	</tr>
            <tr style="height:20px;">
            	<td style="width:50%">{$fl_destiny}</td>
            	<td>
                    <input name="galaxy" size="3" maxlength="2" onChange="shortInfo()" onKeyUp="shortInfo()" value="{$galaxy_post}">
                    <input name="system" size="3" maxlength="3" onChange="shortInfo()" onKeyUp="shortInfo()" value="{$system_post}">
                    <input name="planet" size="3" maxlength="2" onChange="shortInfo()" onKeyUp="shortInfo()" value="{$planet_post}">
                    <select name="planettype" onChange="shortInfo()" onKeyUp="shortInfo()">
                    {html_options options=$options_selector selected=$options}
                    </select>
            	</td>
            </tr>
            <tr style="height:20px;">
            	<td>{$fl_fleet_speed}</td>
            	<td>
                <select name="speed" onChange="shortInfo()" onKeyUp="shortInfo()">
                    {html_options options=$AvailableSpeeds}
                </select> %
                </td>
            </tr>
            <tr style="height:20px;">
            	<td>{$fl_distance}</td>
            	<td><div id="distance">-</div></td>
            </tr>
            <tr style="height:20px;">
            	<td>{$fl_flying_time}</th>
            	<td><div id="duration">-</div></td>
            </tr>
            <tr style="height:20px;">
                <td>{$fl_fuel_consumption}</td>
                <td><div id="consumption">-</div></td>
            </tr>
            <tr style="height:20px;">
                <td>{$fl_max_speed}</td>
                <td><div id="maxspeed">-</div></td>
            </tr>
            <tr style="height:20px;">
                <td>{$fl_cargo_capacity}</td>
                <td><div id="storage">-</div></td>
            </tr>
            <tr style="height:20px;">
                <th colspan="2">{$fl_shortcut} <a href="javascript:f('game.php?page=shortcuts','short');">{$fl_shortcut_add_edit}</a></th>
            </tr>
            {foreach name=ShoutcutList item=ShoutcutRow from=$Shoutcutlist}
			{if $smarty.foreach.ShoutcutList.iteration is odd}<tr style="height:20px;">{/if}
            <td><a href="javascript:setTarget({$ShoutcutRow.galaxy},{$ShoutcutRow.system},{$ShoutcutRow.planet},{$ShoutcutRow.planet_type});shortInfo();">{$ShoutcutRow.name}{if $ColonyRow.planet_type == 1}{$fl_planet_shortcut}{elseif $ColonyRow.planet_type == 2}{$fl_derbis_shortcut}{elseif $ShoutcutRow.planet_type == 3}{$fl_moon_shortcut}{/if} [{$ShoutcutRow.galaxy}:{$ShoutcutRow.system}:{$ShoutcutRow.planet}]</a></td>
			{if $smarty.foreach.ShoutcutList.last && $smarty.foreach.ShoutcutList.total is odd}<td>&nbsp;</td>{/if}
			{if $smarty.foreach.ShoutcutList.iteration is even}</tr>{/if}
			{foreachelse}
			<tr style="height:20px;">
				<td colspan="2">{$fl_no_shortcuts}</td>
			</tr>
            {/foreach}
			<tr style="height:20px;">
            	<th colspan="2">{$fl_my_planets}</th>
            </tr>
			{foreach name=ColonyList item=ColonyRow from=$Colonylist}
			{if $smarty.foreach.ColonyList.iteration is odd}<tr style="height:20px;">{/if}
            <td><a href="javascript:setTarget({$ColonyRow.galaxy},{$ColonyRow.system},{$ColonyRow.planet},{$ColonyRow.planet_type});shortInfo();">{$ColonyRow.name} {if $ColonyRow.planet_type == 3}{$fl_moon_shortcut}{/if}[{$ColonyRow.galaxy}:{$ColonyRow.system}:{$ColonyRow.planet}]</a></td>
			{if $smarty.foreach.ColonyList.last && $smarty.foreach.ColonyList.total is odd}<td>&nbsp;</td>{/if}
			{if $smarty.foreach.ColonyList.iteration is even}</tr>{/if}
			{foreachelse}
			<tr style="height:20px;">
				<td colspan="2">{$fl_no_colony}</td>
			</tr>
			{/foreach}
			{if $AKSList}
            <tr style="height:20px;">
                <th colspan="2">{$fl_acs_title}</th>
            </tr>
            {foreach item=AKSRow from=$AKSList}
			<tr style="height:20px;"><td colspan="2">
			<a href="javascript:setTarget({$AKSRow.galaxy},{$AKSRow.system},{$AKSRow.planet},{$AKSRow.planet_type});shortInfo();setACS({$AKSRow.id});setACS_target('g{$AKSRow.galaxy}s{$AKSRow.system}p{$AKSRow.planet}t{$AKSRow.planet_type}');">{$AKSRow.name} - [{$AKSRow.galaxy}:{$AKSRow.system}:{$AKSRow.planet}]</a>
			</td></tr>
			{/foreach}
			{/if}
            <tr style="height:20px;">
            	<td colspan="2"><input type="submit" value="{$fl_continue}"></td>
            </tr>
        </table>
    </div>
</form>
<script type="text/javascript"> 
$(document).ready(function() {
	shortInfo();
 });
</script>
{include file="planet_menu.tpl"}
{include file="overall_footer.tpl"}
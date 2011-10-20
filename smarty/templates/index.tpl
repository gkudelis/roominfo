{extends file="base.tpl"}

{block name=css append}
<link rel="stylesheet" type="text/css" href="css/index.css" />
{/block}

{block name=js append}
<script type="text/javascript" src="js/clock.js"></script>
{/block}

{block name=header}
<h1>Guild of Students Room Bookings<div id="clock"><span id="hours"></span>:<span id="minutes"></span></h1>
{/block}

{block name=content}
<table id="bookings">
<tr id="times">
	<td>Rooms</td>
    {foreach from=$times item=time}
        <td>{$time['formatted']}</td>
    {/foreach}
</tr>
{foreach from=$rooms item=room}
	<tr id="{$room['Room_ID']}" class="room_row">
		<td class="room_name"><div class="height_limit">{$room['Meeting Room']}</div></td>
		{assign var=last_end value=-1}
		{foreach from=$times item=time}
			{if $time['raw'] >= $last_end} 
				{if isset($reservations[$room['Room_ID']][$time['raw']])}
					{assign var=res value=$reservations[$room['Room_ID']][$time['raw']]}
					{assign var=last_end value=$res['begins']+$res['duration']}
					<td colspan={$res['colspan']} class="taken"><div class="height_limit">
					{$res['title']}</div></td>
				{else}
					<td></td>
				{/if}
			{/if}
		{/foreach}
	</tr>
{/foreach}
</table>

<form action="" method="GET">
    <button class="corner" id="left" type="submit">&lsaquo;</button>
</form>

<form action="" method="GET">
    <button class="corner" id="right" type="submit">&rsaquo;</button>
</form>
{/block}

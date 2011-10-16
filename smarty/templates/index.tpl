{extends file="base.tpl"}

{block name=css append}
<link rel="stylesheet" type="text/css" href="css/index.css" />
{/block}

{block name=header}
<h1>Guild of Students Room Bookings</h1>
{/block}

{block name=content}
<table id="bookings">
<tr id="times">
	<td>Rooms</td>
    {foreach from=$times item=time}
        <td>{$time}</td>
    {/foreach}
</tr>

<tr id="mandella" class="room_row">
	<td class="room_name">Mandella</td>
	<td colspan=2 class="taken">This room is reserved by the president</td>
	<td></td>
	<td></td>
	<td></td>
	<td></td>
	<td></td>
</tr>
</table>

<form action="" method="GET">
    <button class="corner" id="left" type="submit">&lsaquo;</button>
</form>

<form action="" method="GET">
    <button class="corner" id="right" type="submit">&rsaquo;</button>
</form>
{/block}

<?php
require('settings.php');

// load Smarty library
require(SMARTY_DIR.'Smarty.class.php');

$smarty = new Smarty;

$smarty->template_dir = ROOT_DIR.'smarty/templates';
$smarty->config_dir = ROOT_DIR.'smarty/config';
$smarty->cache_dir = ROOT_DIR.'smarty/cache';
$smarty->compile_dir = ROOT_DIR.'smarty/templates_c';

# Connect to the database
mssql_connect(DBSERVER, DBUSERNAME, DBPASSWORD);
mssql_select_db(DBNAME);

# Get current time
$now_time = time();

# Get start time of the display
# Note: all in UNIX timestamps - easy to work with
$start_time = $now_time - $now_time % (30*60) - (TIME_SPAN * 60 * 60);
$end_time = $start_time + (30 * 60) + 2*(TIME_SPAN * 60 * 60);

# Loop over every half hour and add that to times array
$times = array();
$cur_time = $start_time;
while ($cur_time < $end_time) {
    $times[] = array(
		'formatted' => date('G:i', $cur_time),
		'raw' => $cur_time
	);
    $cur_time += 30 * 60;
}

# Select all rooms for DB
$rooms = array();
$res = mssql_query("SELECT Room_ID, [Meeting Room] FROM Rooms ORDER BY [Meeting Room]");
while ($row = mssql_fetch_assoc($res))
	if (in_array($row['Meeting Room'], array_keys($show_rooms))) {
		$new_room = array(
			'Room_ID' => $row['Room_ID'],
			'Meeting Room' => $show_rooms[$row['Meeting Room']]
		);
		$rooms[] = $new_room;
	}

# Query the DB for reservations in the current time period
$reservations = array();
# Convert to UNIX timestamps!
$res = mssql_query('SELECT Rooms.[Meeting Room], Rooms.[Room_ID], [Cell Color].Name, '.
	'CAST(Reservations.[General/Meeting Title] AS TEXT) AS title, '.
	'DATEDIFF(s, \'19700101\', Reservations.[Actual Start]) AS begins, '.
	'DATEDIFF(s, Reservations.[Actual Start], Reservations.[Actual End]) AS duration '.
	'FROM Reservations INNER JOIN Rooms ON Reservations.Room_ID = Rooms.Room_ID '.
	'INNER JOIN [Cell Color] ON Reservations.CellColor_ID = [Cell Color].CellColor_ID '.
	'WHERE (DATEDIFF(mi, DATEADD(mi, - 180, CURRENT_TIMESTAMP), Reservations.[Actual End]) > 0) '.
	'AND (DATEDIFF(mi, Reservations.[Actual Start], DATEADD(mi, 180, CURRENT_TIMESTAMP)) > 0) '.
	'ORDER BY Rooms.[Meeting Room], begins');

while ($row = mssql_fetch_assoc($res))
	if (in_array($row['Meeting Room'], array_keys($show_rooms))) {
		# Clip off what comes before the start-time of our table
		if ($row['begins'] < $start_time)
			$row['begins'] = $start_time;
			
		# Clip off what comes after the end-time of our table
		if ($row['begins'] + $row['duration'] > $end_time)
			$row['duration'] = $end_time - $row['begins'];
			
		# Format duration to half-hours
		# So it can be used directly in colspan
		$row['colspan'] = $row['duration'] / (30*60);
			
		# Add row to reservations
		if (!isset($reservations[$row['Room_ID']]))
			$reservations[$row['Room_ID']] = array();
		
		$reservations[$row['Room_ID']][$row['begins']] = $row;
	}	
	
## Disconnect from the database
mssql_close();

$smarty->assign('reservations', $reservations);
$smarty->assign('rooms', $rooms);
$smarty->assign('times', $times);
$smarty->display('index.tpl');
?>

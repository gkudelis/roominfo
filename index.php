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

# Get current time and extract the last half-hour mark
$now_time = localtime(time(), true);
$mins = $now_time['tm_min'];
if ($mins > 30) {
    $mins_back = $mins - 30;
} else {
    $mins_back = $mins;
}
$mins_forward = 30 - $mins_back;

# Get start time of the display
$start_time = time() - $mins_back * 60 - (1.5 * 60 * 60);
$end_time = time() + $mins_forward * 60 + (1.5 * 60 * 60);

# Loop over every half hour and add that to times array
$times = array();
$cur_time = $start_time;
while ($cur_time < $end_time) {
    $times[] = date('G:i', $cur_time);
    $cur_time += 30 * 60;
}

# Select all rooms for DB
$rooms = array();
$res = mssql_query("SELECT Room_ID, [Meeting Room] FROM Rooms");
while ($row = mssql_fetch_assoc($res)) $rooms[] = $row;

## Query the DB for reservations in the current time period
#$reservations = array();
#$res = mssql_query("SELECT ... FROM ... WHERE ... ORDER BY room name, start_time");
#while ($row = mssql_fetch_assoc($res)) {
#    $room_name = $row['room_name'];
#    if !isset($reservations[$room_name])
#        $reservations[$room_name] = array();
#    $reservations[$room_name][] = $row;
#    # Calculate duration of the reservation to figure out colspan
#}

## Disconnect from the database
mssql_close();

$smarty->assign('rooms', $rooms);
$smarty->assign('times', $times);
$smarty->display('index.tpl');
?>

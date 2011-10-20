function correctFormat(time) {
	if (time < 10) time='0'+time;
	return time;
}

function updateClock() {
	var now = new Date();
	var hours = now.getHours(), minutes = now.getMinutes();
	hours = correctFormat(hours);
	minutes = correctFormat(minutes);
		
	$('div#clock span#hours').text(hours);
	$('div#clock span#minutes').text(minutes);
}

$(document).ready(function() {
	updateClock();
	setInterval('updateClock()', 1000);
});
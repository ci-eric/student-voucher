// JavaScript Document

$months = {
	'Jan' : '01',
    'Feb' : '02',
	'Mar' : '03',
    'Apr' : '04',
	'May' : '05',
    'Jun' : '06',
	'Jul' : '07',
    'Aug' : '08',
	'Sep' : '09',
    'Oct' : '10',
	'Nov' : '11',
    'Dec' : '12'
}

$offset = 0;

function getDays(month, current) {
	var splitMonth = month.split(' ');
	if (current) {
		return new Date(parseInt(splitMonth[1]), $months[splitMonth[0]], 0).getDate();
	}
	else {
		return new Date(parseInt(splitMonth[1]), $months[splitMonth[0]]-1, 0).getDate();
	}
}

function monthStart() {
	var row = 1;
	var month = $('#month_widget').val();
	var daysInMonth = getDays(month, true);
	var daysInLastMonth = getDays(month, false);
	var day = daysInLastMonth - new Date(month).getDay() + 1 + ($offset * 7);
	var currentDay = $("#t1 td:nth-child(1)");
	
	for (day; day <= daysInLastMonth; day++) {
		if (currentDay.hasClass('weekly')) {
			row++;
			currentDay = $("#t"+row+" td:nth-child(1)");

			}
			currentDay.find("div").html(day).addClass('notCurrent');
		currentDay = currentDay.next();
	}
	
	for (day = 1; day <= daysInMonth; day++) {
		if (currentDay.hasClass('weekly')) {
			row++;
			currentDay = $("#t"+row+" td:nth-child(1)");
		}
		
		currentDay.find("div").html(day);
		currentDay = currentDay.next();
	}
	
	for (day = 1; row <= 5; day++) {
		if (currentDay.hasClass('weekly')) {
			row++;
			currentDay = $("#t"+row+" td:nth-child(1)");
		}
		
		currentDay.find("div").html(day).addClass('notCurrent');
		currentDay = currentDay.next();
	}
}

function calculate() {
	var day = 1;
	var row = 1;
	var total = 0;
	var weekTotal = 0;
	var month = $('#month_widget').val();
	var daysInMonth = getDays(month);
	var currentDay = $("#t1 td:nth-child(1)");
	var running = true;
	
	while (running) {
		var value = parseFloat(currentDay.find("input").val());
		if (value) {
			weekTotal += value;
		}
		
		currentDay = currentDay.next();
		
		if (currentDay.hasClass('weekly')) {
			currentDay.html(weekTotal);
			total += weekTotal;
			weekTotal = 0;
			row++;
			if (row <= 5) {
				currentDay = $("#t"+row+" td:nth-child(1)");
			}
			else {
				running = false;
			}
		}
	}
	//test
	$("#t5 td:nth-child(9)").find('#total').html(total);
	
	console.log(total);
}

function resetCalendar() {
	$('.date').empty().removeClass('notCurrent');
	$('.weekly').html(0);
	$('.total').find('#total').html(0);
	monthStart();
}

function upWeek() {
    if ($offset < 1) {
        $offset++;
        resetCalendar();
	}
}
function downWeek() {
  	if ($offset > -1) {
        $offset--;
        resetCalendar();
	}
}


$(document).ready(function() {
    $('#month_widget').monthpicker();
});
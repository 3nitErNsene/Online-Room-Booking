$(document).ready(function(){
	$("#datepick").datepicker({
		maxDate: "+1m +3w + 3d",
		minDate: "today",
		dateFormat: 'yy-mm-dd'
	});
	$(".timepick").timepicker({
		'minTime': '9:00am',
		'maxTime': '7:00pm',
		'timeFormat': 'H:i',
		'disableTextInput': true,
		// 'noneOption': [{
		// 	'label': 'null',
  //           'value': ''
		// }]
	});
});
$(function()
{
    /*
     * bootstrap-timepicker
     */
    $('[id^="timepicker"]').timepicker();
    $('#timepicker1').timepicker();
    $('#timepicker2').timepicker({
        minuteStep: 1,
        template: 'modal',
        showSeconds: true,
        showMeridian: false,
        modalBackdrop: true
    });
    $('#timepicker3').timepicker({
        minuteStep: 5,
        showInputs: false,
        disableFocus: true
    });
    $('#timepicker4').timepicker({
        minuteStep: 1,
        secondStep: 5,
        showInputs: false,
        showSeconds: true,
        showMeridian: false
    });
    $('#timepicker5').timepicker({
        template: false,
        showInputs: false,
        minuteStep: 5
    });
	$('[id^="timepick"]').timepicker({
        minuteStep: 5,
        showSeconds: false,
        showMeridian: false
    });
    $('[id^="convertTime"]').timepicker({
        showInputs: false,
        template: 'modal',
        modalBackdrop: true
    });
	
});

$(function()
{

/* Select2 - Advanced Select Controls */
	if (typeof $.fn.select2 != 'undefined')
	{
		// ID
		if ($('[id^="select2_"]').length)
			$('[id^="select2_"]').select2();
		
		// Basic
		if ($('#select2_1').length)
			$('#select2_1').select2();
		
		// Multiple
		if ($('#select2_2').length)
			$('#select2_2').select2();
		
		// Placeholders
		$("#select2_3").select2({
			placeholder: "Select a State",
			allowClear: true
		});
		$("#select2_4").select2({
		    placeholder: "Select a State",
		    allowClear: true
		});
		
		// tagging support
		$("#select2_5").select2({tags:["red", "green", "blue"]});
		
		// enable/disable mode
		$("#select2_6_1").select2();
		$("#select2_6_2").select2();
		$("#select2_6_enable").click(function() { $("#select2_6_1,#select2_6_2").select2("enable"); });
		$("#select2_6_disable").click(function() { $("#select2_6_1,#select2_6_2").select2("disable"); });

		// templating
		function format(state) {
		    if (!state.id) return state.text; // optgroup
		    return "<img class='flag' src='http://ivaynberg.github.com/select2/images/flags/" + state.id.toLowerCase() + ".png'/>" + state.text;
		}
		$("#select2_7").select2({
		    formatResult: format,
		    formatSelection: format,
		    escapeMarkup: function(m) { return m; }
		});
	}



});
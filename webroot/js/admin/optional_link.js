/*
 * jQuery - OptionalLink
 *
 */
$(function () {
	var optionalLinkStatusVal = $("input[name='data[OptionalLink][status]']:checked").val();
	if (optionalLinkStatusVal != 1) {
		optionalLinkStatusChangeHandler();
	}
	// status の切り替えイベント
	$("input[name='data[OptionalLink][status]']").click(function () {
		var optionalLinkStatusVal = $("input[name='data[OptionalLink][status]']:checked").val();
		if (optionalLinkStatusVal != 1) {
			optionalLinkStatusChangeHandler();
		} else {
			$('#OptionalLinkName').attr('disabled', false);
			$('#OptionalLinkName').css('background-color', '');
			$('#OptionalLinkBlank').attr('disabled', false);
			$('label[for="OptionalLinkBlank"]').css('color', '');
			$('#OptionalLinkNolink').attr('disabled', false);
			$('label[for="OptionalLinkNolink"]').css('color', '');
			$("#OptionalLinkName").focus();
		}
	});
	// nolink の切り替えイベント
	$('#OptionalLinkNolink').click(function () {
		var optionalLinkStatusVal = $("input[name='data[OptionalLink][status]']:checked").val();
		if (optionalLinkStatusVal == 1) {
			optionalLinkValueChengeHandler();
		}
	});
	
	// status 値による切り替え動作
	function optionalLinkStatusChangeHandler() {
		$('#OptionalLinkName').attr('disabled', true);
		$('#OptionalLinkName').css('background-color', '#CCC');
		$('#OptionalLinkBlank').attr('disabled', true);
		$('label[for="OptionalLinkBlank"]').css('color', '#CCC');
		$('#OptionalLinkNolink').attr('disabled', true);
		$('label[for="OptionalLinkNolink"]').css('color', '#CCC');
	}
	// nolink 値による切り替え動作
	function optionalLinkValueChengeHandler() {		
		var judge = $('#OptionalLinkNolink').prop('checked');
		if (judge) {
			$('#OptionalLinkName').attr('disabled', true);
			$('#OptionalLinkName').css('background-color', '#CCC');
			$('#OptionalLinkBlank').attr('disabled', true);
			$('label[for="OptionalLinkBlank"]').css('color', '#CCC');
		} else {
			$('#OptionalLinkName').attr('disabled', false);
			$('#OptionalLinkName').css('background-color', '');
			$('#OptionalLinkBlank').attr('disabled', false);
			$('label[for="OptionalLinkBlank"]').css('color', '');
		}
	}
});

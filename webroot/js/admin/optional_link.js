/*
 * jQuery - OptionalLink
 *
 */
$(function () {
	var optionalLinkStatusVal = $("input[name='data[OptionalLink][status]']:checked").val();
	if (optionalLinkStatusVal != 1) {
		optionalLinkStatusChangeHandler();
	}
	var judge = $('#OptionalLinkNolink').prop('checked');
	if (judge) {
		optionalLinkNolinkChengeHandler();
	}
	
	// status の切り替えイベント
	$("input[name='data[OptionalLink][status]']").click(function () {
		var optionalLinkStatusVal = $("input[name='data[OptionalLink][status]']:checked").val();
		if (optionalLinkStatusVal != 1) {
			optionalLinkStatusChangeHandler();
		} else {
			var judge = $('#OptionalLinkNolink').prop('checked');
			if (!judge) {
				$('#OptionalLinkName').attr('readonly', false);
				$('#OptionalLinkName').css('background-color', '');
				$('#OptionalLinkBlank').attr('disabled', false);
				$('label[for="OptionalLinkBlank"]').css('color', '');
			}
			$('#OptionalLinkNolink').attr('disabled', false);
			$('label[for="OptionalLinkNolink"]').css('color', '');
			$("#OptionalLinkName").focus();
		}
	});
	// nolink の切り替えイベント
	$('#OptionalLinkNolink').click(function () {
		var optionalLinkStatusVal = $("input[name='data[OptionalLink][status]']:checked").val();
		if (optionalLinkStatusVal == 1) {
			var judge = $('#OptionalLinkNolink').prop('checked');
			if (judge) {
				optionalLinkNolinkChengeHandler();
			} else {
				$('#OptionalLinkName').attr('readonly', false);
				$('#OptionalLinkName').css('background-color', '');
				$('#OptionalLinkBlank').attr('disabled', false);
				$('label[for="OptionalLinkBlank"]').css('color', '');
			}
		}
	});
	
	// status 値による切り替え動作
	function optionalLinkStatusChangeHandler() {
		$('#OptionalLinkName').attr('readonly', true);
		$('#OptionalLinkName').css('background-color', '#CCC');
		$('#OptionalLinkBlank').attr('disabled', true);
		$('label[for="OptionalLinkBlank"]').css('color', '#CCC');
		$('#OptionalLinkNolink').attr('disabled', true);
		$('label[for="OptionalLinkNolink"]').css('color', '#CCC');
	}
	// nolink 値による切り替え動作
	function optionalLinkNolinkChengeHandler() {
		$('#OptionalLinkName').attr('readonly', true);
		$('#OptionalLinkName').css('background-color', '#CCC');
		$('#OptionalLinkBlank').attr('disabled', true);
		$('label[for="OptionalLinkBlank"]').css('color', '#CCC');
	}
});

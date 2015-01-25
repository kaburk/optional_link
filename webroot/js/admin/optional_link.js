/**
 * [ADMIN] OptionalLink
 *
 * @link			http://www.materializing.net/
 * @author			arata
 * @license			MIT
 */
$(function () {
	changeOptionalView();
	// status の切り替えイベント
	$(".optionallink-status").change(function(){
		changeOptionalView();
	});
	// status の値に応じたフォーム表示切替え
	function changeOptionalView(){
		var optionalLinkStatusVal = $(".optionallink-status:checked").val();
		$urlArea      = $("#OptionalLinkName").parents('.section');
		$fileArea     = $("#OptionalLinkFile").parents('.section');
		$urlArea.slideUp();
		$fileArea.slideUp();
		
		// ステータス値により表示切替え
		switch (optionalLinkStatusVal) {
			case '0':
				break;

			case '1':
				$urlArea.slideDown();
				optionalLinkNolinkChengeHandler();
				break;

			case '2':
				$fileArea.slideDown();
				break;
		}
	}
	
	// nolink の切り替えイベント
	$('#OptionalLinkNolink').click(function () {
		optionalLinkNolinkChengeHandler();
	});
	// nolink 値による切り替え動作
	function optionalLinkNolinkChengeHandler() {
		var judge = $('#OptionalLinkNolink').prop('checked');
		if (!judge) {
			$('#OptionalLinkName').attr('readonly', false);
			$('#OptionalLinkName').css('background-color', '');
			$('#OptionalLinkBlank').attr('disabled', false);
			$('label[for="OptionalLinkBlank"]').css('color', '');
		} else {
			$('#OptionalLinkName').attr('readonly', true);
			$('#OptionalLinkName').css('background-color', '#CCC');
			$('#OptionalLinkBlank').attr('disabled', true);
			$('label[for="OptionalLinkBlank"]').css('color', '#CCC');
		}
		// $('#OptionalLinkNolink').attr('disabled', false);
		// $('label[for="OptionalLinkNolink"]').css('color', '');
	}
});

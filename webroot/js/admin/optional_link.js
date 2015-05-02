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
	
	/**
	 * 公開期間クリアボタンを押下した際に、ダイアログを表示して期間指定をクリアする
	 */
	$("#BtnClearOptionalLinkPublish").click(function() {
		var dialogId = '#' + $(this).attr('id') + 'Dialog';
		var dialogTitle = $(dialogId).find('.dialog-property').find('h3').html();
		var dialogWidth = $(dialogId).find('.dialog-property').find('.width').html();
		// TODO ボタン用のテキストを動的に設定する
		//var dialogBtnCancel = $(dialogId).find('.dialog-property').find('.btn-cancel').html();
		//var dialogBtnOk = $(dialogId).find('.dialog-property').find('.btn-ok').html();
		$(dialogId).dialog({
			modal: true,
			title: dialogTitle,
			width: dialogWidth,
			buttons: {
				'キャンセル': function() {
					$(this).dialog("close");
				},
				'OK': function() {
					$('#OptionalLinkPublishBeginDate').val('');
					$('#OptionalLinkPublishBeginTime').val('');
					$('#OptionalLinkPublishEndDate').val('');
					$('#OptionalLinkPublishEndTime').val('');
					$(this).dialog("close");
				}
			}
		});
		return false;
	});
	
	/**
	 * ファイルに画像を登録した際、表示画像が150pxより大きい場合は小さくして表示する
	 */
	resizeUploadImage();
	function resizeUploadImage() {
		$imageFile = $('#OptionalLinkTable .upload-file img');
		$imageFile.addClass('optional-thumbnail');
		
		imgHeight = '';
		imgHeight = $imageFile.height();
		console.log(imgHeight);
		if (imgHeight) {
			if (imgHeight > 200) {
				$imageFile.attr({'height': 200});
			}
		}
	}
});

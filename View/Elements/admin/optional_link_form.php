<?php
/**
 * [ADMIN] OptionalLink
 *
 * @link			http://www.materializing.net/
 * @author			arata
 * @license			MIT
 */
$blogContentId = $this->BcForm->value('OptionalLink.blog_content_id');
if (!$blogContentId) {
	$blogContentId = $blogContent['BlogContent']['id'];
}
?>
<?php if($this->request->params['action'] != 'admin_add'): ?>
	<?php echo $this->BcForm->input('OptionalLink.id', array('type' => 'hidden')) ?>
	<?php echo $this->BcForm->input('OptionalLink.blog_content_id', array('type' => 'hidden', 'value' => $blogContentId)) ?>
<?php else: ?>
	<?php echo $this->BcForm->input('OptionalLink.blog_content_id', array('type' => 'hidden', 'value' => $blogContentId)) ?>
<?php endif ?>

<?php $this->BcBaser->css('OptionalLink.admin/optional_link', array('inline' => false)); ?>
<?php $this->BcBaser->js(array('OptionalLink.admin/optional_link'), array('inline' => true)) ?>
<div id="OptionalLinkTable">
<table cellpadding="0" cellspacing="0" class="form-table section" style="margin-bottom: 10px;">
	<tr>
		<th class="col-head">
			<?php echo $this->BcForm->label('OptionalLink.status', 'オプショナルリンク') ?>
		</th>
		<td class="col-input">
			<?php $labelUrl = (Hash::get($this->request->data, 'OptionalLink.name')) ? 'URL<small>（指定アリ）</small>' : 'URL'; ?>
			<?php $labelFile = (Hash::get($this->request->data, 'OptionalLink.file')) ? 'ファイル<small>（指定アリ）</small>' : 'ファイル'; ?>
			<?php echo $this->BcForm->input('OptionalLink.status', array(
					'type'		=> 'radio',
					'options'	=> array(0 => '利用しない', 1 => $labelUrl, 2 => $labelFile),
					'legend'	=> false,
					'class'		=> 'optionallink-status',
					'separator'	=> '&nbsp;&nbsp;')) ?>
			<?php echo $this->BcForm->error('OptionalLink.status') ?>
		</td>
	</tr>
</table>

<div class="section">
<table cellpadding="0" cellspacing="0" class="form-table section">
	<tr>
		<th class="col-head">
			<?php echo $this->BcForm->label('OptionalLink.name', 'URL') ?>
		</th>
		<td class="col-input">
			<?php echo $this->BcForm->input('OptionalLink.name', array('type' => 'text', 'size' => 68, 'maxlength' => 255, 'counter' => true)) ?>
			<?php echo $this->BcForm->error('OptionalLink.name') ?>
			<br />
			<?php echo $this->BcForm->input('OptionalLink.blank', array('type' => 'checkbox', 'label' => '別ウィンドウ（タブ）で開く')) ?>
			<?php echo $this->BcForm->error('OptionalLink.blank') ?>
			<?php echo $this->BcBaser->img('admin/icn_help.png', array('id' => 'helpOptionalLinkName', 'class' => 'btn help', 'alt' => 'ヘルプ')) ?>
			<div id="helptextOptionalLinkName" class="helptext">
				<ul>
					<li>サイト内へのリンクを利用する際は「/」から始まる絶対パスで指定してください。</li>
					<li>「http://〜」から記述した場合、リンクにプレフィックスがつかなくなりスマホ、モバイルでも共通のリンク指定となります。</li>
					<li>「/files/〜」（アップローダでの管理ファイル）から記述した場合、リンクにプレフィックスがつかなくなりスマホ、モバイルでも共通のリンク指定となります。</li>
				</ul>
			</div>
			<?php echo $this->BcForm->input('OptionalLink.nolink', array('type' => 'checkbox', 'label' => 'リンクなし')) ?>
			<?php echo $this->BcForm->error('OptionalLink.nolink') ?>
		</td>
	</tr>
</table>
<!-- /.section --></div>

<div class="section">
<table cellpadding="0" cellspacing="0" class="form-table section">
	<tr>
		<th class="col-head">
			<?php echo $this->BcForm->label('OptionalLink.file', 'ファイル') ?>
		</th>
		<td class="col-input">
			<?php echo $this->BcForm->file('OptionalLink.file', array('link' => true, 'imgsize' => 'thumb')) ?>
			<?php echo $this->BcForm->error('OptionalLink.file') ?>

			<?php echo $this->BcForm->label('OptionalLink.publish_begin', '公開期間指定') ?>
			<?php echo $this->BcForm->dateTimePicker('OptionalLink.publish_begin', array('size' => 12, 'maxlength' => 10), true) ?>
			&nbsp;〜&nbsp;
			<?php echo $this->BcForm->dateTimePicker('OptionalLink.publish_end', array('size' => 12, 'maxlength' => 10), true) ?>
			<?php echo $this->BcForm->error('OptionalLink.publish_begin') ?>
			<?php echo $this->BcForm->error('OptionalLink.publish_end') ?>
			&nbsp;&nbsp;&nbsp;<?php echo $this->BcForm->input('公開期間クリア', array('type' => 'button', 'div' => false, 'id' => 'BtnClearOptionalLinkPublish')) ?>
			<br /><small>※管理システムにログイン中は、公開期間を指定しているファイルにアクセスすることができます。
			<!-- <p class="link"><?php echo $this->OptionalLink->file($this->request->data) ?></p> --></small>
			<div id="BtnClearOptionalLinkPublishDialog" class="display-none">
				<p>公開期間指定欄の開始日時と終了日時を空にします。</p>
				<p>よろしいですか？</p>
				<div class="dialog-property display-none">
					<h3>公開期間指定欄</h3>
					<span class="width">360</span>
					<span class="btn-cancel">キャンセル</span>
					<span class="btn-ok">OK</span>
				</div>
			</div>
		</td>
	</tr>
</table>
<!-- /.section --></div>
<!-- /#OptionalLinkTable --></div>

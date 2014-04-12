<?php
/**
 * [ADMIN] OptionalLink
 *
 * @link			http://www.materializing.net/
 * @author			arata
 * @license			MIT
 */
?>
<?php if($this->request->params['action'] != 'admin_add'): ?>
	<?php echo $this->BcForm->input('OptionalLink.id', array('type' => 'hidden')) ?>
	<?php echo $this->BcForm->input('OptionalLink.blog_content_id', array('type' => 'hidden')) ?>
<?php else: ?>
	<?php echo $this->BcForm->input('OptionalLink.blog_content_id', array('type' => 'hidden', 'value' => $blogContent['BlogContent']['id'])) ?>
<?php endif ?>

<?php if($this->request->data['OptionalLinkConfig']['status']): ?>
<?php $this->BcBaser->js(array('OptionalLink.admin/optional_link'), array('inline' => true)) ?>
<div id="OptionalLinkTable">
<table cellpadding="0" cellspacing="0" class="form-table section">
	<tr>
		<th class="col-head">
			<?php echo $this->BcForm->label('OptionalLink.status', 'オプショナルリンク') ?>
		</th>
		<td class="col-input">
			<?php echo $this->BcForm->input('OptionalLink.status', array(
					'type'		=> 'radio',
					'options'	=> $this->BcText->booleanDoList('利用'),
					'legend'	=> false,
					'separator'	=> '&nbsp;&nbsp;')) ?>
			<?php echo $this->BcForm->error('OptionalLink.status') ?>
		</td>
	</tr>
	<tr>
		<th class="col-head">
			<?php echo $this->BcForm->label('OptionalLink.name', 'URL') ?>
		</th>
		<td class="col-input">
			<?php echo $this->BcForm->input('OptionalLink.name', array('type' => 'text', 'size' => 68, 'maxlength' => 255, 'counter' => true)) ?>
			<?php echo $this->BcForm->error('OptionalLink.name') ?>
			<br />
			<?php echo $this->BcForm->input('OptionalLink.blank', array('type' => 'checkbox', 'label' => '別窓で開く')) ?>
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
</div>
<?php else: ?>
	<?php echo $this->BcForm->input('OptionalLink.status', array('type' => 'hidden')) ?>
	<?php echo $this->BcForm->input('OptionalLink.name', array('type' => 'hidden')) ?>
	<?php echo $this->BcForm->input('OptionalLink.blank', array('type' => 'hidden')) ?>
	<?php echo $this->BcForm->input('OptionalLink.nolink', array('type' => 'hidden')) ?>
<?php endif ?>

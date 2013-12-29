<?php
/**
 * [ADMIN] OptionalLink
 *
 * @link			http://www.materializing.net/
 * @author			arata
 * @license			MIT
 */
?>
<?php if($this->params['controller'] != 'blog_posts'): ?>
<script type="text/javascript">
$(window).load(function() {
	$("#OptionalLinkName").focus();
});
</script>
<?php endif ?>

<?php if($this->action != 'admin_add'): ?>
	<?php echo $this->BcForm->input('OptionalLink.id', array('type' => 'hidden')) ?>
<?php endif ?>

<div id="OptionalLinkTable">

<table cellpadding="0" cellspacing="0" class="form-table section">
<?php if($this->params['controller'] != 'blog_posts'): ?>
	<tr>
		<th class="col-head"><?php echo $this->BcForm->label('OptionalLink.id', 'NO') ?></th>
		<td class="col-input">
			<?php echo $this->BcForm->value('OptionalLink.id') ?>
		</td>
	</tr>
	<tr>
		<th class="col-head">ブログ名</th>
		<td class="col-input">
			<ul>
				<li><?php echo $blogContentDatas[$this->BcForm->value('OptionalLink.blog_content_id')] ?></li>
			</ul>
		</td>
	</tr>
<?php endif ?>
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
			<?php echo $this->BcForm->input('OptionalLink.name', array('type' => 'text', 'size' => 74, 'maxlength' => 255, 'counter' => true)) ?>
			<?php echo $this->BcForm->error('OptionalLink.name') ?>
			<br />
			<?php echo $this->BcForm->input('OptionalLink.blank', array('type' => 'checkbox', 'label' => '別窓で開く')) ?>
			<?php echo $this->BcForm->error('OptionalLink.blank') ?>
			<?php echo $this->BcBaser->img('admin/icn_help.png', array('id' => 'helpOptionalLinkName', 'class' => 'btn help', 'alt' => 'ヘルプ')) ?>
			<div id="helptextOptionalLinkName" class="helptext">
				<ul>
					<li>サイト内へのリンクを利用する際は「/」から始まる絶対パスで指定してください。</li>
				</ul>
			</div>
		</td>
	</tr>
</table>
</div>

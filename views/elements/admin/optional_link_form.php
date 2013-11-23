<?php
/**
 * [ADMIN] optional_link
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
	<?php echo $bcForm->input('OptionalLink.id', array('type' => 'hidden')) ?>
<?php endif ?>

<div id="OptionalLinkTable">

<table cellpadding="0" cellspacing="0" class="form-table section">
<?php if($this->params['controller'] != 'blog_posts'): ?>
	<tr>
		<th class="col-head"><?php echo $bcForm->label('OptionalLink.id', 'NO') ?></th>
		<td class="col-input">
			<?php echo $bcForm->value('OptionalLink.id') ?>
		</td>
	</tr>
	<tr>
		<th class="col-head">ブログ名</th>
		<td class="col-input">
			<ul>
				<li><?php echo $blogContentDatas[$bcForm->value('OptionalLink.blog_content_id')] ?></li>
			</ul>
		</td>
	</tr>
<?php endif ?>
	<tr>
		<th class="col-head">
			<?php echo $bcForm->label('OptionalLink.status', 'オプショナルリンク') ?>
		</th>
		<td class="col-input">
			<?php echo $bcForm->input('OptionalLink.status', array(
					'type'		=> 'radio',
					'options'	=> $bcText->booleanDoList('利用'),
					'legend'	=> false,
					'separator'	=> '&nbsp;&nbsp;')) ?>
			<?php echo $bcForm->error('OptionalLink.status') ?>
		</td>
	</tr>
	<tr>
		<th class="col-head">
			<?php echo $bcForm->label('OptionalLink.name', 'URL') ?>
		</th>
		<td class="col-input">
			<?php echo $bcForm->input('OptionalLink.name', array('type' => 'text', 'size' => 74, 'maxlength' => 255, 'counter' => true)) ?>
			<?php echo $bcForm->error('OptionalLink.name') ?>
			<br />
			<?php echo $bcForm->input('OptionalLink.blank', array('type' => 'checkbox', 'label' => '別窓で開く')) ?>
			<?php echo $bcForm->error('OptionalLink.blank') ?>
		</td>
	</tr>
</table>
</div>

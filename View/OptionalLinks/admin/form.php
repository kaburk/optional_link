<?php
/**
 * [ADMIN] OptionalLink
 *
 * @link			http://www.materializing.net/
 * @author			arata
 * @license			MIT
 */
?>
<?php if($this->action == 'admin_add'): ?>
	<?php echo $this->BcForm->create('OptionalLink', array('url' => array('action' => 'add'))) ?>
<?php else: ?>
	<?php echo $this->BcForm->create('OptionalLink', array('url' => array('action' => 'edit'))) ?>
	<?php echo $this->BcForm->input('OptionalLink.id', array('type' => 'hidden')) ?>
	<?php echo $this->BcForm->input('OptionalLink.blog_post_id', array('type' => 'hidden')) ?>
	<?php echo $this->BcForm->input('OptionalLink.blog_content_id', array('type' => 'hidden')) ?>
<?php endif ?>

<?php $this->BcBaser->element('optional_link_form') ?>

<div class="submit">
<?php if($this->action == 'admin_add'): ?>
	<?php echo $this->BcForm->submit('登録', array('div' => false, 'class' => 'btn-red button')) ?>
<?php else: ?>
	<?php echo $this->BcForm->submit('更新', array('div' => false, 'class' => 'btn-red button')) ?>
	<?php $this->BcBaser->link('削除',
		array('action' => 'delete', $this->BcForm->value('OptionalLink.id')),
		array('class' => 'btn-gray button'),
		sprintf('ID：%s のデータを削除して良いですか？', $this->BcForm->value('OptionalLink.id')),
		false); ?>
<?php endif ?>
</div>
<?php echo $this->BcForm->end() ?>

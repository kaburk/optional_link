<?php
/**
 * [ADMIN] optional_link
 *
 * @link			http://www.materializing.net/
 * @author			arata
 * @license			MIT
 */
?>
<?php if($this->action == 'admin_add'): ?>
	<?php echo $bcForm->create('OptionalLink', array('url' => array('action' => 'add'))) ?>
<?php else: ?>
	<?php echo $bcForm->create('OptionalLink', array('url' => array('action' => 'edit'))) ?>
	<?php echo $bcForm->input('OptionalLink.id', array('type' => 'hidden')) ?>
	<?php echo $bcForm->input('OptionalLink.blog_post_id', array('type' => 'hidden')) ?>
	<?php echo $bcForm->input('OptionalLink.blog_content_id', array('type' => 'hidden')) ?>
<?php endif ?>

<?php $bcBaser->element('optional_link_form') ?>

<div class="submit">
<?php if($this->action == 'admin_add'): ?>
	<?php echo $bcForm->submit('登録', array('div' => false, 'class' => 'btn-red button')) ?>
<?php else: ?>
	<?php echo $bcForm->submit('更新', array('div' => false, 'class' => 'btn-red button')) ?>
	<?php $bcBaser->link('削除',
		array('action' => 'delete', $bcForm->value('OptionalLink.id')),
		array('class' => 'btn-gray button'),
		sprintf('ID：%s のデータを削除して良いですか？', $bcForm->value('OptionalLink.id')),
		false); ?>
<?php endif ?>
</div>
<?php echo $bcForm->end() ?>

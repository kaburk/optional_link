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
	<?php echo $bcForm->create('OptionalLinkConfig', array('url' => array('action' => 'add'))) ?>
<?php else: ?>
	<?php echo $bcForm->create('OptionalLinkConfig', array('url' => array('action' => 'edit'))) ?>
	<?php echo $bcForm->input('OptionalLinkConfig.id', array('type' => 'hidden')) ?>
<?php endif ?>

<h2><?php echo $blogContentDatas[$this->data['OptionalLinkConfig']['blog_content_id']] ?></h2>
<?php $bcBaser->element('optional_link_config_form') ?>

<div class="submit">
	<?php echo $bcForm->submit('保　存', array('div' => false, 'class' => 'btn-red button')) ?>
</div>
<?php echo $bcForm->end() ?>

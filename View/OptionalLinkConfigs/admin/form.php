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
	<?php echo $this->BcForm->create('OptionalLinkConfig', array('url' => array('action' => 'add'))) ?>
<?php else: ?>
	<?php echo $this->BcForm->create('OptionalLinkConfig', array('url' => array('action' => 'edit'))) ?>
	<?php echo $this->BcForm->input('OptionalLinkConfig.id', array('type' => 'hidden')) ?>
<?php endif ?>

<h2><?php echo $blogContentDatas[$this->data['OptionalLinkConfig']['blog_content_id']] ?></h2>
<?php $bcBaser->element('optional_link_config_form') ?>

<div class="submit">
	<?php echo $this->BcForm->submit('保　存', array('div' => false, 'class' => 'btn-red button')) ?>
</div>
<?php echo $this->BcForm->end() ?>

<?php
/**
 * [ADMIN] optional_link
 *
 * @link			http://www.materializing.net/
 * @author			arata
 * @license			MIT
 */
?>
<?php echo $bcForm->create('OptionalLinkConfig', array('url' => array('action' => 'index'))) ?>
<p>
	<span>
		<?php echo $bcForm->label('OptionalLinkConfig.blog_content_id', 'ブログ') ?>
		&nbsp;<?php echo $bcForm->input('OptionalLinkConfig.blog_content_id', array('type' => 'select', 'options' => $blogContentDatas)) ?>
	</span>
	<span>
		<?php echo $bcForm->label('OptionalLinkConfig.status', '利用状態') ?>
		&nbsp;<?php echo $bcForm->input('OptionalLinkConfig.status', array('type' => 'select', 'options' => $bcText->booleanMarkList(), 'empty' => '指定なし')) ?>
	</span>
</p>
<div class="button">
	<?php echo $bcForm->submit('admin/btn_search.png', array('alt' => '検索', 'class' => 'btn'), array('div' => false, 'id' => 'BtnSearchSubmit')) ?>
</div>
<?php echo $bcForm->end() ?>

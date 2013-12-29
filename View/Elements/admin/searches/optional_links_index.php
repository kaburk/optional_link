<?php
/**
 * [ADMIN] optional_link
 *
 * @link			http://www.materializing.net/
 * @author			arata
 * @license			MIT
 */
?>
<?php echo $bcForm->create('OptionalLink', array('url' => array('action' => 'index'))) ?>
<p>
	<span>
		<?php echo $bcForm->label('OptionalLink.name', 'URL') ?>
		&nbsp;<?php echo $bcForm->input('OptionalLink.name', array('type' => 'text', 'size' => '40')) ?>
	</span>
	<br />
	<span>
		<?php echo $bcForm->label('OptionalLink.blog_content_id', 'ブログ') ?>
		&nbsp;<?php echo $bcForm->input('OptionalLink.blog_content_id', array('type' => 'select', 'options' => $blogContentDatas)) ?>
	</span>
	<span>
		<?php echo $bcForm->label('OptionalLink.status', '利用状態') ?>
		&nbsp;<?php echo $bcForm->input('OptionalLink.status', array('type' => 'select', 'options' => $bcText->booleanMarkList(), 'empty' => '指定なし')) ?>
	</span>
</p>
<div class="button">
	<?php echo $bcForm->submit('admin/btn_search.png', array('alt' => '検索', 'class' => 'btn'), array('div' => false, 'id' => 'BtnSearchSubmit')) ?>
</div>
<?php echo $bcForm->end() ?>

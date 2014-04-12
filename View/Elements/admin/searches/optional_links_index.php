<?php
/**
 * [ADMIN] OptionalLink
 *
 * @link			http://www.materializing.net/
 * @author			arata
 * @license			MIT
 */
?>
<?php echo $this->BcForm->create('OptionalLink', array('url' => array('action' => 'index'))) ?>
<p>
	<span>
		<?php echo $this->BcForm->label('OptionalLink.name', 'URL') ?>
		&nbsp;<?php echo $this->BcForm->input('OptionalLink.name', array('type' => 'text', 'size' => '40')) ?>
	</span>
	<br />
	<span>
		<?php echo $this->BcForm->label('OptionalLink.blog_content_id', 'ブログ') ?>
		&nbsp;<?php echo $this->BcForm->input('OptionalLink.blog_content_id', array('type' => 'select', 'options' => $blogContentDatas)) ?>
	</span>
	&nbsp;&nbsp;
	<span>
		<?php echo $this->BcForm->label('OptionalLink.status', '利用状態') ?>
		&nbsp;<?php echo $this->BcForm->input('OptionalLink.status', array('type' => 'select', 'options' => $this->BcText->booleanMarkList(), 'empty' => '指定なし')) ?>
	</span>
	&nbsp;&nbsp;
	<span>
		<?php echo $this->BcForm->label('OptionalLink.nolink', 'リンクなし') ?>
		&nbsp;<?php echo $this->BcForm->input('OptionalLink.nolink', array('type' => 'select', 'options' => $this->BcText->booleanMarkList(), 'empty' => '指定なし')) ?>
	</span>
</p>
<div class="button">
	<?php echo $this->BcForm->submit('admin/btn_search.png', array('alt' => '検索', 'class' => 'btn'), array('div' => false, 'id' => 'BtnSearchSubmit')) ?>
</div>
<?php echo $this->BcForm->end() ?>

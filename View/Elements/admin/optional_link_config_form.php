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
	<?php echo $this->BcForm->input('OptionalLinkConfig.id', array('type' => 'hidden')) ?>
<?php endif ?>

<div id="OptionalLinkConfigConfigTable">

<table cellpadding="0" cellspacing="0" class="form-table section">
<?php if($this->request->params['controller'] != 'blog_contents'): ?>
	<tr>
		<th class="col-head"><?php echo $this->BcForm->label('OptionalLinkConfig.id', 'NO') ?></th>
		<td class="col-input">
			<?php echo $this->BcForm->value('OptionalLinkConfig.id') ?>
		</td>
	</tr>
<?php endif ?>
	<tr>
		<th class="col-head">
			<?php echo $this->BcForm->label('OptionalLinkConfig.status', 'オプショナルリンクの利用') ?>
			<?php echo $this->BcBaser->img('admin/icn_help.png', array('id' => 'helpOptionalLinkConfigStatus', 'class' => 'btn help', 'alt' => 'ヘルプ')) ?>
			<div id="helptextOptionalLinkConfigStatus" class="helptext">
				<ul>
					<li>ブログ記事でのオプショナルリンクの利用の有無を指定します。</li>
				</ul>
			</div>
		</th>
		<td class="col-input">
			<?php echo $this->BcForm->input('OptionalLinkConfig.status', array('type' => 'radio', 'options' => $this->BcText->booleanDoList('利用'))) ?>
			<?php echo $this->BcForm->error('OptionalLinkConfig.status') ?>
		</td>
	</tr>
</table>
</div>

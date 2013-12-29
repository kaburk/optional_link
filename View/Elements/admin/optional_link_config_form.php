<?php
/**
 * [ADMIN] optional_link
 *
 * @link			http://www.materializing.net/
 * @author			arata
 * @license			MIT
 */
?>
<?php if($this->action != 'admin_add'): ?>
	<?php echo $bcForm->input('OptionalLinkConfig.id', array('type' => 'hidden')) ?>
<?php endif ?>

<div id="OptionalLinkConfigConfigTable">

<table cellpadding="0" cellspacing="0" class="form-table section">
<?php if($this->params['controller'] != 'blog_contents'): ?>
	<tr>
		<th class="col-head"><?php echo $bcForm->label('OptionalLinkConfig.id', 'NO') ?></th>
		<td class="col-input">
			<?php echo $bcForm->value('OptionalLinkConfig.id') ?>
		</td>
	</tr>
<?php endif ?>
	<tr>
		<th class="col-head">
			<?php echo $bcForm->label('OptionalLinkConfig.status', 'オプショナルリンクの利用') ?>
			<?php echo $bcBaser->img('admin/icn_help.png', array('id' => 'helpOptionalLinkConfigStatus', 'class' => 'btn help', 'alt' => 'ヘルプ')) ?>
			<div id="helptextOptionalLinkConfigStatus" class="helptext">
				<ul>
					<li>ブログ記事でのオプショナルリンクの利用の有無を指定します。</li>
				</ul>
			</div>
		</th>
		<td class="col-input">
			<?php echo $bcForm->input('OptionalLinkConfig.status', array('type' => 'radio', 'options' => $bcText->booleanDoList('利用'))) ?>
			<?php echo $bcForm->error('OptionalLinkConfig.status') ?>
		</td>
	</tr>
</table>
</div>

<?php
/**
 * [ADMIN] optional_link
 *
 * @link			http://www.materializing.net/
 * @author			arata
 * @license			MIT
 */
?>
<!-- pagination -->
<?php $bcBaser->element('pagination') ?>

<table cellpadding="0" cellspacing="0" class="list-table sort-table" id="ListTable">
	<thead>
		<tr><th style="width: 90px;">操作</th>
			<th><?php echo $paginator->sort(array(
					'asc' => $bcBaser->getImg('admin/blt_list_down.png', array('alt' => '昇順', 'title' => '昇順')).' NO',
					'desc' => $bcBaser->getImg('admin/blt_list_up.png', array('alt' => '降順', 'title' => '降順')).' NO'),
					'id', array('escape' => false, 'class' => 'btn-direction')) ?>
			</th>
			<th>
				<?php echo $paginator->sort(array(
					'asc' => $bcBaser->getImg('admin/blt_list_down.png', array('alt' => '昇順', 'title' => '昇順')).' ブログ名',
					'desc' => $bcBaser->getImg('admin/blt_list_up.png', array('alt' => '降順', 'title' => '降順')).' ブログ名'),
					'blog_content_id', array('escape' => false, 'class' => 'btn-direction')) ?>
			</th>
			<th><?php echo $paginator->sort(array(
					'asc' => $bcBaser->getImg('admin/blt_list_down.png', array('alt' => '昇順', 'title' => '昇順')).' '. 'URL',
					'desc' => $bcBaser->getImg('admin/blt_list_up.png', array('alt' => '降順', 'title' => '降順')).' '. 'URL'),
					'name', array('escape' => false, 'class' => 'btn-direction')) ?>
			</th>
			<th><?php echo $paginator->sort(array(
					'asc' => $bcBaser->getImg('admin/blt_list_down.png', array('alt' => '昇順', 'title' => '昇順')).' 別窓指定',
					'desc' => $bcBaser->getImg('admin/blt_list_up.png', array('alt' => '降順', 'title' => '降順')).' 別窓指定'),
					'blank', array('escape' => false, 'class' => 'btn-direction')) ?>
			</th>
			<th><?php echo $paginator->sort(array(
					'asc' => $bcBaser->getImg('admin/blt_list_down.png', array('alt' => '昇順', 'title' => '昇順')).' 登録日',
					'desc' => $bcBaser->getImg('admin/blt_list_up.png', array('alt' => '降順', 'title' => '降順')).' 登録日'),
					'created', array('escape' => false, 'class' => 'btn-direction')) ?>
				<br />
				<?php echo $paginator->sort(array(
					'asc' => $bcBaser->getImg('admin/blt_list_down.png', array('alt' => '昇順', 'title' => '昇順')).' 更新日',
					'desc' => $bcBaser->getImg('admin/blt_list_up.png', array('alt' => '降順', 'title' => '降順')).' 更新日'),
					'modified', array('escape' => false, 'class' => 'btn-direction')) ?>
			</th>
		</tr>
	</thead>
	<tbody>
<?php if(!empty($datas)): ?>
	<?php foreach($datas as $data): ?>
		<?php $bcBaser->element('optional_links/index_row', array('data' => $data)) ?>
	<?php endforeach; ?>
<?php else: ?>
		<tr>
			<td colspan="7"><p class="no-data">データが見つかりませんでした。</p></td>
		</tr>
<?php endif; ?>
	</tbody>
</table>

<!-- list-num -->
<?php $bcBaser->element('list_num') ?>

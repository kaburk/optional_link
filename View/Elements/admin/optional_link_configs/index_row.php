<?php
/**
 * [ADMIN] optional_link
 *
 * @link			http://www.materializing.net/
 * @author			arata
 * @license			MIT
 */
$classies = array();
if (!$optionalLink->allowPublish($data)) {
	$classies = array('unpublish', 'disablerow');
} else {
	$classies = array('publish');
}
$class=' class="'.implode(' ', $classies).'"';
?>
<tr<?php echo $class; ?>>
	<td class="row-tools">
	<?php $bcBaser->link($bcBaser->getImg('admin/icn_tool_unpublish.png', array('width' => 24, 'height' => 24, 'alt' => '無効', 'class' => 'btn')),
			array('action' => 'ajax_unpublish', $data['OptionalLinkConfig']['id']), array('title' => '無効', 'class' => 'btn-unpublish')) ?>
	<?php $bcBaser->link($bcBaser->getImg('admin/icn_tool_publish.png', array('width' => 24, 'height' => 24, 'alt' => '有効', 'class' => 'btn')),
			array('action' => 'ajax_publish', $data['OptionalLinkConfig']['id']), array('title' => '有効', 'class' => 'btn-publish')) ?>

	<?php $bcBaser->link($bcBaser->getImg('admin/icn_tool_edit.png', array('width' => 24, 'height' => 24, 'alt' => '編集', 'class' => 'btn')),
			array('action' => 'edit', $data['OptionalLinkConfig']['id']), array('title' => '編集')) ?>
	<?php $bcBaser->link($bcBaser->getImg('admin/icn_tool_delete.png', array('width' => 24, 'height' => 24, 'alt' => '削除', 'class' => 'btn')),
		array('action' => 'ajax_delete', $data['OptionalLinkConfig']['id']), array('title' => '削除', 'class' => 'btn-delete')) ?>
	</td>
	<td style="width: 45px;"><?php echo $data['OptionalLinkConfig']['id']; ?></td>
	<td>
		<?php echo $bcBaser->link($blogContentDatas[$data['OptionalLinkConfig']['blog_content_id']], array('action' => 'edit', $data['OptionalLinkConfig']['id']), array('title' => '編集')) ?>
	</td>
	<td style="white-space: nowrap">
		<?php echo $bcTime->format('Y-m-d', $data['OptionalLinkConfig']['created']) ?>
		<br />
		<?php echo $bcTime->format('Y-m-d', $data['OptionalLinkConfig']['modified']) ?>
	</td>
</tr>

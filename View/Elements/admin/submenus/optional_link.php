<?php
/**
 * [ADMIN] OptionalLink
 *
 * @link			http://www.materializing.net/
 * @author			arata
 * @license			MIT
 */
?>
<tr>
	<th>オプショナルリンク設定管理メニュー</th>
	<td>
		<ul>
			<li><?php $this->BcBaser->link('オプショナルリンク設定一覧', array('admin' => true, 'plugin' => 'optional_link', 'controller' => 'optional_link_configs', 'action'=>'index')) ?></li>
			<?php if(!$judgeOptionalLinkConfigUse): ?>
			<li><?php $this->BcBaser->link('オプショナルリンク設定データ作成', array('admin' => true, 'plugin' => 'optional_link', 'controller' => 'optional_link_configs', 'action'=>'first')) ?></li>
			<?php endif ?>
		</ul>
	</td>
</tr>

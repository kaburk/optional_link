<?php
/**
 * [ADMIN] optional_link
 *
 * @link			http://www.materializing.net/
 * @author			arata
 * @license			MIT
 */
?>
<tr>
	<th>オプショナルリンク管理メニュー</th>
	<td>
		<ul>
			<li><?php $bcBaser->link('オプショナルリンク一覧', array('admin' => true, 'plugin' => 'optional_link', 'controller' => 'optional_links', 'action'=>'index')) ?></li>
			<li><?php $bcBaser->link('オプショナルリンク一括設定', array('admin' => true, 'plugin' => 'optional_link', 'controller' => 'optional_links', 'action'=>'batch')) ?></li>
		</ul>
	</td>
</tr>
<tr>
	<th>オプショナルリンク設定管理メニュー</th>
	<td>
		<ul><?php if($judgeOptionalLinkConfigUse): ?>
			<li><?php $bcBaser->link('オプショナルリンク設定一覧', array('admin' => true, 'plugin' => 'optional_link', 'controller' => 'optional_link_configs', 'action'=>'index')) ?></li>
			<?php endif ?>
			<li><?php $bcBaser->link('オプショナルリンク設定データ作成', array('admin' => true, 'plugin' => 'optional_link', 'controller' => 'optional_link_configs', 'action'=>'first')) ?></li>
		</ul>
	</td>
</tr>

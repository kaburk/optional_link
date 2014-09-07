<?php
/**
 * [ADMIN] OptionalLink
 *
 * @link			http://www.materializing.net/
 * @author			arata
 * @license			MIT
 */
/**
 * システムナビ
 */
$config['BcApp.adminNavi.optional_link'] = array(
		'name'		=> 'オプショナルリンク プラグイン',
		'contents'	=> array(
			array('name' => 'オプショナルリンク一覧',
				'url' => array(
					'admin' => true,
					'plugin' => 'optional_link',
					'controller' => 'optional_links',
					'action' => 'index')
			),
			array('name' => 'オプショナルリンク設定一覧',
				'url' => array(
					'admin' => true,
					'plugin' => 'optional_link',
					'controller' => 'optional_link_configs',
					'action' => 'index')
			)
	)
);

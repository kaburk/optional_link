<?php
/**
 * [ADMIN] OptionalLink
 *
 * @link			http://www.materializing.net/
 * @author			arata
 * @license			MIT
 */
App::uses('OptionalLinkUtil', 'OptionalLink.Lib');
/**
 * システムナビ
 */
$config['BcApp.adminNavi.optional_link'] = array(
		'name'		=> 'オプショナルリンク プラグイン',
		'contents'	=> array(
			array('name' => '設定一覧',
				'url' => array(
					'admin' => true,
					'plugin' => 'optional_link',
					'controller' => 'optional_link_configs',
					'action' => 'index')
			)
	)
);

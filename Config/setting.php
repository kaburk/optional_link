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
	'name'		 => 'オプショナルリンク プラグイン',
	'contents'	 => array(
		array('name'	 => '設定一覧',
			'url'	 => array(
				'admin'		 => true,
				'plugin'	 => 'optional_link',
				'controller' => 'optional_link_configs',
				'action'	 => 'index')
		)
	)
);

/**
 * 専用ログ
 */
define('LOG_OPTIONAL_LINK', 'log_optional_link');
CakeLog::config('log_optional_link', array(
	'engine' => 'FileLog',
	'types'	 => array('log_optional_link'),
	'file'	 => 'log_optional_link',
));

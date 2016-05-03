<?php

/**
 * [ADMIN] OptionalLink
 *
 * @link			http://www.materializing.net/
 * @author			arata
 * @license			MIT
 */
/**
 * オプショナルリンクで生成したURLにアクセスした際に、公開期間制限に掛かっているかを判定する
 */
Router::connect('/files/optionallink/*', array('plugin' => 'optional_link', 'controller' => 'optional_links', 'action' => 'view_limited_file'));

<?php 
/* SVN FILE: $Id$ */
/* OptionalLinkConfigs schema generated on: 2013-11-24 01:11:43 : 1385223523*/
class OptionalLinkConfigsSchema extends CakeSchema {
	var $name = 'OptionalLinkConfigs';

	var $file = 'optional_link_configs.php';

	var $connection = 'plugin';

	function before($event = array()) {
		return true;
	}

	function after($event = array()) {
	}

	var $optional_link_configs = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 8, 'key' => 'primary'),
		'blog_content_id' => array('type' => 'integer', 'null' => true, 'default' => NULL, 'length' => 1),
		'status' => array('type' => 'boolean', 'null' => true, 'default' => NULL),
		'modified' => array('type' => 'datetime', 'null' => true, 'default' => NULL),
		'created' => array('type' => 'datetime', 'null' => true, 'default' => NULL),
		'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1))
	);
}
?>
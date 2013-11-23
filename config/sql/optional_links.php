<?php 
/* SVN FILE: $Id$ */
/* OptionalLinks schema generated on: 2013-11-21 02:11:25 : 1384968445*/
class OptionalLinksSchema extends CakeSchema {
	var $name = 'OptionalLinks';

	var $file = 'optional_links.php';

	var $connection = 'plugin';

	function before($event = array()) {
		return true;
	}

	function after($event = array()) {
	}

	var $optional_links = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'key' => 'primary'),
		'blog_post_id' => array('type' => 'integer', 'null' => true, 'default' => NULL, 'length' => 8),
		'blog_content_id' => array('type' => 'integer', 'null' => true, 'default' => NULL, 'length' => 8),
		'name' => array('type' => 'string', 'null' => true, 'default' => NULL),
		'blank' => array('type' => 'boolean', 'null' => false, 'default' => '1'),
		'status' => array('type' => 'boolean', 'null' => false, 'default' => '1'),
		'created' => array('type' => 'datetime', 'null' => true, 'default' => NULL),
		'modified' => array('type' => 'datetime', 'null' => true, 'default' => NULL),
		'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1))
	);
}
?>
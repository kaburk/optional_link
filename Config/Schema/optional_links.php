<?php 
class OptionalLinksSchema extends CakeSchema {

	public $file = 'optional_links.php';

	public $connection = 'plugin';

	public function before($event = array()) {
		return true;
	}

	public function after($event = array()) {
	}

	public $optional_links = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false, 'key' => 'primary', 'comment' => 'ID'),
		'blog_post_id' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => false, 'comment' => 'ブログ記事ID'),
		'blog_content_id' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => false, 'comment' => 'ブログコンテンツID'),
		'status' => array('type' => 'integer', 'null' => true, 'default' => '0', 'length' => 2, 'unsigned' => false, 'comment' => '状態'),
		'name' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => 'リンクURL', 'charset' => 'utf8'),
		'blank' => array('type' => 'boolean', 'null' => true, 'default' => '0', 'comment' => '別ウィンドウ'),
		'nolink' => array('type' => 'boolean', 'null' => true, 'default' => '0', 'comment' => 'リンクなし'),
		'file' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => 'ファイル', 'charset' => 'utf8'),
		'publish_begin' => array('type' => 'datetime', 'null' => true, 'default' => null, 'comment' => '公開期間開始日'),
		'publish_end' => array('type' => 'datetime', 'null' => true, 'default' => null, 'comment' => '公開期間終了日'),
		'modified' => array('type' => 'datetime', 'null' => true, 'default' => null, 'comment' => '更新日'),
		'created' => array('type' => 'datetime', 'null' => true, 'default' => null, 'comment' => '作成日'),
		'indexes' => array(
			'PRIMARY' => array('column' => 'id', 'unique' => 1)
		),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB')
	);

}

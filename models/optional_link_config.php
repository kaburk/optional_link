<?php
/**
 * OptionalLinkConfig モデル
 *
 * @link			http://www.materializing.net/
 * @author			arata
 * @license			MIT
 */
class OptionalLinkConfig extends BaserPluginAppModel {
/**
 * ModelName
 * 
 * @var string
 */
	public $name = 'OptionalLinkConfig';
	
/**
 * PluginName
 * 
 * @var string
 */
	public $plugin = 'OptionalLink';
		
/**
 * Validation
 *
 * @var array
 */
	public $validate = array(
		'blog_content_id' => array(
			'notEmpty' => array(
				'rule'		=> array('notEmpty'),
				'message'	=> '必須入力です。'
			)
		)
	);
	
/**
 * 初期値を取得する
 *
 * @return array
 */
	public function getDefaultValue() {
		$data = array(
			'OptionalLinkConfig' => array(
				'status' => true
			)
		);
		return $data;		
	}
	
}

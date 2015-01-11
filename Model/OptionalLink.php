<?php
/**
 * OptionalLink Model
 *
 * @link			http://www.materializing.net/
 * @author			arata
 * @license			MIT
 */
class OptionalLink extends BcPluginAppModel {
/**
 * ModelName
 * 
 * @var string
 */
	public $name = 'OptionalLink';
	
/**
 * PluginName
 * 
 * @var string
 */
	public $plugin = 'OptionalLink';
	
/**
 * ビヘイビア
 * 
 * @var array
 */
	public $actsAs = array(
		'BcUpload' => array(
			'saveDir' => "optionallink",
			'fields' => array(
				'file' => array(
					'type'			=> 'pdf',
					//'namefield'		=> 'id',
					//'nameformat'	=> '%07d',
					'nameadd'		=> false,
				),
			)
		),
	);
	
/**
 * belongsTo
 * 
 * @var array
 */
	public $belongsTo = array(
		'BlogPost' => array(
			'className'	=> 'Blog.BlogPost',
			'foreignKey' => 'blog_post_id'
		)
	);
	
/**
 * Validation
 *
 * @var array
 */
	public $validate = array(
		'name' => array(
			'maxLength' => array(
				'rule'		=> array('maxLength', 255),
				'message'	=> '255文字以内で入力してください。'
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
			'OptionalLink' => array(
				'status' => false
			)
		);
		return $data;
	}
	
}

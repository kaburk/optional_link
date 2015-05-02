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
 * Behavior
 * 
 * @var array
 */
	public $actsAs = array(
		'BcCache',
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
				'status' => 0
			)
		);
		return $data;
	}
	
/**
 * beforeSave
 * 公開期間指定がある場合、ファイルを limited に移動する
 * 公開期間指定がない場合、ファイルを limited から通常領域に移動する
 * 
 * @param array $options
 * @return boolean
 */
	public function beforeSave($options = array()) {
		parent::beforeSave($options);
		
		if (!empty($this->data['OptionalLink']['id'])) {
			$savePath = WWW_ROOT . 'files' . DS . $this->actsAs['BcUpload']['saveDir'] . DS;
			//$pathinfo = pathinfo($this->data['OptionalLink']['file']);
			
			if (!empty($this->data['OptionalLink']['publish_begin']) || !empty($this->data['OptionalLink']['publish_end'])) {
				// 削除チェックを入れた場合、'file' にファイル名が入ってこない
				if (empty($this->data['OptionalLink']['file_delete'])) {
					if (file_exists($savePath . $this->data['OptionalLink']['file'])) {
						rename($savePath . $this->data['OptionalLink']['file'], $savePath . 'limited' . DS . $this->data['OptionalLink']['file']);
					}
				} else {
					$filePath = $savePath .'limited'. DS . $this->data['OptionalLink']['file_'];
					if (file_exists($filePath)) {
						return unlink($filePath);
					}
				}
			} else {
				if (!empty($this->data['OptionalLink']['file'])) {
					if (file_exists($savePath . 'limited' . DS . $this->data['OptionalLink']['file'])) {
						rename($savePath . 'limited' . DS . $this->data['OptionalLink']['file'], $savePath . $this->data['OptionalLink']['file']);
					}
				}
			}
		}
		
		return true;
	}
	
/**
 * beforeDelete
 * 公開期間指定がある場合、削除前に保存場所のパスを limited を考慮して書換える
 * 
 * @param boolean $cascade
 * @return boolean
 */
	public function beforeDelete($cascade = true) {
		$data = $this->read(null, $this->id);
		if (!empty($data['OptionalLink']['publish_begin']) || !empty($data['OptionalLink']['publish_end'])) {
			$this->Behaviors->BcUpload->savePath .= 'limited' . DS;
		} else {
			$this->Behaviors->BcUpload->savePath = preg_replace('/' . preg_quote('limited' . DS, '/') . '$/', '', $this->Behaviors->BcUpload->savePath);
		}
		parent::beforeDelete($cascade);
		
		return true;
	}
	
}

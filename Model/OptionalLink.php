<?php

/**
 * OptionalLink Model
 *
 * @link			http://www.materializing.net/
 * @author			arata
 * @license			MIT
 */
class OptionalLink extends BcPluginAppModel
{

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
			'saveDir'	 => "optionallink",
			'fields'	 => array(
				'file' => array(
					'type'		 => 'all',
					// TODO 保存されるファイル名のフォーマットを指定すると、管理側で、公開期間を指定したファイル名が保存されない
					//'namefield'		=> 'id',
					//'nameformat'	=> '%07d',
					'nameadd'	 => false,
					'imagecopy'	 => array(
						'large'	 => array('prefix' => 'large_', 'width' => '1600', 'height' => '1600'),
						'thumb'	 => array('prefix' => 'thumb_', 'width' => '150', 'height' => '150'),
					),
				),
			),
		),
	);

	/**
	 * belongsTo
	 * 
	 * @var array
	 */
	public $belongsTo = array(
		'BlogPost' => array(
			'className'	 => 'Blog.BlogPost',
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
				'rule'		 => array('maxLength', 255),
				'message'	 => '255文字以内で入力してください。'
			)
		)
	);

	/**
	 * 初期値を取得する
	 *
	 * @return array
	 */
	public function getDefaultValue()
	{
		$data = array(
			'OptionalLink' => array(
				'status' => 0
			)
		);
		return $data;
	}

	/**
	 * ファイル削除の指定がなされているかどうかの値
	 * 
	 * @var boolean
	 */
	public $fileDelete = true;

	/**
	 * 同一ファイル名を持つデータが複数存在するかどうかの値
	 * 
	 * @var boolean
	 */
	public $hasDuplicateFileDate = false;

	/**
	 * beforeSave
	 * 公開期間指定がある場合、ファイルを limited に移動する
	 * 公開期間指定がない場合、ファイルを limited から通常領域に移動する
	 * 
	 * @param array $options
	 * @return boolean
	 */
	public function beforeSave($options = array())
	{
		parent::beforeSave($options);

		if (!$this->Behaviors->enabled('BcUpload')) {
			return true;
		}

		if (!empty($this->data[$this->alias]['id'])) {
			$savePath		 = WWW_ROOT . 'files' . DS . $this->actsAs['BcUpload']['saveDir'] . DS; // ファイル保存パス
			$savePathLimited = WWW_ROOT . 'files' . DS . $this->actsAs['BcUpload']['saveDir'] . DS . 'limited' . DS; // 公開制限ファイル保存パス
			$uploadField	 = key($this->actsAs['BcUpload']['fields']); // ファイル保存フィールド名
			$fileName		 = ''; // 保存ファイル名
			if (isset($this->data[$this->alias][$uploadField])) {
				//$pathinfo = pathinfo($this->data[$this->alias]['file']);
				$fileName = $this->data[$this->alias][$uploadField];
			}

			if (!empty($this->data[$this->alias]['publish_begin']) || !empty($this->data[$this->alias]['publish_end'])) {
				// 削除チェックを入れた場合、'file' にファイル名が入ってこない
				if (empty($this->data[$this->alias]['file_delete'])) {
					if ($fileName) {
						if (file_exists($savePath . $fileName)) {
							// オリジナルのファイルを処理
							rename($savePath . $fileName, $savePathLimited . $fileName);
							foreach ($this->actsAs['BcUpload']['fields'][$uploadField]['imagecopy'] as $keyCopyName => $valueCopy) {
								if (file_exists($savePath . $valueCopy['prefix'] . $fileName)) {
									rename($savePath . $valueCopy['prefix'] . $fileName, $savePathLimited . $valueCopy['prefix'] . $fileName);
								}
							}
						}
					}
				} else {
					if (file_exists($savePathLimited . $this->data[$this->alias]['file_'])) {
						// オリジナルのファイルを処理
						unlink($savePathLimited . $this->data[$this->alias]['file_']);
						foreach ($this->actsAs['BcUpload']['fields'][$uploadField]['imagecopy'] as $keyCopyName => $valueCopy) {
							if (file_exists($savePathLimited . $valueCopy['prefix'] . $this->data[$this->alias]['file_'])) {
								unlink($savePathLimited . $valueCopy['prefix'] . $this->data[$this->alias]['file_']);
							}
						}
					}
				}
			} else {
				// 公開期間指定がなされていない場合は、ファイルを通常領域に移動する
				if ($fileName && is_string($fileName)) {
					if (file_exists($savePathLimited . $fileName)) {
						// オリジナルのファイルを処理
						rename($savePathLimited . $fileName, $savePath . $fileName);
						foreach ($this->actsAs['BcUpload']['fields'][$uploadField]['imagecopy'] as $keyCopyName => $valueCopy) {
							if (file_exists($savePathLimited . $valueCopy['prefix'] . $fileName)) {
								rename($savePathLimited . $valueCopy['prefix'] . $fileName, $savePath . $valueCopy['prefix'] . $fileName);
							}
						}
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
	public function beforeDelete($cascade = true)
	{
		if (Hash::get($this->data, $this->alias . '.publish_begin') || Hash::get($this->data, $this->alias . '.publish_end')) {
			$this->Behaviors->BcUpload->savePath .= 'limited' . DS;
		} else {
			$this->Behaviors->BcUpload->savePath = preg_replace('/' . preg_quote('limited' . DS, '/') . '$/', '', $this->Behaviors->BcUpload->savePath);
		}
		parent::beforeDelete($cascade);

		return true;
	}

	/**
	 * 同一ファイル名のファイルが複数記事に存在するかどうかを判別する
	 * 
	 * @param array $data
	 * @return boolean | array
	 */
	public function hasDuplicateFile($data)
	{
		$duplicate = array();
		if (Hash::get($data, $this->alias . '.file')) {
			$fileName = '';
			// 削除チェックを入れた場合、file は配列で入り、file_ にファイル名が入ってくる
			if ($this->isFileDelete($data)) {
				$fileName = $data[$this->alias]['file_'];
			} else {
				$fileName = $data[$this->alias]['file'];
			}

			$duplicate = $this->find('all', array(
				'conditions' => array(
					$this->alias . '.file'	 => $fileName,
					'NOT'					 => array($this->alias . '.id' => array($data[$this->alias]['id'])),
				),
				'recursive'	 => -1,
				'callbacks'	 => false,
			));
		}
		return $duplicate;
	}

	/**
	 * ファイルが削除指定されているかどうかを判別する
	 * 
	 * @param array $data
	 * @return boolean
	 */
	public function isFileDelete($data)
	{
		if (Hash::get($data, $this->alias . '.file_delete')) {
			return true;
		}
		return false;
	}

}

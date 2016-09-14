<?php

/**
 * [ModelEventListener] OptionalLink
 *
 * @link			http://www.materializing.net/
 * @author			arata
 * @license			MIT
 */
class OptionalLinkModelEventListener extends BcModelEventListener
{

	/**
	 * 登録イベント
	 *
	 * @var array
	 */
	public $events = array(
		'Blog.BlogPost.beforeFind',
		'Blog.BlogPost.beforeValidate',
		'Blog.BlogPost.beforeSave',
		'Blog.BlogPost.beforeDelete',
		'Blog.BlogPost.afterSave',
		'Blog.BlogPost.afterDelete',
		'Blog.BlogContent.beforeFind',
		'Blog.BlogContent.afterDelete',
	);

	/**
	 * オプショナルリンクモデル
	 * 
	 * @var Object
	 */
	private $OptionalLink = null;

	/**
	 * オプショナルリンク設定モデル
	 * 
	 * @var Object
	 */
	private $OptionalLinkConfig = null;

	/**
	 * ブログ記事多重保存の判定
	 * 
	 * @var boolean
	 */
	private $throwBlogPost = false;

	/**
	 * Construct
	 * 
	 */
	public function __construct()
	{
		parent::__construct();
		if (ClassRegistry::isKeySet('OptionalLink.OptionalLink')) {
			$this->OptionalLink = ClassRegistry::getObject('OptionalLink.OptionalLink');
		} else {
			$this->OptionalLink = ClassRegistry::init('OptionalLink.OptionalLink');
		}
		if (ClassRegistry::isKeySet('OptionalLink.OptionalLinkConfig')) {
			$this->OptionalLinkConfig = ClassRegistry::getObject('OptionalLink.OptionalLinkConfig');
		} else {
			$this->OptionalLinkConfig = ClassRegistry::init('OptionalLink.OptionalLinkConfig');
		}
	}

	/**
	 * blogBlogPostBeforeFind
	 * 
	 * @param CakeEvent $event
	 */
	public function blogBlogPostBeforeFind(CakeEvent $event)
	{
		$Model		 = $event->subject();
		// ブログ記事取得の際にオプショナルリンク情報も併せて取得する
		$association = array(
			'OptionalLink' => array(
				'className'	 => 'OptionalLink.OptionalLink',
				'foreignKey' => 'blog_post_id'
			)
		);
		$Model->bindModel(array('hasOne' => $association));
	}

	/**
	 * blogBlogContentBeforeFind
	 * 
	 * @param CakeEvent $event
	 * @return array
	 */
	public function blogBlogContentBeforeFind(CakeEvent $event)
	{
		$Model		 = $event->subject();
		// ブログ設定取得の際にオプショナルリンク設定情報も併せて取得する
		$association = array(
			'OptionalLinkConfig' => array(
				'className'	 => 'OptionalLink.OptionalLinkConfig',
				'foreignKey' => 'blog_content_id'
			)
		);
		$Model->bindModel(array('hasOne' => $association));
	}

	/**
	 * blogBlogPostBeforeValidate
	 * 
	 * @param CakeEvent $event
	 * @return boolean
	 */
	public function blogBlogPostBeforeValidate(CakeEvent $event)
	{
		$Model = $event->subject();
		// ブログ記事保存の手前で OptionalLink モデルのデータに対して validation を行う
		// TODO saveAll() ではbeforeValidateが効かない？
		$this->OptionalLink->set($Model->data);
		return $this->OptionalLink->validates();
	}

	/**
	 * blogBlogPostAfterSave
	 * - ブログ記事を削除する場合、関連付くオプショナルリンクのデータを削除するが、
	 *   同一ファイルに対して複数の記事設定がなされているかどうかをチェックし、
	 *   対象ファイルの実体を削除して良いかどうかをチェックしている
	 * 
	 * @param CakeEvent $event
	 */
	public function blogBlogPostBeforeSave(CakeEvent $event)
	{
		$Model = $event->subject();

		if (Hash::get($Model->data, 'OptionalLink.id')) {
			if ($this->OptionalLink->isFileDelete($Model->data)) {
				$this->OptionalLink->fileDelete = true;
			} else {
				$this->OptionalLink->fileDelete = false;
			}
			if ($this->OptionalLink->hasDuplicateFile($Model->data)) {
				$this->OptionalLink->hasDuplicateFileDate = true;
			} else {
				$this->OptionalLink->hasDuplicateFileDate = false;
			}
		}

		return true;
	}

	/**
	 * blogBlogPostAfterSave
	 * 
	 * @param CakeEvent $event
	 */
	public function blogBlogPostAfterSave(CakeEvent $event)
	{
		$Model = $event->subject();

		// OptionalLinkのデータがない場合は save 処理を実施しない
		if (!isset($Model->data['OptionalLink']) || empty($Model->data['OptionalLink'])) {
			return;
		}

		$saveData = $this->generateSaveData($Model, $Model->id);
		// 2周目では保存処理に渡らないようにしている
		if (!$this->throwBlogPost) {
			if ($this->OptionalLink->fileDelete && $this->OptionalLink->hasDuplicateFileDate) {
				$saveData['OptionalLink']['file'] = '';
				$this->OptionalLink->Behaviors->disable('BcUpload');
			}
			if (!$this->OptionalLink->save($saveData)) {
				$this->log(sprintf('ID：%s のオプショナルリンクの保存に失敗しました。', $Model->data['OptionalLink']['id']));
			}
		}

		// ブログ記事コピー保存時、アイキャッチが入っていると処理が2重に行われるため、1周目で処理通過を判定し、
		// 2周目では保存処理に渡らないようにしている
		$this->throwBlogPost = true;
	}

	/**
	 * blogBlogPostBeforeDelete
	 * - ブログ記事削除前に、そのブログ記事が持っているオプショナルリンクデータがファイルを持っていて、
	 *   ファイルが複数記事に設定されている場合は実ファイルを削除させない
	 * - 同一ファイル名のファイルが複数記事に存在する場合、BcUpload の削除処理（ファイルの削除）を実行させない
	 * 
	 * @param CakeEvent $event
	 */
	public function blogBlogPostBeforeDelete(CakeEvent $event)
	{
		$Model = $event->subject();
		if (Hash::get($Model->data, 'OptionalLink.id')) {
			if (ClassRegistry::isKeySet('OptionalLink')) {
				$OptionalLinkModel = ClassRegistry::getObject('OptionalLink');
			} else {
				$OptionalLinkModel = ClassRegistry::init('OptionalLink');
			}
			if ($OptionalLinkModel->hasDuplicateFile($Model->data)) {
				// ビヘイビアにモデルのコールバックを処理させない
				// unload は OptionalLink モデル側の beforeDelete 処理に影響するため使えない
				// $Model->OptionalLink->Behaviors->unload('BcUpload');
				$OptionalLinkModel->Behaviors->disable('BcUpload');
			}
		}
	}

	/**
	 * blogBlogPostAfterDelete
	 * 
	 * @param CakeEvent $event
	 */
	public function blogBlogPostAfterDelete(CakeEvent $event)
	{
		$Model	 = $event->subject();
		// ブログ記事削除時、そのブログ記事が持つOptionalLinkを削除する
		$data	 = $this->OptionalLink->find('first', array(
			'conditions' => array('OptionalLink.blog_post_id' => $Model->id),
			'recursive'	 => -1
		));
		if ($data) {
			if (!$this->OptionalLink->delete($data['OptionalLink']['id'])) {
				$this->log('ID:' . $data['OptionalLink']['id'] . 'のOptionalLinkの削除に失敗しました。');
			}
		}
	}

	/**
	 * blogBlogContentAfterDelete
	 * 
	 * @param CakeEvent $event
	 */
	public function blogBlogContentAfterDelete(CakeEvent $event)
	{
		$Model	 = $event->subject();
		// ブログ削除時、そのブログが持つOptionalLink設定を削除する
		$data	 = $this->OptionalLinkConfig->find('first', array(
			'conditions' => array('OptionalLinkConfig.blog_content_id' => $Model->id),
			'recursive'	 => -1
		));
		if ($data) {
			if (!$this->OptionalLinkConfig->delete($data['OptionalLinkConfig']['id'])) {
				$this->log('ID:' . $data['OptionalLinkConfig']['id'] . 'のOptionalLink設定の削除に失敗しました。');
			}
		}
	}

	/**
	 * 保存するデータの生成
	 * 
	 * @param Object $Model
	 * @param int $contentId
	 * @return array
	 */
	private function generateSaveData($Model, $contentId = '')
	{
		$params		 = Router::getParams();
		$data		 = array();
		$modelId	 = $oldModelId	 = null;
		if ($Model->alias == 'BlogPost') {
			$modelId = $contentId;
			if (!empty($params['pass'][1])) {
				$oldModelId = $params['pass'][1];
			}
		}

		if ($contentId) {
			$data = $this->OptionalLink->find('first', array(
				'conditions' => array('OptionalLink.blog_post_id' => $contentId),
				'recursive'	 => -1
			));
		}

		switch ($params['action']) {
			case 'admin_add':
				// 追加時
				$data['OptionalLink']					 = $Model->data['OptionalLink'];
				$data['OptionalLink']['blog_post_id']	 = $contentId;
				break;

			case 'admin_edit':
				// 編集時
				$data['OptionalLink']					 = $Model->data['OptionalLink'];
				$data['OptionalLink']['blog_post_id']	 = $contentId;
				break;

			case 'admin_ajax_copy':
				// Ajaxコピー処理時に実行
				// データを手動で調整した場合等、既存データ内に同一の blog_post_id がある場合はそのデータを返す
				if ($data) {
					return $data;
				}
				// ブログコピー保存時にエラーがなければ保存処理を実行
				if (empty($Model->validationErrors)) {
					$_data = array();
					if ($oldModelId) {
						$_data = $this->OptionalLink->find('first', array(
							'conditions' => array('OptionalLink.blog_post_id' => $oldModelId),
							'recursive'	 => -1
						));
					}
					// もしオプショナルリンク設定の初期データ作成を行ってない事を考慮して判定している
					if ($_data) {
						// コピー元データがある時
						$_data['OptionalLink']['id']			 = null;
						$data['OptionalLink']					 = $_data['OptionalLink'];
						$data['OptionalLink']['blog_post_id']	 = $contentId;
					} else {
						// コピー元データがない時
						$data['OptionalLink']['blog_post_id']	 = $modelId;
						$data['OptionalLink']['blog_content_id'] = $params['pass'][0];
					}
				}
				break;

			default:
				break;
		}

		return $data;
	}

}

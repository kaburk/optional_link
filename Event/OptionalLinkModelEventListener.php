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
		'Blog.BlogPost.beforeValidate',
		'Blog.BlogPost.afterSave',
		'Blog.BlogPost.afterDelete',
		'Blog.BlogPost.beforeFind',
		'Blog.BlogContent.afterDelete',
		'Blog.BlogContent.beforeFind'
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
			if (!$this->OptionalLink->save($saveData)) {
				$this->log(sprintf('ID：%s のオプショナルリンクの保存に失敗しました。', $Model->data['OptionalLink']['id']));
			}
		}

		// ブログ記事コピー保存時、アイキャッチが入っていると処理が2重に行われるため、1周目で処理通過を判定し、
		// 2周目では保存処理に渡らないようにしている
		$this->throwBlogPost = true;
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
						$data['OptionalLink']					 = $_data['OptionalLink'];
						$data['OptionalLink']['blog_post_id']	 = $contentId;
						unset($data['OptionalLink']['id']);
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

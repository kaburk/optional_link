<?php
/**
 * [ModelEventListener] OptionalLink
 *
 * @link			http://www.materializing.net/
 * @author			arata
 * @license			MIT
 */
class OptionalLinkModelEventListener extends BcModelEventListener {
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
		'Blog.BlogContent.beforeValidate',
		'Blog.BlogContent.afterSave',
		'Blog.BlogContent.afterDelete',
		'Blog.BlogContent.beforeFind'
	);
	
/**
 * オプショナルリンクモデル
 * 
 * @var Object
 */
	public $OptionalLink = null;
	
/**
 * オプショナルリンク設定モデル
 * 
 * @var Object
 */
	public $OptionalLinkConfig = null;
	
/**
 * Construct
 * 
 */
	function __construct() {
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
	public function blogBlogPostBeforeFind(CakeEvent $event) {
		$model = $event->subject();
		// ブログ記事取得の際にオプショナルリンク情報も併せて取得する
		$association = array(
			'OptionalLink' => array(
				'className' => 'OptionalLink.OptionalLink',
				'foreignKey' => 'blog_post_id'
			)
		);
		$model->bindModel(array('hasOne' => $association));
	}
	
/**
 * blogBlogContentBeforeFind
 * 
 * @param CakeEvent $event
 * @return array
 */
	public function blogBlogContentBeforeFind(CakeEvent $event) {
		$model = $event->subject();
		// ブログ設定取得の際にオプショナルリンク設定情報も併せて取得する
		$association = array(
			'OptionalLinkConfig' => array(
				'className' => 'OptionalLink.OptionalLinkConfig',
				'foreignKey' => 'blog_content_id'
			)
		);
		$model->bindModel(array('hasOne' => $association));
	}
	
/**
 * blogBlogPostBeforeValidate
 * 
 * @param CakeEvent $event
 * @return boolean
 */
	public function blogBlogPostBeforeValidate(CakeEvent $event) {
		$model = $event->subject();
		// ブログ記事保存の手前で OptionalLink モデルのデータに対して validation を行う
		// TODO saveAll() ではbeforeValidateが効かない？
		$this->OptionalLink->set($model->data);
		return $this->OptionalLink->validates();
	}
	
/**
 * blogBlogContentBeforeValidate
 * 
 * @param CakeEvent $event
 * @return boolean
 */
	public function blogBlogContentBeforeValidate(CakeEvent $event) {
		$model = $event->subject();
		// ブログ設定保存の手前で OptionalLinkConfig モデルのデータに対して validation を行う
		$this->OptionalLinkConfig->set($model->data);
		return $this->OptionalLinkConfig->validates();
	}
	
/**
 * blogBlogPostAfterSave
 * 
 * @param CakeEvent $event
 */
	public function blogBlogPostAfterSave(CakeEvent $event) {
		$model = $event->subject();
		$created = $event->data[0];
		if ($created) {
			$contentId = $model->getLastInsertId();
		} else {
			$contentId = $model->data[$model->alias]['id'];
		}
		$saveData = $this->_generateSaveData($model, $contentId);
		if (isset($saveData['OptionalLink']['id'])) {
			// ブログ記事編集保存時に設定情報を保存する
			$this->OptionalLink->set($saveData);
		} else {
			// ブログ記事追加時に設定情報を保存する
			$this->OptionalLink->create($saveData);
		}
		if (!$this->OptionalLink->save()) {
			$this->log(sprintf('ID：%s のオプショナルリンクの保存に失敗しました。', $model->data['OptionalLink']['id']));
		}
	}
	
/**
 * blogBlogContentAfterSave
 * 
 * @param CakeEvent $event
 */
	public function blogBlogContentAfterSave(CakeEvent $event) {
		$model = $event->subject();
		$created = $event->data[0];
		if ($created) {
			$contentId = $model->getLastInsertId();
		} else {
			$contentId = $model->data[$model->alias]['id'];
		}
		$saveData = $this->_generateSaveData($model, $contentId);
		if (isset($saveData['OptionalLinkConfig']['id'])) {
			// ブログ設定編集保存時に設定情報を保存する
			$this->OptionalLinkConfig->set($saveData);
		} else {
			// ブログ追加時に設定情報を保存する
			$this->OptionalLinkConfig->create($saveData);
		}
		if (!$this->OptionalLinkConfig->save()) {
			$this->log(sprintf('ID：%s のオプショナルリンク設定の保存に失敗しました。', $model->data['OptionalLinkConfig']['id']));
		}
		
	}
	
/**
 * 保存するデータの生成
 * 
 * @param Object $model
 * @param int $contentId
 * @return array
 */
	private function _generateSaveData($model, $contentId = '') {
		
		if ($model->alias == 'BlogPost') {
			$params = Router::getParams();
			$data = array();
			
			if ($contentId) {
				$data = $this->OptionalLink->find('first', array('conditions' => array(
					'OptionalLink.blog_post_id' => $contentId
				)));
			}
			if ($params['action'] != 'admin_ajax_copy') {
				if(!empty($model->data['OptionalLink'])) {
					$data['OptionalLink'] = $model->data['OptionalLink'];
					$data['OptionalLink']['blog_post_id'] = $contentId;
				} else {
					// ブログ記事追加の場合
					$data['OptionalLink']['blog_post_id'] = $contentId;
					$data['OptionalLink']['blog_content_id'] = $model->BlogContent->id;
				}
			} else {
				// Ajaxコピー処理時に実行
				// ブログコピー保存時にエラーがなければ保存処理を実行
				if (empty($model->validationErrors)) {
					$_data = $this->OptionalLink->find('first', array(
						'conditions' => array(
							'OptionalLink.blog_post_id' => $params['pass'][1]
						),
						'recursive' => -1
					));
					// もしオプショナルリンク設定の初期データ作成を行ってない事を考慮して判定している
					if ($_data) {
						$data['OptionalLink'] = $_data['OptionalLink'];
						$data['OptionalLink']['blog_post_id'] = $contentId;
					} else {
						$data['OptionalLink']['blog_post_id'] = $contentId;
						$data['OptionalLink']['blog_content_id'] = $params['pass'][0];
					}
				}
			}
		}
		
		if ($model->alias == 'BlogContent') {
			$params = Router::getParams();
			$data = array();
			
			if ($contentId) {
				$data = $this->OptionalLinkConfig->find('first', array('conditions' => array(
					'OptionalLinkConfig.blog_content_id' => $contentId
				)));
			}
			if ($params['action'] != 'admin_ajax_copy') {
				$data['OptionalLinkConfig']['blog_content_id'] = $contentId;
				$data['OptionalLinkConfig']['status'] = $this->OptionalLinkConfig->data['OptionalLinkConfig']['status'];
			} else {
				// Ajaxコピー処理時に実行
				// ブログコピー保存時にエラーがなければ保存処理を実行
				if (empty($model->validationErrors)) {
					$_data = $this->OptionalLinkConfig->find('first', array(
						'conditions' => array(
							'OptionalLinkConfig.blog_content_id' => $params['pass']['0']
						),
						'recursive' => -1
					));
					// もしオプショナルリンク設定の初期データ作成を行ってない事を考慮して判定している
					if ($_data) {
						$data['OptionalLinkConfig'] = $_data['OptionalLinkConfig'];
						$data['OptionalLinkConfig']['blog_content_id'] = $contentId;
					} else {
						$data['OptionalLinkConfig']['blog_content_id'] = $contentId;
						$data['OptionalLinkConfig']['status'] = true;
					}
				}
			}
		}
		
		return $data;
	}
	
/**
 * blogBlogPostAfterDelete
 * 
 * @param CakeEvent $event
 */
	public function blogBlogPostAfterDelete(CakeEvent $event) {
		$model = $event->subject();
		// ブログ記事削除時、そのブログ記事が持つOptionalLinkを削除する
		$data = $this->OptionalLink->find('first', array(
			'conditions' => array('OptionalLink.blog_post_id' => $model->id),
			'recursive' => -1
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
	public function blogBlogContentAfterDelete(CakeEvent $event) {
		$model = $event->subject();
		// ブログ削除時、そのブログが持つOptionalLink設定を削除する
		$data = $this->OptionalLinkConfig->find('first', array(
			'conditions' => array('OptionalLinkConfig.blog_content_id' => $model->id),
			'recursive' => -1
		));
		if ($data) {
			if (!$this->OptionalLinkConfig->delete($data['OptionalLinkConfig']['id'])) {
				$this->log('ID:' . $data['OptionalLinkConfig']['id'] . 'のOptionalLink設定の削除に失敗しました。');
			}
		}
	}
	
}

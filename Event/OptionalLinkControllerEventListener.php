<?php
/**
 * [ControllerEventListener] OptionalLink
 *
 * @link			http://www.materializing.net/
 * @author			arata
 * @license			MIT
 */
class OptionalLinkControllerEventListener extends BcControllerEventListener {
/**
 * 登録イベント
 *
 * @var array
 */
	public $events = array(
		'initialize',
		'startup',
		'Blog.Blog.startup',
		'Blog.Blog.beforeRender',
		'Blog.BlogPosts.beforeRender',
	);
	
/**
 * OptionalLink設定情報
 * 
 * @var array
 */
	private $optionalLinkConfigs = array();
	
/**
 * OptionalLinkモデル
 * 
 * @var Object
 */
	private $OptionalLinkModel = null;
	
/**
 * OptionalLink設定モデル
 * 
 * @var Object
 */
	private $OptionalLinkConfigModel = null;
	
/**
 * 処理対象とするコントローラー
 * 
 * @var array
 */
	private $targetController = array('blog_posts', 'blog_contents');
	
/**
 * 処理対象とするアクション
 * 
 * @var array
 */
	private $targetAction = array('admin_edit', 'admin_add');
	
/**
 * initialize
 * 
 * @param CakeEvent $event
 */
	public function initialize(CakeEvent $event) {
		$Controller = $event->subject();
		$Controller->helpers[] = 'OptionalLink.OptionalLink';
	}
	
/**
 * OptionalLinkConfig モデルを準備する
 * 
 */
	private function setUpModel() {
		if (ClassRegistry::isKeySet('OptionalLink.OptionalLinkConfig')) {
			$this->OptionalLinkConfigModel = ClassRegistry::getObject('OptionalLink.OptionalLinkConfig');
		} else {
			$this->OptionalLinkConfigModel = ClassRegistry::init('OptionalLink.OptionalLinkConfig');
		}
	}
	
/**
 * startup
 * 
 * @param CakeEvent $event
 */
	public function startup(CakeEvent $event) {
		if (!BcUtil::isAdminSystem()) {
			return;
		}
		
		$Controller = $event->subject();
		if (!in_array($Controller->request->params['controller'], $this->targetController)) {
			return;
		}
		
		$this->setUpModel();
		// ブログ記事編集画面でタグの追加を行うと Undefined が発生するため判定
		if (!empty($Controller->BlogContent->id)) {
			$this->optionalLinkConfigs = $this->OptionalLinkConfigModel->find('first', array(
				'conditions' => array(
					'OptionalLinkConfig.blog_content_id' => $Controller->BlogContent->id
				),
				'recursive' => -1
			));
			$this->OptionalLinkModel = ClassRegistry::init('OptionalLink.OptionalLink');
		}
	}
	
/**
 * blogBlogStartup
 * 
 * @param CakeEvent $event
 */
	public function blogBlogStartup(CakeEvent $event) {
		if(BcUtil::isAdminSystem()) {
			return;
		}
		
		$Controller = $event->subject();
		$this->setUpModel();
		$this->optionalLinkConfigs = $this->OptionalLinkConfigModel->find('first', array(
			'conditions' => array(
				'OptionalLinkConfig.blog_content_id' => $Controller->BlogContent->id
			),
			'recursive' => -1
		));
	}
	
/**
 * blogBlogBeforeRender
 * 
 * @param CakeEvent $event
 */
	public function blogBlogBeforeRender(CakeEvent $event) {
		$Controller = $event->subject();
		if (!empty($Controller->blogContent['BlogContent'])) {			
			$Controller->set('OptionalLinkConfig', $this->optionalLinkConfigs);
		}
		// プレビューの際は編集欄の内容を送る
		if ($Controller->preview) {
			if (!empty($Controller->data['OptionalLink'])) {
				$Controller->viewVars['post']['OptionalLink'] = $Controller->data['OptionalLink'];
			}
		}
	}
	
/**
 * blogBlogPostsBeforeRender
 * 
 * @param CakeEvent $event
 */
	public function blogBlogPostsBeforeRender(CakeEvent $event) {
		if (!BcUtil::isAdminSystem()) {
			return;
		}
		
		// オプショナルリンク設定データがない場合は何もせず通常動作にする
		if (!$this->optionalLinkConfigs) {
			return;
		}
		
		$Controller = $event->subject();
		// ブログ記事編集・追加画面で実行
		if ($Controller->request->params['action'] == 'admin_edit') {
			if (isset($Controller->request->data['OptionalLink'])) {
				if (empty($Controller->request->data['OptionalLink']['id'])) {
					$defalut = $this->OptionalLinkModel->getDefaultValue();
					$Controller->request->data['OptionalLink'] = $defalut['OptionalLink'];
				}
			}
		}
		if ($Controller->request->params['action'] == 'admin_add') {
			$defalut = $this->OptionalLinkModel->getDefaultValue();
			$Controller->request->data['OptionalLink'] = $defalut['OptionalLink'];
		}
		
		$Controller->request->data['OptionalLinkConfig'] = $this->optionalLinkConfigs['OptionalLinkConfig'];
	}
	
}

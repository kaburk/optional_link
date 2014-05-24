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
		'Blog.BlogContents.beforeRender'
	);
	
/**
 * OptionalLink設定情報
 * 
 * @var array
 */
	public $optionalLinkConfigs = array();
	
/**
 * OptionalLinkモデル
 * 
 * @var Object
 */
	public $OptionalLinkModel = null;
	
/**
 * OptionalLink設定モデル
 * 
 * @var Object
 */
	public $OptionalLinkConfigModel = null;
	
/**
 * initialize
 * 
 * @param CakeEvent $event
 */
	function initialize(CakeEvent $event) {
		$controller = $event->subject();
		$controller->helpers[] = 'OptionalLink.OptionalLink';
	}
	
/**
 * startup
 * 
 * @param CakeEvent $event
 */
	public function startup(CakeEvent $event) {
		$controller = $event->subject();
		if ($controller->request->params['controller'] == 'blog_posts' || $controller->request->params['controller'] == 'blog_contents') {
			// ブログページ表示の際に実行
			if (ClassRegistry::isKeySet('OptionalLink.OptionalLinkConfig')) {
				$this->OptionalLinkConfigModel = ClassRegistry::getObject('OptionalLink.OptionalLinkConfig');
			} else {
				$this->OptionalLinkConfigModel = ClassRegistry::init('OptionalLink.OptionalLinkConfig');
			}
			// ブログ記事編集画面でタグの追加を行うと Undefined が発生するため判定
			if (!empty($controller->BlogContent->id)) {
				$this->optionalLinkConfigs = $this->OptionalLinkConfigModel->find('first', array(
					'conditions' => array(
						'OptionalLinkConfig.blog_content_id' => $controller->BlogContent->id
					),
					'recursive' => -1
				));
				$this->OptionalLinkModel = ClassRegistry::init('OptionalLink.OptionalLink');
			}
		}
	}
	
/**
 * blogBlogStartup
 * 
 * @param CakeEvent $event
 */
	public function blogBlogStartup(CakeEvent $event) {
		$controller = $event->subject();
		if(!BcUtil::isAdminSystem()) {
			if (ClassRegistry::isKeySet('OptionalLink.OptionalLinkConfig')) {
				$this->OptionalLinkConfigModel = ClassRegistry::getObject('OptionalLink.OptionalLinkConfig');
			} else {
				$this->OptionalLinkConfigModel = ClassRegistry::init('OptionalLink.OptionalLinkConfig');
			}
			$this->optionalLinkConfigs = $this->OptionalLinkConfigModel->find('first', array(
				'conditions' => array(
					'OptionalLinkConfig.blog_content_id' => $controller->BlogContent->id
				),
				'recursive' => -1
			));
		}
	}
	
/**
 * blogBlogBeforeRender
 * 
 * @param CakeEvent $event
 */
	public function blogBlogBeforeRender(CakeEvent $event) {
		$controller = $event->subject();
		if (!empty($controller->blogContent['BlogContent'])) {			
			$controller->set('OptionalLinkConfig', $this->optionalLinkConfigs);
		}
		// プレビューの際は編集欄の内容を送る
		if ($controller->preview) {
			if (!empty($controller->data['OptionalLink'])) {
				$controller->viewVars['post']['OptionalLink'] = $controller->data['OptionalLink'];
			}
		}
	}
	
/**
 * blogBlogPostsBeforeRender
 * 
 * @param CakeEvent $event
 */
	public function blogBlogPostsBeforeRender(CakeEvent $event) {
		$controller = $event->subject();
		// ブログ記事編集・追加画面で実行
		if ($controller->request->params['action'] == 'admin_edit') {
			$controller->request->data['OptionalLinkConfig'] = $this->optionalLinkConfigs['OptionalLinkConfig'];
		}
		if ($controller->request->params['action'] == 'admin_add') {
			$defalut = $this->OptionalLinkModel->getDefaultValue();
			$controller->request->data['OptionalLink'] = $defalut['OptionalLink'];
			$controller->request->data['OptionalLinkConfig'] = $this->optionalLinkConfigs['OptionalLinkConfig'];
		}
	}
	
/**
 * blogBlogContentsBeforeRender
 * 
 * @param CakeEvent $event
 */
	public function blogBlogContentsBeforeRender(CakeEvent $event) {
		$controller = $event->subject();
		// ブログ設定編集
		if ($controller->request->params['action'] == 'admin_edit') {
			$this->optionalLinkConfigs = $this->OptionalLinkConfigModel->find('first', array(
				'conditions' => array(
					'OptionalLinkConfig.blog_content_id' => $controller->BlogContent->id
				),
				'recursive' => -1
			));
			$controller->request->data['OptionalLinkConfig'] = $this->optionalLinkConfigs['OptionalLinkConfig'];
		}
		// ブログ追加画面に設定情報を送る
		if ($controller->action == 'admin_add') {
			$defalut = $this->OptionalLinkConfigModel->getDefaultValue();
			$controller->request->data['OptionalLinkConfig'] = $defalut['OptionalLinkConfig'];
		}
	}
	
}

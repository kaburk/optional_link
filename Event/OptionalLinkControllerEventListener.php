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
		$Controller = $event->subject();
		$Controller->helpers[] = 'OptionalLink.OptionalLink';
	}
	
/**
 * startup
 * 
 * @param CakeEvent $event
 */
	public function startup(CakeEvent $event) {
		$Controller = $event->subject();
		if ($Controller->request->params['controller'] == 'blog_posts' || $Controller->request->params['controller'] == 'blog_contents') {
			// ブログページ表示の際に実行
			if (ClassRegistry::isKeySet('OptionalLink.OptionalLinkConfig')) {
				$this->OptionalLinkConfigModel = ClassRegistry::getObject('OptionalLink.OptionalLinkConfig');
			} else {
				$this->OptionalLinkConfigModel = ClassRegistry::init('OptionalLink.OptionalLinkConfig');
			}
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
	}
	
/**
 * blogBlogStartup
 * 
 * @param CakeEvent $event
 */
	public function blogBlogStartup(CakeEvent $event) {
		$Controller = $event->subject();
		if(!BcUtil::isAdminSystem()) {
			if (ClassRegistry::isKeySet('OptionalLink.OptionalLinkConfig')) {
				$this->OptionalLinkConfigModel = ClassRegistry::getObject('OptionalLink.OptionalLinkConfig');
			} else {
				$this->OptionalLinkConfigModel = ClassRegistry::init('OptionalLink.OptionalLinkConfig');
			}
			$this->optionalLinkConfigs = $this->OptionalLinkConfigModel->find('first', array(
				'conditions' => array(
					'OptionalLinkConfig.blog_content_id' => $Controller->BlogContent->id
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
		$Controller = $event->subject();
		// ブログ記事編集・追加画面で実行
		if ($Controller->request->params['action'] == 'admin_edit') {
			$Controller->request->data['OptionalLinkConfig'] = $this->optionalLinkConfigs['OptionalLinkConfig'];
		}
		if ($Controller->request->params['action'] == 'admin_add') {
			$defalut = $this->OptionalLinkModel->getDefaultValue();
			$Controller->request->data['OptionalLink'] = $defalut['OptionalLink'];
			$Controller->request->data['OptionalLinkConfig'] = $this->optionalLinkConfigs['OptionalLinkConfig'];
		}
	}
	
}

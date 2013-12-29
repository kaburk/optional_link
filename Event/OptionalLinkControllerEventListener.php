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
		'Blog.Blog.startup',
		'Blog.Blog.beforeRender',
		'Blog.BlogPosts.beforeRender',
		'Blog.BlogContents.beforeRender'
	);
	
/**
 * optional_link設定情報
 * 
 * @var array
 */
	public $optionalLinkConfigs = array();
	
/**
 * optional_linkモデル
 * 
 * @var Object
 */
	public $OptionalLinkModel = null;
	
/**
 * optional_link設定モデル
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
 * blogBlogStartup
 * 
 * @param CakeEvent $event
 */
	public function blogBlogStartup(CakeEvent $event) {
		$controller = $event->subject();
		// ブログページ表示の際に実行
		if (ClassRegistry::isKeySet('OptionalLink.OptionalLinkConfig')) {
			$this->OptionalLinkConfigModel = ClassRegistry::getObject('OptionalLink.OptionalLinkConfig');
		} else {
			$this->OptionalLinkConfigModel = ClassRegistry::init('OptionalLink.OptionalLinkConfig');
		}
		// ブログ記事編集画面でタグの追加を行うと Undefined が発生するため判定
		if (!empty($controller->BlogContent->id)) {
			$this->optionalLinkConfigs = $this->OptionalLinkConfigModel->read(null, $controller->BlogContent->id);
			$this->OptionalLinkModel = ClassRegistry::init('OptionalLink.OptionalLink');
		}
	}
	
/**
 * blogBlogBeforeRender
 * 
 * @param CakeEvent $event
 */
	public function blogBlogBeforeRender(CakeEvent $event) {
		$controller = $event->subject();
		// プレビューの際は編集欄の内容を送る
		if ($controller->name == 'Blog') {			
			if ($controller->preview) {
				if (!empty($controller->data['OptionalLink'])) {
					$controller->viewVars['post']['OptionalLink'] = $controller->data['OptionalLink'];
				}
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
		if ($controller->action == 'admin_edit') {
			$controller->data['OptionalLinkConfig'] = $this->optionalLinkConfigs['OptionalLinkConfig'];
		}
		if ($controller->action == 'admin_add') {
			$defalut = $this->OptionalLinkModel->getDefaultValue();
			$controller->data['OptionalLink'] = $defalut['OptionalLink'];
			$controller->data['OptionalLinkConfig'] = $this->optionalLinkConfigs['OptionalLinkConfig'];
		}
	}
	
/**
 * blogBlogContentsBeforeRender
 * 
 * @param CakeEvent $event
 */
	public function blogBlogContentsBeforeRender(CakeEvent $event) {
		$controller = $event->subject();
		// ブログ追加画面に設定情報を送る
		if ($controller->action == 'admin_add') {
			$defalut = $this->OptionalLinkConfigModel->getDefaultValue();
			$controller->data['OptionalLinkConfig'] = $defalut['OptionalLinkConfig'];
		}
	}
	
}

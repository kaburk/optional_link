<?php
/**
 * [Component] optional_link
 *
 * @link			http://www.materializing.net/
 * @author			arata
 * @license			MIT
 */
class OptionalLinkHookComponent extends Object {
/**
 * 登録フック
 *
 * @var array
 */
	public $registerHooks = array('initialize', 'startup', 'beforeRender');
	
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
 * @param Controller $controller 
 */
	function initialize($controller) {
		$controller->helpers[] = 'OptionalLink.OptionalLink';
	}
	
/**
 * startup
 * 
 * @param Controller $controller 
 * @return void
 */
	public function startup($controller) {
		// ブログページ表示の際に実行
		if (!empty($controller->params['plugin'])) {
			if ($controller->params['plugin'] == 'blog') {
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
		}
	}
	
/**
 * beforeRender
 * 
 * @param Controller $controller 
 * @return void
 */
	public function beforeRender($controller) {
		// プレビューの際は編集欄の内容を送る
		if ($controller->name == 'Blog') {			
			if ($controller->preview) {
				if (!empty($controller->data['OptionalLink'])) {
					$controller->viewVars['post']['OptionalLink'] = $controller->data['OptionalLink'];
				}
			}
		}
		
		if ($controller->name == 'BlogPosts') {
			// ブログ記事編集・追加画面で実行
			// - startup で処理したかったが $controller->data に入れるとそれを全て上書きしてしまうのでダメだった
			if ($controller->action == 'admin_edit') {
				$controller->data['OptionalLinkConfig'] = $this->optionalLinkConfigs['OptionalLinkConfig'];
			}
			if ($controller->action == 'admin_add') {
				$defalut = $this->OptionalLinkModel->getDefaultValue();
				$controller->data['OptionalLink'] = $defalut['OptionalLink'];
				$controller->data['OptionalLinkConfig'] = $this->optionalLinkConfigs['OptionalLinkConfig'];
			}			
		}
		
		if ($controller->name == 'BlogContents') {
			// ブログ追加画面に設定情報を送る
			if ($controller->action == 'admin_add') {
				$defalut = $this->OptionalLinkConfigModel->getDefaultValue();
				$controller->data['OptionalLinkConfig'] = $defalut['OptionalLinkConfig'];
			}			
		}
	}
	
}

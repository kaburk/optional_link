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
	public $registerHooks = array(
		'startup', 'beforeRender', 'beforeRedirect', 'afterBlogPostAdd', 'afterBlogPostEdit');
	
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
 * startup
 * 
 * @param Controller $controller 
 * @return void
 */
	public function startup($controller) {
		
		// ブログページ表示の際に実行
		if(!empty($controller->params['plugin'])) {
			if($controller->params['plugin'] == 'blog') {
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
		if($controller->name == 'Blog') {			
			if($controller->preview) {
				if(!empty($controller->data['OptionalLink'])) {
					$controller->viewVars['post']['OptionalLink'] = $controller->data['OptionalLink'];
				}
			}
		}
		
		if($controller->name == 'BlogPosts') {
			// ブログ記事編集・追加画面で実行
			// - startup で処理したかったが $controller->data に入れるとそれを全て上書きしてしまうのでダメだった
			if($controller->action == 'admin_edit') {
				$controller->data['OptionalLinkConfig'] = $this->optionalLinkConfigs['OptionalLinkConfig'];
			}
			if($controller->action == 'admin_add') {
				$defalut = $this->OptionalLinkModel->getDefaultValue();
				$controller->data['OptionalLink'] = $defalut['OptionalLink'];
				$controller->data['OptionalLinkConfig'] = $this->optionalLinkConfigs['OptionalLinkConfig'];
			}
			
			// Ajaxコピー処理時に実行
			//   ・Ajax削除時は、内部的に Model->delete が呼ばれているため afterDelete で処理可能
			if($controller->action == 'admin_ajax_copy') {
				// ブログ記事コピー保存時にエラーがなければ保存処理を実行
				if(empty($controller->BlogPost->validationErrors)) {
					$optionalLinkData = array();
					$optionalLinkData['OptionalLink']['blog_post_id'] = $controller->viewVars['data']['BlogPost']['id'];
					$optionalLinkData['OptionalLink']['blog_content_id'] = $controller->viewVars['data']['BlogPost']['blog_content_id'];
					//$optionalLinkData['OptionalLink']['name'] = $controller->viewVars['data']['BlogPost']['name'];
					
					$this->OptionalLinkModel->create($optionalLinkData);
					$this->OptionalLinkModel->save($optionalLinkData, false);
					// キャッシュの削除を行わないと、登録したオプショナルリンクがブログ記事編集画面に反映されない
					clearAllCache();
				}
			}
			
		}
		
		if($controller->name == 'BlogContents') {			
			// ブログ設定編集画面に設定情報を送る
			if($controller->action == 'admin_edit') {
				$this->optionalLinkConfigs = $this->OptionalLinkConfigModel->findByBlogContentId($controller->BlogContent->id);
				$controller->data['OptionalLinkConfig'] = $this->optionalLinkConfigs['OptionalLinkConfig'];
			}
			// ブログ追加画面に設定情報を送る
			if($controller->action == 'admin_add') {
				$defalut = $this->OptionalLinkConfigModel->getDefaultValue();
				$controller->data['OptionalLinkConfig'] = $defalut['OptionalLinkConfig'];
			}
			
			// Ajaxコピー処理時に実行
			//   ・Ajax削除時は、内部的に Model->delete が呼ばれているため afterDelete で処理可能
			if($controller->action == 'admin_ajax_copy') {
				// ブログコピー保存時にエラーがなければ保存処理を実行
				if(empty($controller->BlogContent->validationErrors)) {
					$configData = $this->OptionalLinkConfigModel->findByBlogContentId($controller->params['pass']['0']);
					// もしオプショナルリンク設定の初期データ作成を行ってない事を考慮して判定している
					$saveData = array();
					if($configData) {
						$saveData['OptionalLinkConfig']['blog_content_id'] = $controller->viewVars['data']['BlogContent']['id'];
						$saveData['OptionalLinkConfig']['status'] = $configData['OptionalLinkConfig']['status'];
					} else {
						$saveData['OptionalLinkConfig']['blog_content_id'] = $controller->viewVars['data']['BlogContent']['id'];
						$saveData['OptionalLinkConfig']['status'] = true;
					}
					
					$this->OptionalLinkConfigModel->create($saveData);
					$this->OptionalLinkConfigModel->save($saveData, false);
					// キャッシュの削除を行わないと、登録したオプショナルリンク設定が編集画面に反映されない
					clearAllCache();
				}
			}
			
		}
		
	}
	
/**
 * beforeRedirect
 * 
 * @param Object $controller
 * @param array $url
 * @param boolean $status
 * @param type $exit
 * @return void
 */
	public function beforeRedirect($controller, $url, $status, $exit) {
		if($controller->name == 'BlogContents') {
			if($controller->action == 'admin_edit') {
				// ブログ設定編集保存時に設定情報を保存する
				$this->OptionalLinkConfigModel->set($controller->data['OptionalLinkConfig']);
			} elseif($controller->action == 'admin_add') {
				// ブログ追加時に設定情報を保存する
				$controller->data['OptionalLinkConfig']['blog_content_id'] = $controller->BlogContent->id;
				$this->OptionalLinkConfigModel->create($controller->data['OptionalLinkConfig']);
			}
			if(empty($controller->BlogContent->validationErrors)) {
				if(!$this->OptionalLinkConfigModel->save(null, false)) {
					$this->log(sprintf('ID：%s のオプショナルリンク設定の保存に失敗しました。', $controller->data['OptionalLinkConfig']['id']));
				}
			}
		}
	}
	
/**
 * afterBlogPostAdd
 *
 * @param Controller $controller
 * @return void
 */
	public function afterBlogPostAdd($controller) {
		// ブログ記事保存時にエラーがなければ保存処理を実行
		if(empty($controller->BlogPost->validationErrors)) {
			$this->_dataSaving($controller);
		}
	}
	
/**
 * afterBlogPostEdit
 *
 * @param Controller $controller
 * @return void
 */
	public function afterBlogPostEdit($controller) {
		// ブログ記事保存時にエラーがなければ保存処理を実行
		if(empty($controller->BlogPost->validationErrors)) {
			$this->_dataSaving($controller);
		}
	}
	
/**
 * オプショナルリンク情報を保存する
 * 
 * @param Controller $controller 
 * @return void
 */
	protected function _dataSaving($controller) {
		
		$controller->data['OptionalLink']['blog_content_id'] = $controller->data['BlogPost']['blog_content_id'];
		
		if($controller->action == 'admin_add') {
			$controller->data['OptionalLink']['blog_post_id'] = $controller->BlogPost->getLastInsertId();
		} else {
			$controller->data['OptionalLink']['blog_post_id'] = $controller->BlogPost->id;
		}
		
		if(empty($controller->data['OptionalLink']['id'])) {
			$this->OptionalLinkModel->create($controller->data['OptionalLink']);
		} else {
			$this->OptionalLinkModel->set($controller->data['OptionalLink']);
		}
		
		if(!$this->OptionalLinkModel->save($controller->data['OptionalLink'], false)) {
			$this->log('ブログ記事ID：' . $controller->data['OptionalLink']['blog_post_id'] . 'のオプショナルリンク情報保存に失敗しました。');
		}
		
	}
	
}

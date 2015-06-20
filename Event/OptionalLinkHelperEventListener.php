<?php
/**
 * [HelperEventListener] OptionalLink
 *
 * @link			http://www.materializing.net/
 * @author			arata
 * @license			MIT
 */
class OptionalLinkHelperEventListener extends BcHelperEventListener {
/**
 * 登録イベント
 *
 * @var array
 */
	public $events = array(
		'Form.afterCreate',
		'Form.afterEnd',
		'Html.beforeGetLink',
		'Html.afterGetLink'
	);
	
/**
 * オプショナルリンク設定
 * 
 * @var array
 */
	private $optionalLinkConfigs = array();
	
/**
 * OptionalLinkデータ
 * 
 * @var array
 */
	private $optionalLink = array();
	
/**
 * 判定するURL
 * 
 * @var array
 */
	private $url = array();
	
/**
 * ブログ記事詳細へのURLかどうかを判定
 * 
 * @var boolean
 */
	private $judgeBlogArchivesUrl = false;
	
/**
 * URL書換を機能させるかどうかを判定
 * - OptionalLink データがあり、かつ設定が有効の際に書換る
 * 
 * @var boolean
 */
	private $judgeRewrite = false;
	
/**
 * ブログデータ
 * 
 * @var array
 */
	public $blogContents = array();
	
/**
 * 処理対象とするコントローラー
 * 
 * @var array
 */
	private $targetController = array('BlogPosts', 'BlogContents');
	
/**
 * 処理対象とするアクション
 * 
 * @var array
 */
	private $targetAction = array('admin_edit', 'admin_add');
	
/**
 * formAfterCreate
 * - ブログ記事追加・編集画面に編集欄を追加する
 * 
 * @param CakeEvent $event
 */
	public function formAfterCreate(CakeEvent $event) {
		if (!BcUtil::isAdminSystem()) {
			return $event->data['out'];
		}
		
		$View = $event->subject();
		
		if ($View->request->params['controller'] != 'blog_posts') {
			return $event->data['out'];
		}
		
		if (!in_array($View->request->params['action'], $this->targetAction)) {
			return $event->data['out'];
		}
		
		// ブログ記事追加・編集画面に編集欄を追加する
		if ($event->data['id'] != 'BlogPostForm') {
			return $event->data['out'];
		}
		
		if (isset($View->request->data['OptionalLinkConfig'])) {
			if (!empty($View->request->data['OptionalLinkConfig']['status'])) {
				$event->data['out'] = $event->data['out'] . $View->element('OptionalLink.admin/optional_link_form', array('model'=>'BlogPost'));
			}
		}
		
		return $event->data['out'];
	}
	
/**
 * blogFormAfterEnd
 * - ブログ設定編集画面にオプショナルリンク設定編集リンクを表示する
 * 
 * @param CakeEvent $event
 * @return string
 */
	public function formAfterEnd(CakeEvent $event) {
		if (!BcUtil::isAdminSystem()) {
			return $event->data['out'];
		}
		
		$View = $event->subject();
		
		if ($View->request->params['controller'] != 'blog_contents') {
			return $event->data['out'];
		}
		
		if ($View->request->params['action'] != 'admin_edit') {
			return $event->data['out'];
		}
		
		// ブログ設定編集画面にオプショナルリンク設定編集リンクを表示する
		if ($event->data['id'] == 'BlogContentAdminEditForm') {
			if ($this->optionalLinkConfigs) {
				$this->modelInitializer($View);
				$output = '<div id="OptionalLinkConfigBox">';
				$output .= $View->BcBaser->getLink('≫オプショナルリンク設定', array(
					'plugin' => 'optional_link',
					'controller' => 'optional_link_configs',
					'action' => 'edit', $this->optionalLinkConfigs['OptionalLinkConfig']['id']
				));
				$output .= '</div>';
				$event->data['out'] = $event->data['out'] . $output;
			}
		}
		
		return $event->data['out'];
	}
	
/**
 * モデル登録用メソッド
 * 
 * @param View $View
 */
	private function modelInitializer($View) {
		if (ClassRegistry::isKeySet('OptionalLink.OptionalLinkConfig')) {
			$this->OptionalLinkConfigModel = ClassRegistry::getObject('OptionalLink.OptionalLinkConfig');
		} else {
			$this->OptionalLinkConfigModel = ClassRegistry::init('OptionalLink.OptionalLinkConfig');
		}
		//$this->optionalLinkConfigs = $this->OptionalLinkConfigModel->read(null, $View->Blog->blogContent['id']);
		$this->optionalLinkConfigs = $this->OptionalLinkConfigModel->find('first', array(
			'conditions' => array(
				'OptionalLinkConfig.blog_content_id' => $View->Blog->blogContent['id'],
			),
			'recursive' => -1,
		));
	}
	
/**
 * htmlBeforeGetLink
 * 
 * @param CakeEvent $event
 * @return string
 */
	public function htmlBeforeGetLink(CakeEvent $event) {
		$this->_judgeRewriteUrl($event);
		return $event->data['options'];
	}
	
/**
 * リンクのURLを書き換えるかどうかを判定する
 * 
 * @param CakeEvent $event
 * @return
 */
	private function _judgeRewriteUrl(CakeEvent $event) {
		// 管理システム側でのアクセスではURL変換を行わない
		if (BcUtil::isAdminSystem()) {
			return;
		}
		
		$View = $event->subject();
		$this->judgeBlogArchivesUrl = false;
		$this->judgeRewrite = false;
		
		if (!is_array($event->data['url'])) {
			$this->url = Router::parse($event->data['url']);
		} else {
			$this->url = $event->data['url'];
		}

		if (!$this->url) {
			return;
		}
		
		if (isset($this->url['admin'])) {
			// 管理システムへのURLの場合は書き換えを行わない
			if ($this->url['admin']) {
				return;
			}
		}
		
		// URLがブログ記事詳細へのリンクかどうかを判定する
		if (!$this->judgeBlogArchivesUrl) {
			if (!isset($this->url['action'])) {
				return;
			}
			
			if ($this->url['action'] == 'archives') {
				// 引数のURLが1つ（記事詳細）のときに有効とする
				if (!empty($this->url[0]) && !isset($this->url[1])) {
					if (!$this->blogContents) {
						if (ClassRegistry::isKeySet('Blog.BlogContent')) {
							$BlogContentModel = ClassRegistry::getObject('Blog.BlogContent');
						} else {
							$BlogContentModel = ClassRegistry::init('Blog.BlogContent');
						}
						$this->blogContents = $BlogContentModel->find('all', array('recursive' => -1));
					}
					foreach ($this->blogContents as $value) {
						if ($this->url['controller'] == $value['BlogContent']['name']) {
							$this->judgeBlogArchivesUrl = true;
							break;
						}
					}
				}
			}
		}
		
		// URLがブログ記事詳細へのリンクの場合、リンクの書換えを実施する
		if ($this->judgeBlogArchivesUrl) {
			$this->modelInitializer($View);
			if (!$this->optionalLinkConfigs['OptionalLinkConfig']['status']) {
				// 設定値を初期化
				$this->optionalLink = null;
				// オプショナルリンク設定が無効の場合はURL書き換えを行わない
				return;
			}
			// 現在の画面ではなく、ブログ記事のURLに対しての情報が必要なため取得する
			if (ClassRegistry::isKeySet('Blog.BlogPost')) {
				$BlogPostModel = ClassRegistry::getObject('Blog.BlogPost');
			} else {
				$BlogPostModel = ClassRegistry::init('Blog.BlogPost');
			}
			$post = $BlogPostModel->find('first', array(
				'conditions' => array(
					'BlogPost.blog_content_id' => $value['BlogContent']['id'],
					'BlogPost.no' => $this->url[0]
				),
				// recursiveを設定しないと「最近の投稿」で OptionalLink が取得できない
				'recursive' => 1
			));
			if ($post && !empty($post['OptionalLink'])) {
				$this->optionalLink['OptionalLink'] = $post['OptionalLink'];
				if ($this->optionalLink['OptionalLink']['status']) {
					$this->judgeRewrite = true;
					if ($this->optionalLink['OptionalLink']['blank']) {
						$event->data['options']['target'] = '_blank';
					}
				}
				
				switch ($this->optionalLink['OptionalLink']['status']) {
					case '2':
						// ファイルの場合はnameにファイルへのURLを入れる - modify by gondoh
						$optionalLink = $this->optionalLink['OptionalLink'];
						if ($optionalLink['file']) {
							// サムネイル側へのリンクになるため、imgsize => large を指定する
							$fileLink = $View->BcUpload->uploadImage('OptionalLink.file', $optionalLink['file'], array('imgsize' => 'large'));
							$result = preg_match('/.+<?\shref=[\'|"](.*?)[\'|"].*/', $fileLink, $match);
							if ($result) {
								$optionalLink['name'] = $match[1];
								$event->data['options']['target'] = '_blank'; // 問答無用でblank
							}
						}
						$this->optionalLink['OptionalLink'] = $optionalLink;
						break;
						
					default:
						break;
				}
			}
		}
		
	}
	
/**
 * htmlAfterGetLink
 * 
 * @param CakeEvent $event
 * @return string
 */
	public function htmlAfterGetLink(CakeEvent $event) {
		$View = $event->subject();
		if ($this->judgeBlogArchivesUrl) {
			$event->data['out'] = $this->_rewriteUrl($View, $event->data['out']);
		}
		return $event->data['out'];
	}
	
/**
 * 出力されるHTMLのリンクを書き換える
 * 
 * @param Object $View
 * @param string $out
 * @return string
 */
	private function _rewriteUrl($View, $out) {
		if ($this->optionalLink) {
			if ($this->optionalLink['OptionalLink']['status']) {
				
				switch ($this->optionalLink['OptionalLink']['status']) {
					case '1':	// URLの場合
						if (!$this->optionalLink['OptionalLink']['nolink']) {
							$link = $this->optionalLink['OptionalLink']['name'];
							if ($link) {
								// /files〜 の場合はドメインを付与して絶対指定扱いにする
								$regexFiles = '/^\/files\/.+/';
								if (preg_match($regexFiles, $link)) {
									// /lib/Baser/basics.php
									$link = topLevelUrl(false) . $link;
									//$link = Configure::read('BcEnv.siteUrl') . $link;
								}
								// <a href="/URL">TEXT</a>
								//$regex = '/(<a href=[\'|"])(.*?)([\'|"].*</a>)/';
								$regex = '/href=\"(.+?)\"/';
								$replacement = 'href="'. $link .'"';
								$out = preg_replace($regex, $replacement, $out);
							}
						} else {
							// リンクしない場合は文字列に置換する
							// 例：<a href="/news/archives/2>(.)</a>
							// \<a\ (.+)\>(.+)\<\/a\>
							$regex = '/^\<a\ .+\>(.+)\<\/a\>/';
							preg_match($regex, $out, $matches);
							if ($matches[1]) {
								$out = $matches[1];
							}
						}
						break;

					case '2':	// ファイルの場合
							$link = $this->optionalLink['OptionalLink']['name'];
							if ($link) {
								// ファイルの公開期間をチェックする
								$checkPublish = $View->OptionalLink->allowPublishFile($this->optionalLink);
								if ($checkPublish) {
									// /files〜 の場合はドメインを付与して絶対指定扱いにする
									$regexFiles = '/^\/files\/.+/';
									if (preg_match($regexFiles, $link)) {
										// /lib/Baser/basics.php
										$link = topLevelUrl(false) . $link;
										//$link = Configure::read('BcEnv.siteUrl') . $link;
									}
									// <a href="/URL">TEXT</a>
									//$regex = '/(<a href=[\'|"])(.*?)([\'|"].*</a>)/';
									$regex = '/href=\"(.+?)\"/';
									$replacement = 'href="'. $link .'"';
									$out = preg_replace($regex, $replacement, $out);
								} else {
									// ファイルの公開期間が終了していれば、リンクしない文字列に置換する
									$regex = '/^\<a\ .+\>(.+)\<\/a\>/';
									preg_match($regex, $out, $matches);
									if ($matches[1]) {
										$out = $matches[1];
									}
								}
							}
						break;
					
					default:
						break;
				}
				
				
			}
		}
		return $out;
	}
	
}

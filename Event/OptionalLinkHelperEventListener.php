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
	public $optionalLinkConfigs = array();
	
/**
 * 判定するURL
 * 
 * @var array
 */
	public $url = array();
	
/**
 * ブログ記事詳細へのURLかどうかを判定
 * 
 * @var boolean
 */
	public $judgeBlogArchivesUrl = false;
	
/**
 * URL書換を機能させるかどうかを判定
 * - OptionalLink データがあり、かつ設定が有効の際に書換る
 * 
 * @var boolean
 */
	public $judgeRewrite = false;
	
/**
 * OptionalLinkデータ
 * 
 * @var array
 */
	public $optionalLink = array();
	
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
	public $judgeControllers = array('BlogPosts', 'BlogContents');
	
/**
 * formAfterCreate
 * - ブログ記事追加・編集画面に編集欄を追加する
 * 
 * @param CakeEvent $event
 */
	public function formAfterCreate(CakeEvent $event) {
		$View = $event->subject();
		if (BcUtil::isAdminSystem()) {
			if ($View->request->params['controller'] == 'blog_posts') {
				if ($View->request->params['action'] == 'admin_edit' || $View->request->params['action'] == 'admin_add') {
					// ブログ記事追加・編集画面に編集欄を追加する
					if ($event->data['id'] == 'BlogPostForm') {
						$event->data['out'] = $event->data['out'] . $View->element('OptionalLink.admin/optional_link_form', array('model'=>'BlogPost'));
						return $event->data['out'];
					}
				}
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
		$View = $event->subject();
		if (BcUtil::isAdminSystem()) {
			if ($View->request->params['controller'] == 'blog_contents') {
				if ($View->request->params['action'] == 'admin_edit') {
					// ブログ設定編集画面にオプショナルリンク設定編集リンクを表示する
					if ($event->data['id'] == 'BlogContentAdminEditForm') {
						$this->modelInitializer($View);
						$output = '<div id="OptionalLinkConfigBox">';
						$output .= $View->BcBaser->getLink('≫オプショナルリンク設定', array(
							'plugin' => 'optional_link',
							'controller' => 'optional_link_configs',
							'action' => 'edit', $this->optionalLinkConfigs['OptionalLinkConfig']['id']
						));
						$output .= '</div>';
						$event->data['out'] = $event->data['out'] . $output;
						return $event->data['out'];
					}
				}
			}
		}
		return $event->data['out'];
	}
	
/**
 * モデル登録用メソッド
 * 
 * @param View $View
 */
	public function modelInitializer($View) {
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
		$View = $event->subject();
		$this->judgeBlogArchivesUrl = false;
		$this->judgeRewrite = false;
		
		// 管理システム側でのアクセスではURL変換を行わない
		if(!BcUtil::isAdminSystem()) {
			
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
				if (isset($this->url['action'])) {
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
			}
			
			if ($this->judgeBlogArchivesUrl) {
				if (ClassRegistry::isKeySet('OptionalLink.OptionalLinkConfig')) {
					$OptionalLinkConfigModel = ClassRegistry::getObject('OptionalLink.OptionalLinkConfig');
				} else {
					$OptionalLinkConfigModel = ClassRegistry::init('OptionalLink.OptionalLinkConfig');
				}
				$optionalLinkConfig = $OptionalLinkConfigModel->find('first', array('conditions' => array(
					'OptionalLinkConfig.blog_content_id' => $value['BlogContent']['id']
				)));
				if (!$optionalLinkConfig['OptionalLinkConfig']['status']) {
					// オプショナルリンク設定が無効の場合はURL書き換えを行わない
					return;
				}
				
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
					if ($post['OptionalLink']['status']) {
						$this->judgeRewrite = true;
						if ($post['OptionalLink']['blank']) {
							$event->data['options']['target'] = '_blank';
						}
					}
					// PDFの場合はnameにPDFへのURLを入れる - modify by gondoh
					if ($post['OptionalLink']['status'] == '2') {
						$optionalLink = $this->optionalLink['OptionalLink'];
						if ($optionalLink['file']) {
							$fileLink = $View->BcUpload->uploadImage('OptionalLink.file', $optionalLink['file']);
							$result = preg_match('/.+<?\shref=[\'|"](.*?)[\'|"].*/', $fileLink, $match);
							if ($result) {
								$optionalLink['name'] = $match[1];
								$event->data['options']['target'] = '_blank'; // 問答無用でblank
							}
						}
						$this->optionalLink['OptionalLink'] = $optionalLink;
					}
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

					case '2':	// PDFの場合
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
						break;
					
					default:
						break;
				}
				
				
			}
		}
		return $out;
	}
	
}

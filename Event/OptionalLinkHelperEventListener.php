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
		'Blog.Form.afterCreate',
		'Html.beforeGetLink',
		'Blog.Html.beforeGetLink',
		'Html.afterGetLink',
		'Blog.Html.afterGetLink'
	);
	
	public $url = array();
	
	public $judgeBlogArchivesUrl = false;
		
/**
 * 管理システム側かの判定値
 * 
 * @var boolean
 */
	public $judgeRewrite = false;
	
/**
 * ブログデータ
 * 
 * @var array
 */
	public $blogContents = array();
	
/**
 * Construct 
 * 
 */
	public function __construct() {
		parent::__construct();
		$BlogContentModel = ClassRegistry::init('Blog.BlogContent');
		$this->blogContents = $BlogContentModel->find('all', array('recursive' => -1));
	}
	
/**
 * blogFormAfterCreate
 * 
 * @param CakeEvent $event
 * @return string
 */
	public function blogFormAfterCreate(CakeEvent $event) {
		$form = $event->subject();
		
		if ($form->request->params['controller'] == 'blog_posts') {
			if (!empty($form->request->data['OptionalLinkConfig']['status'])) {
				// ブログ記事追加画面に編集欄を追加する
				if ($form->request->params['action'] == 'admin_add' || $form->request->params['action'] == 'admin_edit') {
					if ($event->data['id'] == 'BlogPostForm') {
						$event->data['out'] = $event->data['out'] . $form->element('OptionalLink.optional_link_form');
					}
				}
			}
		}
		
		if ($form->request->params['controller'] == 'blog_contents'){
			// ブログ設定編集画面に設定欄を表示する
			if ($form->request->params['action'] == 'admin_edit' || $form->request->params['action'] == 'admin_edit') {
				if ($event->data['id'] == 'BlogContentEditForm') {
					$event->data['out'] = $event->data['out'] . $form->element('OptionalLink.optional_link_config_form');
				}
			}
		}
		
		return $event->data['out'];
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
 * blogHtmlBeforeGetLink
 * 
 * @param CakeEvent $event
 * @return string
 */
	public function blogHtmlBeforeGetLink(CakeEvent $event) {
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
		$html = $event->subject();
		$this->judgeBlogArchivesUrl = false;
		$this->judgeRewrite = false;
		
		// 管理システム側でのアクセスではURL変換を行わない
		if (!isset($html->request->params['prefix']) || $html->request->params['prefix'] != 'admin') {
			
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
			
			if ($this->url['plugin'] == 'blog' && $this->url['action'] == 'archives') {
				// 引数のURLが1つ（記事詳細）のときに有効とする
				if (count($this->url[0]) === 1) {
					$this->judgeBlogArchivesUrl = true;
				}
			}
			
			if (!$this->judgeBlogArchivesUrl) {
				if ($this->url['action'] == 'archives') {
					// 引数のURLが1つ（記事詳細）のときに有効とする
					if (count($this->url[0]) === 1) {
						foreach ($this->blogContents as $key => $value) {
							if ($this->url['controller'] == $value['BlogContent']['name']) {
								$this->judgeBlogArchivesUrl = true;
								break;
							}
						}
					}
				}
			}
			
			if ($this->judgeBlogArchivesUrl) {
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
					if ($post['OptionalLink']['status']) {
						$this->judgeRewrite = true;
						if ($post['OptionalLink']['blank']) {
							$event->data['options']['target'] = '_blank';
						}
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
		$html = $event->subject();
		if ($this->judgeBlogArchivesUrl) {
			$event->data['out'] = $this->_rewriteUrl($html, $event->data['out'], $event->data['url']);
		}
		return $event->data['out'];
	}
	
/**
 * blogHtmlAfterGetLink
 * 
 * @param CakeEvent $event
 * @return string
 */
	public function blogHtmlAfterGetLink(CakeEvent $event) {
		$html = $event->subject();
		if ($this->judgeBlogArchivesUrl) {
			$event->data['out'] = $this->_rewriteUrl($html, $event->data['out'], $event->data['url']);
		}
		return $event->data['out'];
	}
	
/**
 * 
 * @param type $html
 * @param type $out
 * @param type $link
 * @return string
 */
	private function _rewriteUrl($html, $out, $link) {
		$urls = $this->url;
		// SP、FPでは判定構造を１つ繰り上げる
		if (!empty($html->request->params['prefix'])) {
//			$urls[1] = $urls[2];
//			$urls[3] = $urls[4];
		}
		
		foreach ($this->blogContents as $value) {
			if ($urls['controller'] == $value['BlogContent']['name']) {
				
				if (ClassRegistry::isKeySet('Blog.BlogPost')) {
					$BlogPostModel = ClassRegistry::getObject('Blog.BlogPost');
				} else {
					$BlogPostModel = ClassRegistry::init('Blog.BlogPost');
				}
				$post = $BlogPostModel->find('first', array(
					'conditions' => array(
						'BlogPost.blog_content_id' => $value['BlogContent']['id'],
						'BlogPost.no' => $urls[0]
					),
					// recursiveを設定しないと「最近の投稿」で OptionalLink が取得できない
					'recursive' => 1
				));
				if ($post && !empty($post['OptionalLink'])) {
					$link = '';
					if ($post['OptionalLink']['status']) {
						$link = $post['OptionalLink']['name'];
					}
					if ($link) {
						// <a href="/URL">TEXT</a>
						//$regex = '/(<a href=[\'|"])(.*?)([\'|"].*</a>)/';
						$regex = '/href=\"(.+?)\"/';
						$replacement = 'href="'. $link .'"';
						$out = preg_replace($regex, $replacement, $out);
					}
				}
				
			}
		}
		
		return $out;
	}
	
}

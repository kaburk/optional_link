<?php
/**
 * [HookHelper] optional_link
 *
 * @link			http://www.materializing.net/
 * @author			arata
 * @license			MIT
 */
class OptionalLinkHookHelper extends AppHelper {
/**
 * 登録フック
 *
 * @var array
 */
	public $registerHooks = array('afterFormCreate', 'afterBaserGetLink');
	
/**
 * ビュー
 * 
 * @var View 
 */
	public $View = null;
	
/**
 * 管理システム側かの判定値
 * 
 * @var boolean
 */
	public $judgeAdmin = false;
	
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
		$this->View = ClassRegistry::getObject('view');
		if (empty($this->View->params['prefix']) || $this->View->params['prefix'] != 'admin') {
			$BlogContentModel = ClassRegistry::init('Blog.BlogContent');
			$this->blogContents = $BlogContentModel->find('all', array('recursive' => -1));
		}
	}
	
/**
 * afterFormCreate
 * 
 * @param Object $form
 * @param string $id
 * @param string $out
 * @return string
 */
	public function afterFormCreate($form, $id, $out) {
		
		if ($form->params['controller'] == 'blog_posts') {
			if (!empty($form->data['OptionalLinkConfig']['status'])) {
				// ブログ記事追加画面に編集欄を追加する
				if ($this->action == 'admin_add' || $this->action == 'admin_edit') {
					if ($id == 'BlogPostForm') {
						$out = $out . $this->View->element('admin/optional_link_form', array('plugin' => 'optional_link'));
					}
				}
			}
		}
		
		if ($form->params['controller'] == 'blog_contents'){
			// ブログ設定編集画面に設定欄を表示する
			if ($this->action == 'admin_edit' || $this->action == 'admin_edit') {
				if ($id == 'BlogContentEditForm') {
					$out = $out . $this->View->element('admin/optional_link_config_form', array('plugin' => 'optional_link'));
				}
			}
		}
		
		return $out;
		
	}
	
/**
 * afterBaserGetLink
 * 
 * @param Object $html
 * @param string $link
 * @param string $out
 * @return string
 */
	public function afterBaserGetLink($html, $link, $out) {
		
		// 管理システム側でのアクセスではURL変換を行わない
		if (!empty($this->params['prefix']) && $this->params['prefix'] == 'admin') {
			$this->judgeAdmin = true;
		}
		
		if (!$this->judgeAdmin) {
			$params = Router::parse($link);
			$judge = false;

			// blogPost での判定を作成する
			if (!$judge) {
				if (empty($params['admin']) || $params['prefix'] != 'admin') {
					if (isset($params['controller']) && $params['action'] == 'archives') {
						foreach ($this->blogContents as $key => $value) {
							if ($params['controller'] == $value['BlogContent']['name']) {
								$judge = true;
								break;
							}
						}
					}
				}
			}
			
			if (!$judge) {
				if (empty($params['admin']) || $params['prefix'] != 'admin') {
					if (isset($params['plugin']) && $params['plugin'] == 'blog') {
						if (isset($params['controller']) && $params['controller'] == 'blog') {
							if (isset($params['action']) && $params['action'] == 'archives') {
								if (count($params['pass']) == 1) {
									$judge = true;
								}
							}

						}
					}
				}
			}
			
			if ($judge) {
				if (isset($params['action']) && $params['action'] == 'archives') {
					if (count($params['pass']) == 1) {

						$urls = explode('/', $link);
						if (is_array($urls)) {
							// SP、FPでは判定構造を１つ繰り上げる
							if (!empty($this->params['prefix'])) {
								$urls[1] = $urls[2];
								$urls[3] = $urls[4];
							}
							
							foreach ($this->blogContents as $key => $value) {
								if ($urls[1] == $value['BlogContent']['name']) {

									if (ClassRegistry::isKeySet('Blog.BlogPost')) {
										$BlogPostModel = ClassRegistry::getObject('Blog.BlogPost');
									} else {
										$BlogPostModel = ClassRegistry::init('Blog.BlogPost');
									}
									$post = $BlogPostModel->find('first', array(
										'conditions' => array(
											'BlogPost.blog_content_id' => $value['BlogContent']['id'],
											'BlogPost.no' => $urls[3]
										),
										// recursiveを設定しないと「最近の投稿」で OptionalLink が取得できない
										'recursive' => 1
									));
									if ($post && !empty($post['OptionalLink'])) {
										$link = '';
										if ($post['OptionalLink']['status']) {
											$link = $post['OptionalLink']['name'];
										}
										$strBlank = '';
										if ($post['OptionalLink']['blank']) {
											$strBlank = ' target="_blank"';
										}
										if ($link) {
											// <a href="/URL">TEXT</a>
											//$regex = '/(<a href=[\'|"])(.*?)([\'|"].*</a>)/';
											$regex = '/href=\"(.+)\"/';
											$replacement = 'href="'. $link .'"';
											if ($strBlank) {
												$replacement = $replacement . $strBlank;
											}
											$out = preg_replace($regex, $replacement, $out);
										}
									}
									
								}
							}
						}
					}
				}
			}
		}
		return $out;
	}
	
}

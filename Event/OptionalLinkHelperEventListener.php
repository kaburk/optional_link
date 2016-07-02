<?php

/**
 * [HelperEventListener] OptionalLink
 *
 * @link			http://www.materializing.net/
 * @author			arata
 * @license			MIT
 */
class OptionalLinkHelperEventListener extends BcHelperEventListener
{

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
	private $isBlogArchivesUrl = false;

	/**
	 * URL書換を機能させるかどうかを判定
	 * - OptionalLink データがあり、かつ設定が有効の際に書換る
	 * 
	 * @var boolean
	 */
	private $isRewrite = false;

	/**
	 * ブログデータ
	 * 
	 * @var array
	 */
	private $blogContentList = array();

	/**
	 * ブログ記事データ
	 * 
	 * @var array
	 */
	private $blogPostList = array();

	/**
	 * formAfterCreate
	 * - ブログ記事追加・編集画面に編集欄を追加する
	 * 
	 * @param CakeEvent $event
	 */
	public function formAfterCreate(CakeEvent $event)
	{
		if (!BcUtil::isAdminSystem()) {
			return $event->data['out'];
		}

		$View = $event->subject();

		if ($View->request->params['controller'] != 'blog_posts') {
			return $event->data['out'];
		}

		if (!in_array($View->request->params['action'], array('admin_edit', 'admin_add'))) {
			return $event->data['out'];
		}

		// ブログ記事追加・編集画面に編集欄を追加する
		if ($event->data['id'] != 'BlogPostForm') {
			return $event->data['out'];
		}

		if (isset($View->request->data['OptionalLinkConfig'])) {
			if (!empty($View->request->data['OptionalLinkConfig']['status'])) {
				$event->data['out'] = $event->data['out'] . $View->element('OptionalLink.admin/optional_link_form', array('model' => 'BlogPost'));
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
	public function formAfterEnd(CakeEvent $event)
	{
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
			$this->modelInitializer($View);
			if ($this->optionalLinkConfigs) {
				$output				 = '<div id="OptionalLinkConfigBox">';
				$output .= $View->BcBaser->getLink('≫オプショナルリンク設定', array(
					'plugin'	 => 'optional_link',
					'controller' => 'optional_link_configs',
					'action'	 => 'edit', $this->optionalLinkConfigs['OptionalLinkConfig']['id']
				));
				$output .= '</div>';
				$event->data['out']	 = $event->data['out'] . $output;
			}
		}

		return $event->data['out'];
	}

	/**
	 * モデル登録用メソッド
	 * 
	 * @param View $View
	 */
	private function modelInitializer($View)
	{
		if (ClassRegistry::isKeySet('OptionalLink.OptionalLinkConfig')) {
			$this->OptionalLinkConfigModel = ClassRegistry::getObject('OptionalLink.OptionalLinkConfig');
		} else {
			$this->OptionalLinkConfigModel = ClassRegistry::init('OptionalLink.OptionalLinkConfig');
		}

		$this->optionalLinkConfigs = $this->OptionalLinkConfigModel->find('first', array(
			'conditions' => array(
				'OptionalLinkConfig.blog_content_id' => $View->Blog->blogContent['id'],
			),
			'recursive'	 => -1,
		));
	}

	/**
	 * htmlBeforeGetLink
	 * 
	 * @param CakeEvent $event
	 * @return string
	 */
	public function htmlBeforeGetLink(CakeEvent $event)
	{
		$this->judgeRewriteUrl($event);
		return $event->data['options'];
	}

	/**
	 * リンクのURLを書き換えるかどうかを判定する
	 * 
	 * @param CakeEvent $event
	 * @return
	 */
	private function judgeRewriteUrl(CakeEvent $event)
	{
		// 管理システム側でのアクセスではURL変換を行わない
		if (BcUtil::isAdminSystem()) {
			return;
		}

		$View					 = $event->subject();
		$this->isBlogArchivesUrl = false;  // URLが記事詳細へのURLかの判定を初期化
		$this->isRewrite		 = false; // URL書換を機能させるかの判定を初期化
		$this->optionalLink		 = null; // オプショナルリンク値を初期化
		$blogContent			 = array();  // URLが持つブログコンテンツ値を初期化

		if (!is_array($event->data['url'])) {
			$this->url = Router::parse($event->data['url']);
		} else {
			$this->url = $event->data['url'];
		}
		if (!$this->url) {
			return;
		}

		if (isset($this->url['admin'])) {
			// URLが、管理システム側の場合は書き換えを行わない
			if ($this->url['admin']) {
				return;
			}
		}

		if (!isset($this->url['action'])) {
			return;
		}

		// URLが、ブログ記事詳細へのリンクかどうかを判定する
		if (!$this->isBlogArchivesUrl) {
			$blogContent = $this->hasBlogContent($this->url);
			if ($blogContent) {
				$this->isBlogArchivesUrl = true;
			}
		}

		// URLが、ブログ記事詳細へのリンクではない場合、リンクの書換えを行わない
		if (!$this->isBlogArchivesUrl) {
			return;
		}

		$this->modelInitializer($View);

		// URLに対するオプショナルリンク設定がない場合はURL書き換えを行わない
		if (!$this->optionalLinkConfigs) {
			return;
		}

		// オプショナルリンク設定が無効の場合はURL書き換えを行わない
		if (!$this->optionalLinkConfigs['OptionalLinkConfig']['status']) {
			return;
		}

		// URLが持つブログコンテンツとURLが持つ記事NOより、ブログ記事を特定する
		$post = $this->getBlogPostData($blogContent['BlogContent']['id'], $this->url[0]);
		if (!$post) {
			return;
		}

		// URL書換えの実施判定。オプショナルリンク値の有無、ステータスにより判定する
		$this->isRewrite = $this->isRewriteUrl($post);
		if (!$this->isRewrite) {
			return;
		}

		// URL書換えが有効な場合は、以降を実施する
		$this->optionalLink['OptionalLink'] = $post['OptionalLink'];
		switch ($this->optionalLink['OptionalLink']['status']) {
			case '1': // ステータスがURLの場合
				if ($this->optionalLink['OptionalLink']['blank']) {
					$event->data['options']['target'] = '_blank';
				}
				break;

			case '2': // ステータスがファイルの場合
				$optionalLink = $this->optionalLink['OptionalLink'];
				if ($optionalLink['file']) {
					// サムネイル側へのリンクになるため、imgsize => large を指定する
					$fileLink	 = $View->BcUpload->uploadImage('OptionalLink.file', $optionalLink['file'], array('imgsize' => 'large'));
					$result		 = preg_match('/.+<?\shref=[\'|"](.*?)[\'|"].*/', $fileLink, $match);
					if ($result) {
						$optionalLink['name']				 = $match[1]; // ファイルの場合はnameにファイルへのURLを入れる - modify by gondoh
						$event->data['options']['target']	 = '_blank'; // 問答無用でblank
					}
				}
				$this->optionalLink['OptionalLink'] = $optionalLink;
				break;

			default:
				break;
		}
	}

	/**
	 * htmlAfterGetLink
	 * 
	 * @param CakeEvent $event
	 * @return string
	 */
	public function htmlAfterGetLink(CakeEvent $event)
	{
		$View = $event->subject();
		if ($this->isRewrite) {
			$event->data['out'] = $this->rewriteUrl($View, $event->data['out']);
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
	private function rewriteUrl($View, $out)
	{
		if (!$this->optionalLink) {
			return $out;
		}

		if ($this->optionalLink['OptionalLink']['status']) {

			switch ($this->optionalLink['OptionalLink']['status']) {
				case '1': // URLの場合
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
							$regex		 = '/href=\"(.+?)\"/';
							$replacement = 'href="' . $link . '"';
							$out		 = preg_replace($regex, $replacement, $out);
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

				case '2': // ファイルの場合
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
							$regex		 = '/href=\"(.+?)\"/';
							$replacement = 'href="' . $link . '"';
							$out		 = preg_replace($regex, $replacement, $out);
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

		return $out;
	}

	/**
	 * URLがブログ記事詳細であることを判定し、そのURLが持つブログコンテンツを取得する
	 * 
	 * @param array $url
	 * @return array
	 */
	private function hasBlogContent($url)
	{
		$data = array();
		if ($url['action'] == 'archives') {
			// 引数のURLが1つ（記事詳細）のときに有効とする
			if (!empty($url[0]) && !isset($url[1])) {
				if (!$this->blogContentList) {
					if (ClassRegistry::isKeySet('Blog.BlogContent')) {
						$BlogContentModel = ClassRegistry::getObject('Blog.BlogContent');
					} else {
						$BlogContentModel = ClassRegistry::init('Blog.BlogContent');
					}
					$this->blogContentList = $BlogContentModel->find('all', array('recursive' => -1));
				}
				foreach ($this->blogContentList as $blogContent) {
					if ($url['controller'] == $blogContent['BlogContent']['name']) {
						$data = $blogContent;
						break;
					}
				}
			}
		}
		return $data;
	}

	/**
	 * ブログコンテンツIDとブログ記事NOからブログ記事データを取得する
	 * 
	 * @param int $blogContentId
	 * @param int $blogPostNo
	 * @return array
	 */
	private function getBlogPostData($blogContentId, $blogPostNo)
	{
		$post = array();

		if (!$this->blogPostList) {
			// 現在の画面ではなく、ブログ記事のURLに対しての情報が必要なため取得する
			if (ClassRegistry::isKeySet('Blog.BlogPost')) {
				$BlogPostModel = ClassRegistry::getObject('Blog.BlogPost');
			} else {
				$BlogPostModel = ClassRegistry::init('Blog.BlogPost');
			}

			$conditions			 = $BlogPostModel->getConditionAllowPublish();
			$this->blogPostList	 = $BlogPostModel->find('all', array(
				'conditions' => $conditions,
				'fields'	 => array(
					'id', 'blog_content_id', 'no', 'name', 'blog_category_id', 'user_id', 'status', 'posts_date',
				),
				'order'		 => 'BlogPost.id DESC',
				'recursive'	 => 2,
			));
		}

		$target = Hash::extract($this->blogPostList, "{n}.BlogPost[blog_content_id={$blogContentId}][no={$blogPostNo}]");
		if ($target) {
			$blogPostId = $target[0]['id'];
			foreach ($this->blogPostList as $key => $blogPost) {
				if ($blogPostId == $blogPost['BlogPost']['id']) {
					$post = $this->blogPostList[$key];
					break;
				}
			}
		}

		return $post;
	}

	/**
	 * 特定したブログ記事から、その記事のオプショナルリンク値を判定する
	 * - オプショナルリンク値を持つ場合、そのステータス値から、URL書換えの実施を判定する
	 * 
	 * @param array $post
	 * @return boolean
	 */
	private function isRewriteUrl($post)
	{
		// 特定したブログ記事のオプショナルリンク値のステータスが未使用 or 存在しない値の場合はURL書換えを行わない
		if (Hash::get($post, 'OptionalLink.status')) {
			return true;
		}
		return false;
	}

}

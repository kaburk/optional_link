<?php

/**
 * [ControllerEventListener] OptionalLink
 *
 * @link			http://www.materializing.net/
 * @author			arata
 * @license			MIT
 */
class OptionalLinkControllerEventListener extends BcControllerEventListener
{

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
	private $optionalLinkConfigs = array();

	/**
	 * OptionalLinkモデル
	 * 
	 * @var Object
	 */
	private $OptionalLinkModel = null;

	/**
	 * OptionalLink設定モデル
	 * 
	 * @var Object
	 */
	private $OptionalLinkConfigModel = null;

	/**
	 * 処理対象とするコントローラー
	 * 
	 * @var array
	 */
	private $targetController = array('blog_posts', 'blog_contents');

	/**
	 * 処理対象とするアクション
	 * 
	 * @var array
	 */
	private $targetAction = array('admin_edit', 'admin_add');

	/**
	 * initialize
	 * 
	 * @param CakeEvent $event
	 */
	public function initialize(CakeEvent $event)
	{
		$Controller				 = $event->subject();
		$Controller->helpers[]	 = 'OptionalLink.OptionalLink';
	}

	/**
	 * OptionalLinkConfig モデルを準備する
	 * 
	 */
	private function setUpModel()
	{
		if (ClassRegistry::isKeySet('OptionalLink.OptionalLinkConfig')) {
			$this->OptionalLinkConfigModel = ClassRegistry::getObject('OptionalLink.OptionalLinkConfig');
		} else {
			$this->OptionalLinkConfigModel = ClassRegistry::init('OptionalLink.OptionalLinkConfig');
		}
	}

	/**
	 * startup
	 * 
	 * @param CakeEvent $event
	 */
	public function startup(CakeEvent $event)
	{
		if (!BcUtil::isAdminSystem()) {
			return;
		}

		$Controller = $event->subject();
		if (!in_array($Controller->request->params['controller'], $this->targetController)) {
			return;
		}

		$this->setUpModel();
		// ブログ記事編集画面でタグの追加を行うと Undefined が発生するため判定
		if (!empty($Controller->BlogContent->id)) {
			$this->optionalLinkConfigs	 = $this->OptionalLinkConfigModel->find('first', array(
				'conditions' => array(
					'OptionalLinkConfig.blog_content_id' => $Controller->BlogContent->id
				),
				'recursive'	 => -1
			));
			$this->OptionalLinkModel	 = ClassRegistry::init('OptionalLink.OptionalLink');
		}
	}

	/**
	 * blogBlogStartup
	 * 
	 * @param CakeEvent $event
	 */
	public function blogBlogStartup(CakeEvent $event)
	{
		if (BcUtil::isAdminSystem()) {
			return;
		}

		$Controller					 = $event->subject();
		$this->setUpModel();
		$this->optionalLinkConfigs	 = $this->OptionalLinkConfigModel->find('first', array(
			'conditions' => array(
				'OptionalLinkConfig.blog_content_id' => $Controller->BlogContent->id
			),
			'recursive'	 => -1
		));
	}

	/**
	 * blogBlogBeforeRender
	 * 
	 * @param CakeEvent $event
	 */
	public function blogBlogBeforeRender(CakeEvent $event)
	{
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

		if (!$this->isRedirect($Controller)) {
			return;
		}

		$this->redirectOptionalLinkUrl($Controller);
	}

	/**
	 * ブログ記事詳細へのアクセス時、オプショナルリンクの値でリダイレクトするか判定する
	 * - 記事プレビューは非対応
	 * 
	 * @param Opject $Controller
	 * @return boolean
	 */
	private function isRedirect($Controller)
	{
		if (!isset($Controller->viewVars['single'])) {
			return false;
		}
		if (!$Controller->viewVars['single']) {
			return false;
		}
		// プレビューの際はオプショナルリンク設定を取得しないため対応しない
		if (!$this->optionalLinkConfigs) {
			return false;
		}
		if (!$this->optionalLinkConfigs['OptionalLinkConfig']['status']) {
			return false;
		}
		if (empty($Controller->viewVars['post']['OptionalLink'])) {
			return false;
		}
		if (!$Controller->viewVars['post']['OptionalLink']['status']) {
			return false;
		}
		return true;
	}

	/**
	 * ブログ記事詳細へのアクセス時、オプショナルリンクの値でリダイレクトさせて、記事詳細画面を表示しないようにする
	 * - ViewEventHelperの処理と似ているが、動作の流れが異なるため共通メソッド化はしない
	 * 
	 * @param Opject $Controller
	 */
	private function redirectOptionalLinkUrl($Controller)
	{
		$optionalLinkData['OptionalLink'] = $Controller->viewVars['post']['OptionalLink'];

		switch ($optionalLinkData['OptionalLink']['status']) {
			case '1': // URLの場合
				if (!$optionalLinkData['OptionalLink']['nolink']) {
					$link = $optionalLinkData['OptionalLink']['name'];
					if ($link) {
						// /files〜 の場合はドメインを付与して絶対指定扱いにする
						$regexFiles = '/^\/files\/.+/';
						if (preg_match($regexFiles, $link)) {
							// /lib/Baser/basics.php
							$link = topLevelUrl(false) . $link;
							//$link = Configure::read('BcEnv.siteUrl') . $link;
						}
						$Controller->redirect($link);
					}
				} else {
					// リンクしない場合は文字列に置換する
					// 例：<a href="/news/archives/2>(.)</a>
					$Controller->notFound();
				}
				break;

			case '2': // ファイルの場合
				$optionalLink = $optionalLinkData['OptionalLink'];

				if ($optionalLink['file']) {
					// サムネイル側へのリンクになるため、imgsize => large を指定する
					App::uses('BcUploadHelper', 'View/Helper');
					$View			 = new View();
					$View->BcUpload	 = new BcUploadHelper($View);
					$fileLink		 = $View->BcUpload->uploadImage('OptionalLink.file', $optionalLink['file'], array('imgsize' => 'large'));
					$result			 = preg_match('/.+<?\shref=[\'|"](.*?)[\'|"].*/', $fileLink, $match);
					if ($result) {
						$optionalLink['name'] = $match[1];	// ファイルの場合はnameにファイルへのURLを入れる - modify by gondoh
					}
				}
				$optionalLinkData['OptionalLink'] = $optionalLink;

				$link = $optionalLinkData['OptionalLink']['name'];
				if ($link) {
					// ファイルの公開期間をチェックする
					App::uses('OptionalLinkHelper', 'OptionalLink.View/Helper');
					$View->OptionalLink	 = new OptionalLinkHelper(new View());
					$checkPublish		 = $View->OptionalLink->allowPublishFile($optionalLinkData);
					if ($checkPublish) {
						// /files〜 の場合はドメインを付与して絶対指定扱いにする
						$regexFiles = '/^\/files\/.+/';
						if (preg_match($regexFiles, $link)) {
							// /lib/Baser/basics.php
							$link = topLevelUrl(false) . $link;
							//$link = Configure::read('BcEnv.siteUrl') . $link;
						}
						$Controller->redirect($link);
					} else {
						// ファイルの公開期間が終了していれば、リンクしない文字列に置換する
						$Controller->notFound();
					}
				}
				break;

			default:
				break;
		}
	}

	/**
	 * blogBlogPostsBeforeRender
	 * 
	 * @param CakeEvent $event
	 */
	public function blogBlogPostsBeforeRender(CakeEvent $event)
	{
		if (!BcUtil::isAdminSystem()) {
			return;
		}

		// オプショナルリンク設定データがない場合は何もせず通常動作にする
		if (!$this->optionalLinkConfigs) {
			return;
		}

		$Controller = $event->subject();
		if (!in_array($Controller->request->params['action'], $this->targetAction)) {
			return;
		}

		if ($Controller->request->params['action'] == 'admin_add') {
			$defalut									 = $this->OptionalLinkModel->getDefaultValue();
			$Controller->request->data['OptionalLink']	 = $defalut['OptionalLink'];
		}

		if (isset($Controller->request->data['OptionalLink'])) {
			if (empty($Controller->request->data['OptionalLink']['id'])) {
				$defalut									 = $this->OptionalLinkModel->getDefaultValue();
				$Controller->request->data['OptionalLink']	 = $defalut['OptionalLink'];
			}
		}

		$Controller->request->data['OptionalLinkConfig'] = $this->optionalLinkConfigs['OptionalLinkConfig'];
	}

}

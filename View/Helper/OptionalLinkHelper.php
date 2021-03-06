<?php

/**
 * [Helper] Optional_link
 *
 * @link			http://www.materializing.net/
 * @author			arata
 * @license			MIT
 */
class OptionalLinkHelper extends AppHelper
{

	/**
	 * ヘルパー
	 *
	 * @var array
	 */
	public $helpers = array('BcBaser', 'Blog', 'BcUpload');

	/**
	 * アップロードファイルの保存URL
	 * 
	 * @var string
	 */
	public $savedUrl = '';

	/**
	 * アップロードファイルの保存パス
	 * 
	 * @var string
	 */
	public $savePath = '';

	/**
	 * constructer
	 * 
	 * @param View $View
	 * @param array $settings
	 */
	public function __construct(View $View, $settings = array())
	{
		parent::__construct($View, $settings);
		$this->savedUrl	 = baseUrl() . 'files' . DS . 'optionallink' . DS;
		$this->savePath	 = OptionalLinkUtil::getSavePath() . DS;
	}

	/**
	 * 公開状態を取得する
	 * 
	 * @param array $data
	 * @return boolean 公開状態
	 */
	public function allowPublish($data)
	{
		if (isset($data['OptionalLink'])) {
			$data = $data['OptionalLink'];
		} elseif (isset($data['OptionalLinkConfig'])) {
			$data = $data['OptionalLinkConfig'];
		}
		$allowPublish = (int) $data['status'];
		return $allowPublish;
	}

	/**
	 * ファイルの公開状態を取得する
	 *
	 * @param array リンク状態のデータ
	 * @return boolean 公開状態
	 */
	public function allowPublishFile($data)
	{
		if (isset($data['OptionalLink'])) {
			$data = $data['OptionalLink'];
		}
		$allowPublish = true;
		// 期限を設定している場合に条件に該当しない場合は強制的に非公開とする
		if (($data['publish_begin'] != 0 && $data['publish_begin'] >= date('Y-m-d H:i:s')) ||
				($data['publish_end'] != 0 && $data['publish_end'] <= date('Y-m-d H:i:s'))) {
			$allowPublish = false;
		}
		return $allowPublish;
	}

	/**
	 * オプショナルリンクの有効を判定する
	 * 
	 * @param array $post
	 * @return boolean
	 */
	public function isStatus($post = array())
	{
		if (!empty($post['OptionalLinkConfig']['status'])) {
			if ($post['OptionalLinkConfig']['status']) {
				return true;
			}
		}
		return false;
	}

	/**
	 * リンク文字列をチェックして判定する
	 * 
	 * @param array $post
	 * @return string
	 */
	public function judgeLinkKinds($post = array())
	{
		$str = '';
		if ($post['OptionalLink']['status']) {
			switch ($post['OptionalLink']['status']) {
				case '1':
					if (!empty($post['OptionalLink']['name'])) {
						if ($post['OptionalLink']['blank']) {
							$str = 'external';
						}
						$content = trim(strip_tags($post['OptionalLink']['name']));
						// URLを分解する
						$links	 = parse_url($content);
						$path	 = pathinfo($content);

						if (!empty($path['extension'])) {
							$str = OptionalLinkUtil::getUrlExtension($path['extension']);
						}
						if ($str) {
							return $str;
						}

						if (!empty($links['host'])) {
							if ($_SERVER['HTTP_HOST'] != $links['host']) {
								$str = 'external';
							}
						}
					}
					break;

				case '2':
					if (!empty($post['OptionalLink']['file'])) {
						$content = trim(strip_tags($post['OptionalLink']['file']));
						// URLを分解する
						$links	 = parse_url($content);
						$path	 = pathinfo($content);

						if (!empty($path['extension'])) {
							$str = OptionalLinkUtil::getUrlExtension($path['extension']);
						}
						if ($str) {
							return $str;
						}
					}
					break;

				default:
					break;
			}
		}
		return $str;
	}

	/**
	 * オプショナルリンクの設定を反映したブログ記事リンクを出力する
	 * - posts ビューで記事を取得する箇所で利用できる
	 * - 利用方法：$this->OptionalLink->getPostTitle($post)
	 * 
	 * @param array $post
	 * @param array $options
	 * @return string
	 */
	public function getPostTitle($post = array(), $options = array())
	{
		$_options	 = array(
			'link' => true,
		);
		$options	 = Hash::merge($_options, $options);
		$url		 = '';
		$this->Blog->setContent($post['BlogPost']['blog_content_id']);

		if ($options['link']) {
			if (isset($post['OptionalLink']) && $post['OptionalLink']['status'] >= 1) {

				switch ($post['OptionalLink']['status']) {
					case '1':
						$url = topLevelUrl(false) . $post['OptionalLink']['name'];
						if ($post['OptionalLink']['blank']) {
							$options['target'] = '_blank';
						}
						if ($post['OptionalLink']['nolink']) {
							return $post['BlogPost']['name'];
						}
						break;

					case '2':
						// サムネイル側へのリンクになるため、imgsize => large を指定する
						$fileLink	 = $this->BcUpload->uploadImage('OptionalLink.file', $post['OptionalLink']['file'], array('imgsize' => 'large'));
						$result		 = preg_match('/.+<?\shref=[\'|"](.*?)[\'|"].*/', $fileLink, $match);
						if ($result) {
							$post['OptionalLink']['name']	 = $match[1];
							$url							 = $post['OptionalLink']['name'];
							$options['target']				 = '_blank';
						}
						break;

					default:
						break;
				}
			} else {
				$url = array('admin' => false, 'plugin' => '', 'controller' => $this->Blog->blogContent['name'], 'action' => 'archives', $post['BlogPost']['no']);
			}

			return $this->BcBaser->getLink($post['BlogPost']['name'], $url, $options);
		} else {
			return $post['BlogPost']['name'];
		}
	}

	/**
	 * ファイルが保存されているURLを取得する
	 *
	 * @param string $fileName
	 * @return string
	 */
	public function getFileUrl($fileName)
	{
		if ($fileName) {
			return $this->savedUrl . $fileName;
		} else {
			return '';
		}
	}

	/**
	 * ファイルリンクタグを出力する
	 * 
	 * @param array $uploaderFile
	 * @param array $options
	 * @return string リンクタグ
	 */
	public function file($uploaderFile, $options = array())
	{
		$_options	 = array(
			'alt'	 => $uploaderFile['file'],
			'target' => '_blank',
		);
		$options	 = Hash::merge($_options, $options);

		if (isset($uploaderFile['OptionalLink'])) {
			$uploaderFile = $uploaderFile['OptionalLink'];
		}

		if (!empty($uploaderFile['file'])) {
			$imgUrl = $this->getFileUrl($uploaderFile['file']);
			//$pathInfo = pathinfo($uploaderFile['file']);
			if (!empty($uploaderFile['publish_begin']) || !empty($uploaderFile['publish_end'])) {
				$savePath = $this->savePath . 'limited' . DS . $uploaderFile['file'];
			} else {
				$savePath = $this->savePath . $uploaderFile['file'];
			}
			if (file_exists($savePath)) {
				$out = $this->BcBaser->getLink('≫ファイル', $imgUrl, $options);
				return $out;
			}
		}
		return '';
	}

	/**
	 * $postからURLを返す
	 * 
	 * @param array $post
	 * @return string リンクURL
	 */
	public function getPostUrl($post)
	{
		$url	 = $this->Blog->getPostLinkUrl($post);
		$target	 = '';
		if ($post['OptionalLink']['status']) {
			if ($post['OptionalLink']['status'] == "1") {
				if ($post['OptionalLink']['nolink']) {
					$url = null;
				} else {
					if ($post['OptionalLink']['blank'])
						$target	 = 'target="_blank"';
					if ($post['OptionalLink']['name'])
						$url	 = $post['OptionalLink']['name'];
				}
			} elseif ($post['OptionalLink']['status'] == "2") {
				if ((!$post['OptionalLink']['publish_begin'] || strtotime($post['OptionalLink']['publish_begin']) < time()) &&
						(!$post['OptionalLink']['publish_end'] || strtotime($post['OptionalLink']['publish_end']) > time())
				) {
					$target	 = 'target="_blank"';
					$url	 = "/files/optionallink/" . $post['OptionalLink']['file'];
				} else {
					$url = null;
				}
			}
		}

		return $url;
	}

	/**
	 * $postからリンクターゲットの文字列を返す
	 * 
	 * @param array $post
	 * @return string ターゲット(target="_blank")
	 */
	public function getPostTarget($post)
	{
		$url	 = $this->Blog->getPostLinkUrl($post);
		$target	 = '';
		if ($post['OptionalLink']['status']) {
			if ($post['OptionalLink']['status'] == "1") {
				if ($post['OptionalLink']['nolink']) {
					$url = null;
				} else {
					if ($post['OptionalLink']['blank'])
						$target	 = 'target="_blank"';
					if ($post['OptionalLink']['name'])
						$url	 = $post['OptionalLink']['name'];
				}
			} elseif ($post['OptionalLink']['status'] == "2") {
				if ((!$post['OptionalLink']['publish_begin'] || strtotime($post['OptionalLink']['publish_begin']) < time()) &&
						(!$post['OptionalLink']['publish_end'] || strtotime($post['OptionalLink']['publish_end']) > time())
				) {
					$target	 = 'target="_blank"';
					$url	 = "/files/optionallink/" . $post['OptionalLink']['file'];
				} else {
					$url = null;
				}
			}
		}

		return $target;
	}

}

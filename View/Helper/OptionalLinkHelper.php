<?php
/**
 * [Helper] Optional_link
 *
 * @link			http://www.materializing.net/
 * @author			arata
 * @license			MIT
 */
class OptionalLinkHelper extends AppHelper {
/**
 * ヘルパー
 *
 * @var array
 */
	public $helpers = array('BcBaser', 'Blog', 'BcUpload');
	
/**
 * 除外状態を取得する
 * 
 * @param array $data
 * @return boolean 除外状態
 */
	public function allowPublish($data){
		if (isset($data['OptionalLink'])){
			$data = $data['OptionalLink'];
		} elseif (isset($data['OptionalLinkConfig'])) {
			$data = $data['OptionalLinkConfig'];
		}
		$allowPublish = (int)$data['status'];
		return $allowPublish;
	}
	
/**
 * オプショナルリンクの有効を判定する
 * 
 * @param array $post
 * @return boolean
 */	
	public function judgeStatus($post = array()) {
		if(!empty($post['OptionalLinkConfig']['status'])) {
			if($post['OptionalLinkConfig']['status']) {
				return true;
			}
		}
		return false;
	}
	
/**
 * リンク文字列をチェックして判定する
 * 
 * @param type $post
 * @return string
 */
	public function judgeLinkKinds($post = array()) {
		$str = '';
		if(!empty($post['OptionalLink']['name'])) {
			$content = trim(strip_tags($post['OptionalLink']['name']));
			// URLを分解する
			$links = parse_url($content);
			$path = pathinfo($content);
			
			if (!empty($path['extension'])) {
				if ($path['extension'] == 'pdf') {
					$str = 'pdf';
				}
				if ($path['extension'] == 'xls' || $path['extension'] == 'xlsx') {
					$str = 'excel';
				}
				if ($path['extension'] == 'doc' || $path['extension'] == 'docx') {
					$str = 'word';
				}
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
	public function getPostTitle($post = array(), $options = array()) {
		$_options = array(
			'link' => true,
		);
		$options = Hash::merge($_options, $options);
		$url = '';
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
						$fileLink = $this->BcUpload->uploadImage('OptionalLink.file', $post['OptionalLink']['file']);
						$result = preg_match('/.+<?\shref=[\'|"](.*?)[\'|"].*/', $fileLink, $match);
						if ($result) {
							$post['OptionalLink']['name'] = $match[1];
							$url = $post['OptionalLink']['name'];
							$options['target'] = '_blank';
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
	
}

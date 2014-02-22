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
	public $helpers = array('Blog', 'Html');
	
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
	
}

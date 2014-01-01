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
	
}

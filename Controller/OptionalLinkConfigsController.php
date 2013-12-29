<?php
/**
 * [Controller] Optional_link
 *
 * @link			http://www.materializing.net/
 * @author			arata
 * @license			MIT
 */
/**
 * Include files
 */
App::import('Controller', 'OptionalLink.OptionalLinkApp');
class OptionalLinkConfigsController extends OptionalLinkAppController {
/**
 * ControllerName
 * 
 * @var string
 */
	public $name = 'OptionalLinkConfigs';
	
/**
 * Model
 * 
 * @var array
 */
	public $uses = array('OptionalLink.OptionalLinkConfig');
	
/**
 * ぱんくずナビ
 *
 * @var string
 */
	public $crumbs = array(
		array('name' => 'プラグイン管理', 'url' => array('plugin' => '', 'controller' => 'plugins', 'action' => 'index')),
		array('name' => 'オプショナルリンク設定管理', 'url' => array('plugin' => 'optional_link', 'controller' => 'optional_link_configs', 'action' => 'index'))
	);
	
/**
 * 管理画面タイトル
 *
 * @var string
 */
	public $adminTitle = 'オプショナルリンク設定';
	
/**
 * beforeFilter
 *
 * @return	void
 */
	public function beforeFilter() {		
		parent::beforeFilter();
	}
	
/**
 * [ADMIN] 設定一覧
 * 
 * @return void
 */
	public function admin_index() {
		$this->pageTitle = $this->adminTitle . '一覧';
		$this->search = 'optional_link_configs_index';
		$this->help = 'optional_link_configs_index';
		parent::admin_index();
	}
	
/**
 * [ADMIN] 編集
 * 
 * @param int $id
 * @return void
 */
	public function admin_edit($id = null) {
		$this->pageTitle = $this->adminTitle . '編集';
		parent::admin_edit($id);
	}
	
/**
 * [ADMIN] 削除
 *
 * @param int $id
 * @return void
 */
	public function admin_delete($id = null) {
		parent::admin_delete($id);
	}
	
/**
 * [ADMIN] 各ブログ別のオプショナル設定データを作成する
 *   ・オプショナル設定データがないブログ用のデータのみ作成する
 * 
 * @return void
 */
	public function admin_first() {
		if ($this->data) {
			$count = 0;
			if ($this->blogContentDatas) {
				foreach ($this->blogContentDatas as $key => $blog) {	
					$configData = $this->OptionalLinkConfig->findByBlogContentId($key);
					if (!$configData) {
						$this->data['OptionalLinkConfig']['blog_content_id'] = $key;
						$this->data['OptionalLinkConfig']['status'] = true;
						$this->OptionalLinkConfig->create($this->data);
						if (!$this->OptionalLinkConfig->save($this->data, false)) {
							$this->log(sprintf('ブログID：%s の登録に失敗しました。', $key));
						} else {
							$count++;
						}
					}
				}
			}
			
			$message = sprintf('%s 件のオプショナル設定を登録しました。', $count);
			$this->setMessage($message);
			$this->redirect(array('controller' => 'optional_link_configs', 'action' => 'index'));
		}
		
		$this->pageTitle = $this->adminTitle . 'データ作成';
	}
	
/**
 * 一覧用の検索条件を生成する
 *
 * @param array $data
 * @return array $conditions
 */
	public function _createAdminIndexConditions($data) {
		
		$conditions = array();
		$blogContentId = '';
		
		if (isset($data[$this->modelClass]['blog_content_id'])) {
			$blogContentId = $data[$this->modelClass]['blog_content_id'];
		}
		if (isset($data[$this->modelClass]['status']) && $data[$this->modelClass]['status'] === '') {
			unset($data[$this->modelClass]['status']);
		}
		
		unset($data['_Token']);
		unset($data[$this->modelClass]['blog_content_id']);
		
		// 条件指定のないフィールドを解除
		foreach($data[$this->modelClass] as $key => $value) {
			if ($value === '') {
				unset($data[$this->modelClass][$key]);
			}
		}
		
		if ($data[$this->modelClass]) {
			$conditions = $this->postConditions($data);
		}
		
		if ($blogContentId) {
			$conditions = array(
				$this->modelClass .'.blog_content_id' => $blogContentId
			);
		}
		
		if($conditions) {
			return $conditions;
		} else {
			return array();
		}
		
	}
	
}

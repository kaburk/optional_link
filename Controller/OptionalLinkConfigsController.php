<?php
/**
 * [Controller] OptionalLink
 *
 * @link			http://www.materializing.net/
 * @author			arata
 * @license			MIT
 */
App::uses('OptionalLinkApp', 'OptionalLink.Controller');
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
	public $uses = array('OptionalLink.OptionalLinkConfig', 'OptionalLink.OptionalLink');
	
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
		
		$hasDir = true;
		if (!$this->hasUploadFileFolder()) {
			$savePath = OptionalLinkUtil::getSavePath();
			$fileHtaccess = OptionalLinkUtil::getLimitedHtaccess();
			
			$message = 'ファイルアップロード用のフォルダが存在するか確認してください。<br />';
			$message .= $savePath .'<br /><br />';
			$message .= '公開制限ファイルアップロード用のフォルダとhtaccessファイルが存在するか確認してください。<br />';
			$message .= $fileHtaccess;
			$this->setMessage($message, true);
			$hasDir = false;
		}
		$this->set('hasDir', $hasDir);
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
 * [ADMIN] 追加
 * 
 * @param int $id
 * @return void
 */
	public function admin_add() {
		$this->pageTitle = $this->adminTitle . '追加';
		
		if ($this->request->is('post')) {
			$this->{$this->modelClass}->create();
			if ($this->{$this->modelClass}->save($this->request->data)) {
				$this->setMessage('追加が完了しました。');
				$this->redirect(array('action' => 'index'));
			} else {
				$this->setMessage('入力エラーです。内容を修正して下さい。', true);
			}
		} else {
			$this->request->data = $this->{$this->modelClass}->getDefaultValue();
		}
		
		// 設定データがあるブログは選択リストから除外する
		$dataList = $this->{$this->modelClass}->find('all');
		if ($dataList) {
			foreach ($dataList as $data) {
				unset($this->blogContentDatas[$data[$this->modelClass]['blog_content_id']]);
			}
		}
		
		$this->set('blogContentDatas', $this->blogContentDatas);
		$this->render('form');
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
		if ($this->request->data) {
			$count = 0;
			if ($this->blogContentDatas) {
				foreach ($this->blogContentDatas as $key => $blog) {	
					$configData = $this->OptionalLinkConfig->findByBlogContentId($key);
					if (!$configData) {
						$this->request->data['OptionalLinkConfig']['blog_content_id'] = $key;
						$this->request->data['OptionalLinkConfig']['status'] = true;
						$this->OptionalLinkConfig->create($this->request->data);
						if (!$this->OptionalLinkConfig->save($this->request->data, false)) {
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
 * [ADMIN] ファイルの公開期間制限に利用するフォルダとファイルを生成する
 *  ・/files/optionallink/limited/.htaccess
 * 
 * @return void
 */
	public function admin_init_folder () {
		// 必要フォルダ初期化
		$filesPath = OptionalLinkUtil::getFilePath();
		$savePath = OptionalLinkUtil::getSavePath();
		$limitedPath = OptionalLinkUtil::getLimitedPath();
		
		if (is_writable($filesPath) && !is_dir($savePath)) {
			mkdir($savePath);
		}
		if (!is_writable($savePath)) {
			chmod($savePath, 0777);
		}
		if (is_writable($savePath) && !is_dir($limitedPath)) {
			mkdir($limitedPath);
		}
		if (!is_writable($limitedPath)) {
			chmod($limitedPath, 0777);
		}
		if (is_writable($limitedPath)) {
			$File = new File(OptionalLinkUtil::getLimitedHtaccess());
			$htaccess = "Order allow,deny\nDeny from all";
			$File->write($htaccess);
			$File->close();
		}
		
		$message = sprintf('フォルダの初期化処理を完了しました。<br />'. $limitedPath .' が作成されていることを確認してください。');
		$this->setMessage($message);
		$this->redirect(array('controller' => 'optional_link_configs', 'action' => 'index'));
	}
	
/**
 * ファイルアップロード用ディレクトリの存在チェックを行う
 * 
 * @return boolean
 */
	protected function hasUploadFileFolder() {
		$savePath = OptionalLinkUtil::getSavePath();
		$limitedPath = OptionalLinkUtil::getLimitedPath();
		$result = false;
		
		if (file_exists($savePath) && is_dir($savePath)) {
			// ファイルアップのためのパスは存在し、ディレクトリである
			$result = true;
		}
		
		if (file_exists($limitedPath) && is_dir($limitedPath)) {
			// 公開制限ファイルアップのためのパスは存在し、ディレクトリである
			$result = true;
		} else {
			$result = false;
		}
		
		if (file_exists(OptionalLinkUtil::getLimitedHtaccess())) {
			// 公開制限ファイルアップのための htaccess は存在している
			$result = true;
		} else {
			$result = false;
		}
		return $result;
	}
	
/**
 * 一覧用の検索条件を生成する
 *
 * @param array $data
 * @return array $conditions
 */
	protected function _createAdminIndexConditions($data) {
		
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

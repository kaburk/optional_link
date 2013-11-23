<?php
/**
 * [HookBehavior] optional_link
 *
 * @link			http://www.materializing.net/
 * @author			arata
 * @license			MIT
 */
class OptionalLinkHookBehavior extends ModelBehavior {
/**
 * 登録フック
 *
 * @var array
 */
	public $registerHooks = array(
			'BlogPost'	=> array(
				'beforeValidate', 'afterDelete', 'beforeFind'),
			'BlogContent'	=> array(
				'afterSave', 'afterDelete')
	);
	
/**
 * beforeValidate
 * 
 * @param Model $model
 * @return boolean
 */
	public function beforeValidate($model) {
		if($model->alias == 'BlogPost') {
			// ブログ記事保存の手前で OptionalLink モデルのデータに対して validation を行う
			$OptionalLinkModel = ClassRegistry::init('OptionalLink.OptionalLink');
			$OptionalLinkModel->set($model->data);
			return $OptionalLinkModel->validates();
			
		}
		return true;
	}
	
	public function afterSave($model, $created) {
		
		if ($model->alias == 'BlogContents') {
			if ($created) {
				// ブログ追加時に設定情報を保存する
				$model->data['OptionalLinkConfig']['blog_content_id'] = $model->BlogContent->id;
				$this->OptionalLinkConfigModel->create($model->data['OptionalLinkConfig']);
			} else {
				// ブログ設定編集保存時に設定情報を保存する
				$this->OptionalLinkConfigModel->set($model->data['OptionalLinkConfig']);
			}
			if(!$this->OptionalLinkConfigModel->save(null, false)) {
				$this->log(sprintf('ID：%s のオプショナルリンク設定の保存に失敗しました。', $model->data['OptionalLinkConfig']['id']));
			}
		}
		
	}
	
/**
 * afterDelete
 * 
 * @param Object $model
 * @return void
 */
	public function afterDelete($model) {
		// ブログ記事削除時、そのブログ記事が持つOptionalLinkを削除する
		if($model->alias == 'BlogPost') {
			$OptionalLinkModel = ClassRegistry::init('OptionalLink.OptionalLink');
			$data = $OptionalLinkModel->find('first', array(
				'conditions' => array('OptionalLink.blog_post_id' => $model->id),
				'recursive' => -1
			));
			if($data) {
				if(!$OptionalLinkModel->delete($data['OptionalLink']['id'])) {
					$this->log('ID:' . $data['OptionalLink']['id'] . 'のOptionalLinkの削除に失敗しました。');
				}
			}
		}
		// ブログ削除時、そのブログが持つOptionalLink設定を削除する
		if($model->alias == 'BlogContent') {
			$OptionalLinkConfigModel = ClassRegistry::init('OptionalLink.OptionalLinkConfig');
			$data = $OptionalLinkConfigModel->find('first', array(
				'conditions' => array('OptionalLink.blog_content_id' => $model->id),
				'recursive' => -1
			));
			if($data) {
				if(!$OptionalLinkConfigModel->delete($data['OptionalLinkConfig']['id'])) {
					$this->log('ID:' . $data['OptionalLinkConfig']['id'] . 'のOptionalLink設定の削除に失敗しました。');
				}
			}
		}
	}
	
/**
 * beforeFind
 * 
 * @param Object $model
 * @param array $query
 * @return array
 */
	public function beforeFind($model, $query) {
		if($model->alias == 'BlogPost') {
			// ブログ記事取得の際にオプショナルリンク情報も併せて取得する
			$association = array(
				'OptionalLink' => array(
					'className' => 'OptionalLink.OptionalLink',
					'foreignKey' => 'blog_post_id'
				)
			);
			$model->bindModel(array('hasOne' => $association));
		}
		return $query;
	}
	
	
}

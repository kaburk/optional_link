<?php
/**
 * OptionalLink 2.1.0 バージョン アップデートスクリプト
 *
 * ----------------------------------------
 * 　アップデートの仕様について
 * ----------------------------------------
 * アップデートスクリプトや、スキーマファイルの仕様については
 * 次のファイルに記載されいているコメントを参考にしてください。
 *
 * /lib/Baser/Controllers/UpdatersController.php
 *
 * スキーマ変更後、モデルを利用してデータの更新を行う場合は、
 * ClassRegistry を利用せず、モデルクラスを直接イニシャライズしないと、
 * スキーマのキャッシュが古いままとなるので注意が必要です。
 *
 * @link			http://www.materializing.net/
 * @author			arata
 * @license			MIT
 */
/**
 * optional_links テーブルの構造変更
 */
	if ($this->loadSchema('2.1.0', 'OptionalLink', '', 'alter')){
		$this->setUpdateLog('optional_links テーブルの構造変更に成功しました。');
	} else {
		$this->setUpdateLog('optional_links テーブルの構造変更に失敗しました。', true);
	}

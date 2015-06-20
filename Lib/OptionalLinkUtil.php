<?php
/**
 * [Lib] OptionalLink
 *
 * @link			http://www.materializing.net/
 * @author			arata
 * @license			MIT
 */
class OptionalLinkUtil extends Object
{
	public static $filesPath = '';
	public static $savePath = '';
	public static $limitedPath = '';
	public static $limitedHtaccess = '';
	
	/**
	 * アップロード用フォルダのパスを取得する
	 * WWW_ROOT .'files';
	 * 
	 * @return string
	 */
	public static function getFilePath() {
		self::$filesPath = WWW_ROOT .'files';
		return self::$filesPath;
	}
	
	/**
	 * オプショナルリンクのファイルアップロード用フォルダのパスを取得する
	 * WWW_ROOT .'files'. DS .'optionallink';
	 * 
	 * @return string
	 */
	public static function getSavePath() {
		self::$savePath = self::getFilePath() .DS. 'optionallink';
		return self::$savePath;
	}
	
	/**
	 * オプショナルリンクの公開制限ファイルアップロード用フォルダのパスを取得する
	 * WWW_ROOT .'files'. DS .'optionallink'. DS .'limited;
	 * 
	 * @return string
	 */
	public static function getLimitedPath() {
		self::$limitedPath = self::getSavePath() .DS. 'limited';
		return self::$limitedPath;
	}
	
	/**
	 * オプショナルリンクの公開制限ファイル用のhtaccessのパスを取得する
	 * WWW_ROOT .'files'. DS .'optionallink'. DS .'limited'. DS .'.htaccess';
	 * 
	 * @return string
	 */
	public static function getLimitedHtaccess() {
		self::$limitedHtaccess = self::getLimitedPath() .DS. '.htaccess';
		return self::$limitedHtaccess;
	}
	
}

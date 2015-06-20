<?php
/**
 * OptionalLink プラグイン用
 * データベース初期化
 */
$this->Plugin->initDb('plugin', 'OptionalLink');
/**
 * 必要フォルダ初期化
 */
	$filesPath = WWW_ROOT .'files';
	$savePath = $filesPath .DS. 'optionallink';
	$limitedPath = $savePath . DS . 'limited';
	
	if (is_writable($filesPath) && !is_dir($savePath)){
		mkdir($savePath);
	}
	if (!is_writable($savePath)){
		chmod($savePath, 0777);
	}
	if (is_writable($savePath) && !is_dir($limitedPath)){
		mkdir($limitedPath);
	}
	if (!is_writable($limitedPath)){
		chmod($limitedPath, 0777);
	}
	if (is_writable($limitedPath)){
		$File = new File($limitedPath . DS . '.htaccess');
		$htaccess = "Order allow,deny\nDeny from all";
		$File->write($htaccess);
		$File->close();
	}

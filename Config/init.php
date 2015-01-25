<?php
/**
 * OptionalLink プラグイン用
 * データベース初期化
 */
$this->Plugin->initDb('plugin', 'OptionalLink');
/**
 * ブログ情報を元にデータを作成する
 *   ・設定データがないブログ用のデータのみ作成する
 * 
 */
	App::uses('BlogContent', 'Blog.Model');
	$BlogContentModel = new BlogContent();
	$blogContentDatas = $BlogContentModel->find('list', array('recursive' => -1));
	if ($blogContentDatas) {
		CakePlugin::load('OptionalLink');
		App::uses('OptionalLinkConfig', 'OptionalLink.Model');
		$OptionalLinkConfigModel = new OptionalLinkConfig();
		foreach ($blogContentDatas as $key => $blog) {
			$optionalLinkConfigData = $OptionalLinkConfigModel->findByBlogContentId($key);
			$savaData = array();
			if (!$optionalLinkConfigData) {
				$savaData['OptionalLinkConfig']['blog_content_id'] = $key;
				$savaData['OptionalLinkConfig']['status'] = 0;
				$OptionalLinkConfigModel->create($savaData);
				$OptionalLinkConfigModel->save($savaData, false);
			}
		}
	}
/**
 * ブログ記事情報を元にデータを作成する
 *   ・データがないブログ用のデータのみ作成する
 * 
 */
	App::uses('BlogPost', 'Blog.Model');
	$BlogPostModel = new BlogPost();
	$posts = $BlogPostModel->find('all', array('recursive' => -1));
	if ($posts) {
		CakePlugin::load('OptionalLink');
		App::uses('OptionalLink', 'OptionalLink.Model');
		$OptionalLinkModel = new OptionalLink();
		foreach ($posts as $key => $post) {
			$optionalLinkData = $OptionalLinkModel->findByBlogPostId($post['BlogPost']['id']);
			$savaData = array();
			if (!$optionalLinkData) {
				$savaData['OptionalLink']['blog_post_id'] = $post['BlogPost']['id'];
				$savaData['OptionalLink']['blog_content_id'] = $post['BlogPost']['blog_content_id'];
				$OptionalLinkModel->create($savaData);
				$OptionalLinkModel->save($savaData, false);
			}
		}
	}
	
/**
 * 必要フォルダ初期化
 */
	$filesPath = WWW_ROOT .'files';
	$savePath = $filesPath .DS. 'optionallink';
	$limitedPath = $savePath . DS . 'limited';
	
	if(is_writable($filesPath) && !is_dir($savePath)){
		mkdir($savePath);
	}
	if(!is_writable($savePath)){
		chmod($savePath, 0777);
	}
	if(is_writable($savePath) && !is_dir($limitedPath)){
		mkdir($limitedPath);
	}
	if(!is_writable($limitedPath)){
		chmod($limitedPath, 0777);
	}
	if(is_writable($limitedPath)){
		$File = new File($limitedPath . DS . '.htaccess');
		$htaccess = "Order allow,deny\nDeny from all";
		$File->write($htaccess);
		$File->close();
	}

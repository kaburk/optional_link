<?php
/**
 * Optional_link プラグイン用
 * データベース初期化
 */
$this->Plugin->initDb('plugin', 'Optional_link');
/**
 * ブログ情報を元にデータを作成する
 *   ・設定データがないブログ用のデータのみ作成する
 * 
 */
	$this->loadModel('Model', 'Blog.BlogContent');
	$BlogContentModel = new BlogContent();
	$blogContentDatas = $BlogContentModel->find('list', array('recursive' => -1));
	if ($blogContentDatas) {
		App::uses('Model', 'OptionalLink.OptionalLinkConfig');
		$OptionalLinkConfigModel = new OptionalLinkConfig();
		foreach ($blogContentDatas as $key => $blog) {
			$optionalLinkConfigData = $OptionalLinkConfigModel->findByBlogContentId($key);
			$savaData = array();
			if(!$optionalLinkConfigData) {
				$savaData['OptionalLinkConfig']['blog_content_id'] = $key;
				$savaData['OptionalLinkConfig']['status'] = true;
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
	$this->loadModel('Blog.BlogPost');
	$BlogPostModel = new BlogPost();
	$posts = $BlogPostModel->find('all', array('recursive' => -1));
	if ($posts) {
		App::uses('Model', 'OptionalLink.OptionalLink');
		$OptionalLinkModel = new OptionalLink();
		foreach ($posts as $key => $post) {
			$optionalLinkData = $OptionalLinkModel->findByBlogPostId($post['BlogPost']['id']);
			$savaData = array();
			if(!$optionalLinkData) {
				$savaData['OptionalLink']['blog_post_id'] = $post['BlogPost']['id'];
				$savaData['OptionalLink']['blog_content_id'] = $post['BlogPost']['blog_content_id'];
				$OptionalLinkModel->create($savaData);
				$OptionalLinkModel->save($savaData, false);
			}
		}
	}

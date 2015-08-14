# オプショナルリンク プラグイン

OptionalLink プラグインは、ブログ記事に任意のURLを設定できる入力欄を追加できるbaserCMS専用のプラグインです。

- [Summary: Wiki](https://github.com/materializing/optional_link/wiki)


## Installation

1. 圧縮ファイルを解凍後、BASERCMS/app/Plugin/OptionalLink に配置します。
2. 管理システムのプラグイン管理に入って、表示されている OptionalLink プラグイン を有効化して下さい。
3. プラグインの有効化後、システムナビの「オプショナルリンク プラグイン」の設定一覧へ移動し、利用するブログを追加し、有効化を行なってください。
4. 利用が有効なブログ記事の投稿画面にアクセスすると、入力項目が追加されてます。


## Uses Config

オプショナルリンク設定画面では、ブログ別に以下の設定を行う事ができます。
- オプショナルリンクの利用の有無を選択できます。

### ファイルの公開期間利用について

- ファイルアップロードに必要なファイルやフォルダは、インストール時は自動生成されます。
- ファイルアップロードに必要なファイルやフォルダが存在しない場合、オプショナルリンク設定画面にアラートが表示されます。
  - 管理システムにログイン状態で /admin/optional_link/optional_link_configs/init_folder にアクセスすると、ファイルの公開期間制限に必要なファイルとフォルダが生成されます。

### 留意点

- ブログ記事が大量（1,000件〜）に存在する場合、著しく動作が遅くなる可能性があります。  
その場合は、optional_links テーブル内の blog_post_id、blog_content_id にインデックスを作成すると改善する場合があります。
- 記事リンクの設定がある場合の記事詳細URLにアクセスした場合、設定URLにリダイレクトします。


## Bug reports, Discuss, Support

- Join online chat at [![Join the chat at https://gitter.im/materializing/optional_link](https://badges.gitter.im/Join%20Chat.svg)](https://gitter.im/materializing/optional_link?utm_source=badge&utm_medium=badge&utm_campaign=pr-badge&utm_content=badge)
- [Issue](https://github.com/materializing/optional_link/issues)


## Thanks

- [http://basercms.net/](http://basercms.net/)
- [http://wiki.basercms.net/](http://wiki.basercms.net/)
- [http://cakephp.jp](http://cakephp.jp)
- [Semantic Versioning 2.0.0](http://semver.org/lang/ja/)

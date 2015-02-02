アラーモ サーバーサイド  aucfan-search-api-v1 
==========

## インブラウザ等のWEBビュー表示

### ソース
iOS版、Android版でmodulesにて分けて実装しています
 + fuel/app/modules/ios/ 配下
 + fuel/app/modules/android/ 配下

### 確認用URL
iOS版、Android版のトップフィード画面
 + http://alermo.aucfan.com/ios/topfeed/home/
 + http://alermo.aucfan.com/android/topfeed/home/

iOS版、Android版のログイン画面
 + http://alermo.aucfan.com/ios/member/login
 + http://alermo.aucfan.com/android/member/login

※外部用ではドメイン「alermo.me」を使用してますが、基本的に直接アクセス出来ないよう下記ファイルにて制御。
　各modules配下で通常のブラウザからドメイン「alermo.me」にてアクセスできるページの設定の必要がある場合は下記ファイルにて設定が必要です。
 + fuel/app/config/access.php


## WebサイトのHTMLパース

詳細は下記に記載
https://dsolmine.aucfan.com/issues/3856

また現在ショップサイトを対応していくうえで、いくつか問題点が出てきており、現状の取り急ぎの対応として以下を予定
 + オプションによって価格幅がある
 　⇒ 一番高い価格を取得
 + オプションが想定以上の膨大な種類がある(310種類を確認)
 　⇒ とりあえずはオプション項目を取得しない


## 対応するショップ情報の設定

新たにショップサイトを対応する際は以下の箇所にショップ情報の設定が必要
※HTMLパースのみの実装時はソース内の設定ファイルのみでOK

### ソース内の設定
+ fuel/packages/iteminfo/config/sites.php

### DBの設定
+ haq_driveDB内の『shops』テーブル


## クリップ機能(商品登録)(旧ブックマーク機能)

### クリップ時の流れ
1. ネイティブのクリップボタン付きのインブラウザで商品ページを表示
2. クリップボタンを押す
3. ネイティブからURLをサーバーサイドAPIに飛ばす
4. URLからサイトを判別して商品ページのHTMLパースを行う
5. パースした商品データから、独自の商品オプション選択フォームを整形するHTMLソースを生成する（この時キャッシュに商品データを保存）
6. 商品オプション選択フォームを整形したHTMLソースをサーバーサイドAPIからネイティブへJSONで返す
7. ネイティブ側で受け取ったHTMLソースからフォームを生成して表示
8. フォームからオプション選択して送信
9. サーバーサイド側で受け取ってチェックしインブラウザ表示（このときキャッシュ保存した商品情報を参照して取得）
　  
    - 問題無ければ商品情報をDB登録し、成功の旨をインブラウザ内のJavascriptでJSON表示（ネイティブ側で成功画面を表示させるため）
　  - 問題が有れば、再度、商品選択フォームをインブラウザで表示させ選択させる

###クリップ時のAPI返却の確認方法
下記URLの形式でAPIを叩くと返却されます。
http://alermo.aucfan.com/ios/bookmark/api/url?_disfa=<ログインセッション情報(_disfa)>&item_detail_url=<商品ページURL>
例）http://alermo.aucfan.com/ios/bookmark/api/url?_disfa=07ca6a817cf595153dcfb9279df06c59&item_detail_url=http://zozo.jp/shop/studious/goods-sale/1800619/?did=7845094

※_disfaはログインページでログインした際のCookie情報を確認してみてください。
http://alermo.aucfan.com/ios/member/login

###クリップ成功時のデータのDB登録
haq_driveデータベース内
商品名や価格等の基本的な商品情報は「item」テーブルに。※既存する場合は重複登録しません。
商品のオプション情報は[item_options]テーブルに。※既存する場合は重複登録しません。
各ユーザー個別で保持しているクリップ情報は「bookmarks」テーブルに。
それぞれ保存されます。


## バッチ

### バッチの種類
+ 価格更新バッチ(ショップサイトのクロール・API取得)
+ 通知バッチ（メール通知・プッシュ通知）

### 実行タイミング
現在、価格更新・通知バッチ共に1日2回、下記タイミングでバッチを実行中
+ 03:00 Yahoo!ショッピング価格更新
+ 04:00 ZOZOTOWN価格更新
+ 05:00 BUYMA価格更新
+ 09:00 通知
+ 15:00 Yahoo!ショッピング価格更新
+ 16:00 ZOZOTOWN価格更新
+ 17:00 BUYMA価格更新
+ 19:00 通知
※今後商品数やショップ数が増大した場合のことを考慮して、各ショップの価格更新は個別のタイミングで実行

### 現在のcron設定例
00  09  *  *  *  env FUEL_ENV=production php /deploy/haq/oil refine notice
00  03  *  *  *  env FUEL_ENV=production php /deploy/haq/oil refine item_update yshop
00  04  *  *  *  env FUEL_ENV=production php /deploy/haq/oil refine item_update zozo
00  05  *  *  *  env FUEL_ENV=production php /deploy/haq/oil refine item_update buyma
00  19  *  *  *  env FUEL_ENV=production php /deploy/haq/oil refine notice
00  15  *  *  *  env FUEL_ENV=production php /deploy/haq/oil refine item_update yshop
00  16  *  *  *  env FUEL_ENV=production php /deploy/haq/oil refine item_update zozo
00  17  *  *  *  env FUEL_ENV=production php /deploy/haq/oil refine item_update buyma


## 価格下落の表示・通知条件に関して

### TOPフィードの表示

**セール価格、及びセール中の表示条件** 
※条件に当てはまった場合、現在の価格をセール価格として表示（優先度は上から順）
+ ショップサイトにて商品がセール中の場合
+ 現在価格が定価を下回った場合
+ 現在価格がクリップ時の最高値を下回った場合

**通常価格の表示条件**
定価が設定されていれば定価を、無ければクリップ時の最高値を表示

### MYフィードの表示
**ディスカウント表示**
登録価格よりも現在価格が下がっていた場合

### メール・プッシュ通知の条件
+ 自分のクリップ時の価格から現在価格が下がった際
+ (2回目以降の通知時)前回通知時の価格よりも更に現在価格が下がった際


## Apply serverspec into project on Windows

**Install bundle version 1.6.9**
```cmd
> gem install bundler -v 1.6.9
```

*NOTE* : Vagrant version 1.6.5 require Bundler version < 1.7.0 and >= 1.5.2

**Install serverspec**
+ add ``` gem 'serverspec' into Gemfile.
+ Run
```cmd
> bundle install --path vendor/bundle
```

**Started**
+ Open cmd and cd to vagrant folder, then run command:
```cmd
> serverspec-init
Select OS type:

  1) UN*X
  2) Windows

Select number: 1

Select a backend type:

  1) SSH
  2) Exec (local)

Select number: 1

Vagrant instance y/n: y
Auto-configure Vagrant from Vagrantfile? y/n: y
0) chefweb
1) vagrantweb
Choose a VM from the Vagrantfile: 0
 + spec/
 + spec/chefweb/
 + spec/chefweb/sample_spec.rb
 + spec/spec_helper.rb
```

+ Open spec/spec_helper.rb file

Current
```
config = Tempfile.new('', Dir.tmpdir)
`vagrant ssh-config #{host} > #{config.path}`

options = Net::SSH::Config.for(host, [config.path])
```

If you run machine with Chef, change to
```
config_path = "~/.ssh/config"
`vagrant ssh-config #{host} >> #{config_path}`
options = Net::SSH::Config.for(host, [config_path])
```

If you run machine with Knife-solo, change to
```
config_path = "~/.ssh/config"
`vagrant ssh-config #{host} >> #{config_path}`
`bundle exec knife solo prepare #{host}`
`bundle exec knife solo cook #{host}`
options = Net::SSH::Config.for(host, [config_path])
```

+ Run tests.
```cmd
> bundle exec rake spec
```

+ Successfull
```
Package "httpd"
  should be installed

Service "httpd"
  should be enabled
  should be running

Port "80"
  should be listening

Finished in 0.21091 seconds (files took 6.37 seconds to load)
4 examples, 0 failures
```

+ Fail
```
Package "httpd"
  should be installed (FAILED - 1)

Service "httpd"
  should be enabled (FAILED - 2)
  should be running (FAILED - 3)

Port "80"
  should be listening (FAILED - 4)

...
```
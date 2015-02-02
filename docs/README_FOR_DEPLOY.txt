

# #1. TOOLSの取得
========================================
cd /deploy
git clone git://root@192.168.101.11:22/var/git/TOOLS.git tools


# #2. 権限設定
========================================
cd /deploy/tools/
chmod 777 -R fuel/app/cache
chmod 777 -R fuel/app/config
chmod 777 -R fuel/app/logs
chmod 777 -R fuel/app/tmp

chmod 777 -R fuel/app/modules/*/cache
chmod 777 -R fuel/app/modules/*/logs

chmod 777 -R public/upfiles

# #3. Redis Install
========================================
yum -y install redis
chkconfig redis on
service redis start


# #4. DB設定
========================================
#deploy環境に合わせる
/deploy/tools/fuel/app/modules/selling/config/development/db.php

#oilを使う場合はデフォルト側の設定が必要なため
vim /deploy/tools/fuel/app/config/development/db.php

php composer.phar update


# #5. MySQL DB作成
========================================
#deploy環境に合わせる
#mysql db作成 user作成 アクセス権限設定
mysql> create database tools_drive;
mysql> GRANT ALL PRIVILEGES ON tools_user.* TO wp_user@'192.168.%' IDENTIFIED BY "tools";
mysql> FLUSH PRIVILEGES;

mysql tools_drive < docs/tools_drive_scheme.sql



# 備考
========================================
上記の#1 ~ #3までを実行するスクリプトsetup.shがあるので、活用すること。

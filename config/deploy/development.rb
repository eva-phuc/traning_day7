# Vagrant向けの設定
role :app, %w{vagrant@chefweb}
server 'vagrant@chefweb', user: 'vagrant', roles: %w{app}
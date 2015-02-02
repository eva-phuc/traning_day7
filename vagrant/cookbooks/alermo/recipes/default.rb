execute "drop db" do
  command "echo 'drop database if exists haq_drive' | mysql -uroot"
end

execute "create db" do
  command "echo 'create database haq_drive' | mysql -uroot"
end

execute "import db" do
  command "mysql -uroot haq_drive < /var/www/html/data/haq.sql"
end

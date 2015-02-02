# config valid only for Capistrano 3.1
lock '3.2.1'

set :application,   'alermo'
set :repo_url,      'git@github.com:aucfan/alermo.git'
set :deploy_to,     '/srv/www/alermo'
set :log_path,      '/var/log/alermo'
set :scm,           :git
set :branch,        'develop-vt'
set :format,        :pretty
set :log_level,     :debug
set :pty,           true
set :keep_releases, 5
set :use_sudo,      false

set :composer_install_flags, '--no-dev --no-interaction --quiet --optimize-autoloader'
set :composer_roles, :all
set :composer_dump_autoload_flags, '--optimize'
set :composer_download_url, "https://getcomposer.org/installer"
SSHKit.config.command_map[:composer] = "php #{shared_path.join("composer.phar")}"




namespace :deploy do

  Rake::Task["deploy:check:directories"].clear
  namespace :check do
    desc '(overwrite) Setup log directories'
    task :directories do
      on release_roles :all do
        execute :sudo, :chmod, '777', "/var/log"
        execute :sudo, :chmod, '777', "/srv/"
        execute :mkdir, '-pv', shared_path, releases_path
        execute :chown, '-R', "#{fetch(:user)}:#{fetch(:group)}", deploy_to
        execute :mkdir, '-pv', "#{fetch(:log_path)}"
        execute :chmod, '777', "#{fetch(:log_path)}"
      end
    end
  end

  desc "Symlink shared configs and folders on each release."
  task :symlink_shared do
    execute :mkdir,  '-p',  "#{shared_path}/assets"
    execute :ln, '-nfs', "#{shared_path}/assets #{release_path}/public/assets"
  end

  # run cap deploy:assets

  desc "Sync the public/assets directory."
  task :assets do
    system "rsync -vr --exclude='.DS_Store' public/assets #{user}@#{application}:#{shared_path}/"
  end
  
  desc 'Restart application'
  task :restart do
    on roles(:app), in: :sequence, wait: 5 do
    end
  end

  after :publishing, :restart

  after :restart, :clear_cache do
    on roles(:web), in: :groups, limit: 3, wait: 10 do
    end
  end

  before :starting, 'composer:install_executable'

end

after 'deploy:updated', 'deploy:symlink_shared'

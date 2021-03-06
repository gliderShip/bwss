# config valid for current version and patch releases of Capistrano
lock "~> 3.11.0"

set :application, "bwss"
set :repo_url, "git@github.com:gliderShip/bwss.git"

# Default branch is :master
 ask :branch, `git rev-parse --abbrev-ref HEAD`.chomp

# Default deploy_to directory is /var/www/my_app_name
# set :deploy_to, "/var/www/my_app_name"

# Default value for :format is :airbrussh.
# set :format, :airbrussh

# You can configure the Airbrussh format using :format_options.
# These are the defaults.
# set :format_options, command_output: true, log_file: "log/capistrano.log", color: :auto, truncate: :auto

# Default value for :pty is false
# set :pty, true

# Default value for :linked_files is []
# append :linked_files, "config/database.yml", "config/secrets.yml"

# Default value for linked_dirs is []
# append :linked_dirs, "log", "tmp/pids", "tmp/cache", "tmp/sockets", "public/system"

# Default value for default_env is {}
# set :default_env, { path: "/opt/ruby/bin:$PATH" }

# Default value for local_user is ENV['USER']
# set :local_user, -> { `git config user.name`.chomp }

# Default value for keep_releases is 5
# set :keep_releases, 5

# Uncomment the following to require manually verifying the host key before first deploy.
# set :ssh_options, verify_host_key: :secure

# Symfony

# Symfony console commands will use this environment for execution
set :symfony_env,  "prod"

# Set this to 2 for the old directory structure
set :symfony_directory_structure, 3
# Set this to 4 if using the older SensioDistributionBundle
set :sensio_distribution_version, 5

# symfony-standard edition directories
set :app_path, "app"
set :web_path, "web"
set :var_path, "var"
set :bin_path, "bin"
set :vendor_path, "vendor"

# The next 3 settings are lazily evaluated from the above values, so take care
# when modifying them
set :app_parameters_path, "app/config/parameters.yml"
set :app_config_path, "app/config"
set :log_path, "var/logs"
set :cache_path, "var/cache"

set :symfony_console_path, "bin/console"
set :symfony_console_flags, "--no-debug"

# Remove app_dev.php during deployment, other files in web/ can be specified here
# set :controllers_to_clear, ["app_*.php"]

# asset management
set :assets_install_path, "web"
set :assets_install_flags,  '--symlink'

set :linked_files, [fetch(:app_parameters_path), '.env']
set :linked_dirs, ["var/logs", "web/uploads/media"]

# Set correct permissions between releases, this is turned off by default
set :file_permissions_paths, ["var"]
set :file_permissions_users, ["deploy", "www-data"]
set :permission_method, :acl

# To make safe to deply to same server
set :tmp_dir, "/tmp/#{fetch(:application)}"

# Role filtering
set :symfony_roles, :all


# reload database with fixtures
namespace :deploy do
  desc "DROP DATABASE SCHEMA"
  task :drop_schema do
    on roles(:db) do
      symfony_console('doctrine:schema:drop', '--force')
    end
  end
end

# reload database with fixtures
namespace :deploy do
  desc "UPDATE DATABASE SCHEMA"
  task :update_schema do
    on roles(:db) do
        symfony_console('doctrine:schema:update', '--force')
    end
  end
end

# reload database with fixtures
namespace :deploy do
  desc "LOAD FIXTURES"
  task :load_fixtures do
    on roles(:db) do
       symfony_console('hautelook:fixtures:load', '--purge-with-truncate')
    end
  end
end

# append doctrine fixtures
namespace :deploy do
  desc "APPEND DOCTRINE FIXTURES"
  task :append_doctrine_fixtures do
    on roles(:db) do
#      invoke 'symfony:console', :'d:f:l --append'
        symfony_console('d:f:l', '--append')
    end
  end
end

#DROP DATABASE SCHEMA
#after 'deploy:published', 'deploy:drop_schema'

#UPDATE DATABASE SCHEMA
after 'deploy:published', 'deploy:update_schema'

#Reload Fixtures
after 'deploy:published', 'deploy:load_fixtures'


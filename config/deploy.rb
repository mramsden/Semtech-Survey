set :stages, %w(testing production)
set :default_stage, "testing"
require 'capistrano/ext/multistage'

set :application, "semtech-survey"

default_run_options[:pty] = true
set :use_sudo, false

set :domain, "iamvm-mmr2.ecs.soton.ac.uk"
set :user, "mmr"
set :port, 22
server domain, :app, :web
role :db, domain, :primary => true

set :repository,  "svn+ssh://#{user}@iamvm-mmr2.ecs.soton.ac.uk/var/svn/semtech-survey/trunk"
set :deploy_via, :export

after :deploy, 'deploy:cleanup'

namespace :maintenance do
  desc "Put the site into maintenence mode."
  task :enable, :roles => :app do
    run "cp #{release_path}/public/maintenance.htaccess #{release_path}/public/.htaccess"
  end

  desc "Take the site out of maintenence mode."
  task :disable, :roles => :app do
    run "cp #{release_path}/public/live.htaccess #{release_path}/public/.htaccess"
  end
end

namespace :deploy do
  desc "Do nothing"
  task :restart, :roles => :app do
    # PHP apps don't need the server to be restarted.
  end

  task :migrate, :roles => :app do
    # Migrate is Rails specific.
  end
end
set :stages, %w(testing production)
set :default_stage, "testing"
require 'capistrano/ext/multistage'

set :application, "semtech-survey"

default_run_options[:pty] = true
set :use_sudo, false

set :domain, "iamvm-mmr2.ecs.soton.ac.uk"
set :user, "mmr"
set :password, Capistrano::CLI.password_prompt("Type the password for #{user}@#{domain}")
set :port, 22
server domain, :app, :web
role :db, domain, :primary => true

set :repository,  "svn+ssh://iamvm-mmr2.ecs.soton.ac.uk/var/svn/semtech-survey"
set :scm_username, "mmr"
set :scm_password, Capistrano::CLI.password_prompt("Type your svn password for user #{scm_username}: ")
set :deploy_via, :export

after :deploy, 'deploy:cleanup'

desc "Put the site into maintenence mode."
maintenance.task :enable, :roles => :app do
  run "cp #{release_path}/public/maintenance.htaccess #{release_path}/public/.htaccess"
end

desc "Take the site out of maintenence mode."
maintenance.task :enable, :roles => :app do
  run "cp #{release_path}/public/live.htaccess #{release_path}/public/.htaccess"
end

desc "Do nothing"
deploy.task :restart, :roles => :app do
  # PHP apps don't need the server to be restarted.
end

deploy.task :migrate, :roles => :app do
  # Migrate is Rails specific.
end
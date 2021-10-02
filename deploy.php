<?php

namespace Deployer;

require 'recipe/laravel.php';
require 'recipe/rsync.php';

set('application', 'My App');
set('ssh_multiplexing', true);

set('rsync_src', function () {
  return __DIR__;
});


add('rsync', [
  'exclude' => [
    '.git',
    '/.env',
    '/storage/',
    '/vendor/',
    '/node_modules/',
    '.github',
    'deploy.php',
  ],
]);

task('deploy:secrets', function () {
  file_put_contents(__DIR__ . '/.env', getenv('DOT_ENV'));
  upload('.env', get('deploy_path') . '/shared');
});

host('18.195.115.161')
  ->hostname('ip-172-26-15-235')
  ->stage('production')
  ->user('bitnami')
  ->set('deploy_path', '~/htdocs')
  ->set('sshOptions', [
  'StrictHostKeyChecking' => 'no',]);


after('deploy:failed', 'deploy:unlock');

desc('Deploy the application');

task('deploy', [
  'deploy:info',
  'deploy:prepare',
  'deploy:lock',
  'deploy:release',
  'rsync',
  'deploy:secrets',
  'deploy:shared',
  'deploy:vendors',
  'deploy:writable',
  'artisan:storage:link',
  'artisan:view:cache',
  'artisan:config:cache',
  'artisan:migrate',
  'artisan:queue:restart',
  'deploy:symlink',
  'deploy:unlock',
  'cleanup',
]);

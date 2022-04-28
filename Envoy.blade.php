@servers(['localhost' => '127.0.0.1'])

@setup
    $now = new DateTime;
@endsetup

@story('deploy')
    update-code
    install-dependencies
    run-migrations
@endstory
 
@task('update-code')
    cd /home/garrett/pcm_infra/laravel
    git pull
@endtask
 
@task('install-dependencies')
    cd /home/garrett/pcm_infra/laravel
    composer install
@endtask

@task('run-migrations')
    cd /home/garrett/pcm_infra/laravel/vendor/bin
    sail artisan migrate
@endtask
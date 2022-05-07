@servers(['localhost' => '127.0.0.1'])

@setup
    $now = new DateTime;
@endsetup

@story('deploy')
    update-code
    install-dependencies
    compile
    run-migrations
@endstory
 
@task('update-code')
    cd /home/garrett/pcm_infra/laravel
    git pull
@endtask
 
@task('install-dependencies')
    cd /home/garrett/pcm_infra/laravel
    composer install
    npm install
    cd /home/garrett/pcm_infra/laravel/frontend
    npm install
@endtask

@task('compile')
    cd /home/garrett/pcm_infra/laravel
    npm run prod
@endtask

@task('run-migrations')
    cd /home/garrett/pcm_infra/laravel/vendor/bin
    sail artisan migrate
@endtask
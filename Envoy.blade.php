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
    cd /home/garrett/pcm_selfhosted/app
    git pull
@endtask
 
@task('install-dependencies')
    cd /home/garrett/pcm_selfhosted/app
    composer install
    npm install
    cd /home/garrett/pcm_selfhosted/app/frontend
    npm install
@endtask

@task('compile')
    cd /home/garrett/pcm_selfhosted/app
    npm run prod
@endtask

@task('run-migrations')
    cd /home/garrett/pcm_selfhosted/app/vendor/bin
    sail artisan migrate
@endtask

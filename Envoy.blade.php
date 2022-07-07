@servers(['localhost' => '127.0.0.1'])

@setup
    $now = new DateTime;
@endsetup

@story('deploy')
    update-code
    install-dependencies
    compile
    run-migrations
    run-seeder
@endstory
 
@task('update-code')
    cd /home/pcmuser/pcm_selfhosted/app
    git pull
@endtask
 
@task('install-dependencies')
    cd /home/pcmuser/pcm_selfhosted/app
    composer install
    npm install
    cd /home/pcmuser/pcm_selfhosted/app/frontend
    npm install
@endtask

@task('compile')
    cd /home/pcmuser/pcm_selfhosted/app
    npm run prod
@endtask

@task('run-migrations')
    cd /home/pcmuser/pcm_selfhosted/app/vendor/bin
    sudo sail artisan migrate
@endtask

@task('run-seeder')
    cd /home/pcmuser/pcm_selfhosted/app/vendor/bin
    sudo sail artisan db:seed
@endtask
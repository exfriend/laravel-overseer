# Laravel Overseer

Allows to control artisan commands from Web/API.

### Overview

This package is designed primarily to control various background tasks from
admin dashboard of your application.

Once installed, Laravel Overseer will provide a flexible JSON API to 
check, start, stop, monitor your commands in real-time.

When used with [**overseer-bootstrap**](https://github.com/exfriend/overseer-bootstrap) it will
also give you a fully functional dashboard pages written in VueJS with Twitter Bootstrap 3.

If you use a different frontend or want to customize any part of it, you can publish the assets and/or
use them as a reference to write your own frontend on top of Overseer's API.

All you have to do is extend your Artisan command from `Exfriend\Overseer\Command`
 instead of `Illuminate\Console\Command`.
 
## Installation
 
 ` composer  require exfriend/laravel-overseer `
  
  or 
 
 ` composer require exfriend/overseer-bootstrap ` 
 
 for bootstrap version with frontend.
 
 Then, add the package's service provider to your config/app.php:
 
 ```
 // ...
 Exfriend\Overseer\OverseerServiceProvider::class,
 Exfriend\OverseerBootstrap\OverseerBootstrapServiceProvider::class, // <- only for overseer-bootstrap
```
 
 Make a directory to store the logs:
 
```
cd /path/to/your/project
mkdir ./storage/logs/tasks
chmod -r 666 ./storage/logs/tasks
```
 
## Example command
 
 Now you are ready to create your first Overseer Command. 
 Basically it's very much like Laravel's console command but there 
 are couple of differences.
 
 ```
 <?php
 
 namespace App\Robots\HackerNews;
 
 class Command extends \Exfriend\Robots\Console\Command
 {
 
     /**
      * The name and signature of the console command.
      *
      * @var string
      */
     protected $signature = 'scrape:hackernews';
 
     /**
      * The console command description.
      *
      * @var string
      */
     protected $description = 'Gets the latest 10 news titles from hackernews';
     
     // this is new thing
     protected $title = 'HackerNews Scraper';
 
     /**
      * Execute the console command.
      *
      * @return mixed
      */
     public function handle()
     {
         $this->line( 'Beginning scrape' );
         for ( $i = 1; $i < 10; $i++ )
         {
             sleep(2);
             
             /**
              place this where you need to check 
              if there is a pending "stop" command from API
              to terminate properly
             */
             $this->checkpoint();
             
             // you can set progress% 0..100
             $this->setProgress( $i*10 );
             $this->line( 'Scraping news #'.$i.' of 10');
         }
         $this->line( 'Bye!' );
     }
 
 }

 ```
 
 The command above describes how your real commands should look like.
 
## Using the API 
 
GET http://your_project/overseer/api/commands
```
{
    status: "ok",
    data: [
    {
        command: "App\Robots\Rozetka\Command",
        running: false,
        title: "Rozetka Scraper",
        progress: 0,
        last_run: "30.08.2016 23:33:19",
        description: ""
    }
    ]
}
```
 
 
GET http://your_project/overseer/api/command?command=App\Robots\Rozetka\Command
```
{
status: "ok",
data: {
        command: "App\Robots\Rozetka\Command",
        running: false,
        title: "Rozetka Scraper",
        progress: 0,
        last_run: "30.08.2016 23:33:19",
        description: "",
        short_log: "",
        logs: [
        "scrape:rozetka__2016_08_30_23_33_19.log",
        "scrape:rozetka__2016_08_26_08_20_48.log",
        "scrape:rozetka__2016_08_26_07_24_49.log",
        "scrape:rozetka__2016_08_26_03_25_14.log"
        ]
    }
}
```
 
GET http://your_project/overseer/api/current_log?command=App\Robots\Rozetka\Command
```
{
status: "ok",
data: {
    "[2016-08-31 00:45:26] local.INFO: Beginning scrape [] [] ",
    "[2016-08-31 00:45:26] local.INFO: Scraping category 1 [] [] ",
    "[2016-08-31 00:45:27] local.INFO: Parsing product 1 [] [] ",
    "[2016-08-31 00:45:29] local.INFO: Parsing product 2 [] [] ",
    "[2016-08-31 00:45:31] local.INFO: Parsing product 3 [] [] ",
    }
}
```
 
GET http://your_project/overseer/api/run?command=App\Robots\Rozetka\Command
```
{
status: "ok",
}
```
 
 
GET http://your_project/overseer/api/stop?command=App\Robots\Rozetka\Command
```
{
status: "ok",
}
```
 
GET http://your_project/overseer/api/unlock?command=App\Robots\Rozetka\Command
```
{
status: "ok",
}
```
 
### Mutex locks

Overseer uses mechanism called Mutual Exclusion to prevent your task overlapping.
Sometimes it is useful to use "unlock" command to delete the mutex file. 
This can be used e.g. if your task died with uncaught exception and was unable to delete the lock file properly.
In this case your system will consider this task as running until you delete the lock file manually or by using unlock api method.

### Console improvements

Overseers adds 2 options to every command you run from console:
```
--force (-f) - ignore mutex lock
--unlock (-u) - delete the mutex lock
 ```
 
### GUI

If you're looking for a webpage-based user interface for this package, make sure to check out [**overseer-bootstrap**](https://github.com/exfriend/overseer-bootstrap).

## Contributing

This package is work-in-progress. Pull requests are welcome. The is so much work ahead!


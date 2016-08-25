<?php

Route::any( 'overseer/api/commands', [
    'as' => 'overseer.api.commands',
    'uses' => 'Exfriend\Overseer\OverseerController@commands',
] );

Route::any( 'overseer/api/run', [
    'as' => 'overseer.api.run',
    'uses' => 'Exfriend\Overseer\OverseerController@run',
] );

Route::any( 'overseer/api/stop', [
    'as' => 'overseer.api.stop',
    'uses' => 'Exfriend\Overseer\OverseerController@stop',
] );

Route::any( 'overseer/api/logs', [
    'as' => 'overseer.api.logs',
    'uses' => 'Exfriend\Overseer\OverseerController@logs',
] );

Route::any( 'overseer/api/unlock', [
    'as' => 'overseer.api.unlock',
    'uses' => 'Exfriend\Overseer\OverseerController@unlock',
] );

Route::any( 'overseer/api/current_log', [
    'as' => 'overseer.api.current_log',
    'uses' => 'Exfriend\Overseer\OverseerController@current_log',
] );

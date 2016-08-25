<?php

namespace Exfriend\Overseer;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class OverseerController extends Controller
{
    public $commands;

    public function __construct( Manager $manager )
    {
        $this->commands = $manager->getAllRegisteredCommands();
    }

    public function commands()
    {
        $coms = [];
        foreach ( $this->commands as $command )
        {
            $coms[] = [
                'command' => $command,
                'running' => ( new CommandManager( $command ) )->is_running(),
            ];

        }
        return $this->respondOkWithData( $coms );
    }

    public function run( Request $request )
    {
        $manager = new CommandManager( $request->command );
        //        \Session::flush();
        $manager->run();
        return $this->respondOk();
    }

    public function unlock( Request $request )
    {
        $manager = new CommandManager( $request->command );
        $manager->unlock();
        return $this->respondOk();
    }

    public function stop( Request $request )
    {
        $manager = new CommandManager( $request->command );
        $manager->stop();
        return $this->respondOk();
    }

    public function logs( Request $request )
    {
        $manager = new CommandManager( $request->command );
        return $this->respondOkWithData( $manager->getLogFilenames( 1 ) );
    }

    public function current_log( Request $request )
    {
        $manager = new CommandManager( $request->command );
        return $this->respondOkWithData( $manager->getCurrentLog( 0 ) );
    }

    protected function respondOkWithData( $data )
    {
        return [ 'status' => 'ok', 'data' => $data ];
    }

    protected function respondOk()
    {
        return [ 'status' => 'ok' ];
    }
}

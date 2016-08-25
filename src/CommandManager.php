<?php
namespace Exfriend\Overseer;

use Carbon\Carbon;

class CommandManager
{
    use RunsCommandsInBackground;
    /**
     * @var Command
     */
    protected $command;

    public function command()
    {
        return $this->command;
    }

    public function __construct( $command )
    {
        if ( !$command instanceof Command )
        {
            $manager = new Manager();
            $cmds = $manager->getAllRegisteredCommands();
            if ( !in_array( $command, $cmds ) )
            {
                return false;
            }

            $command = new $command();
        }
        $this->command = $command;
        return $this;
    }

    public function getProgress()
    {
        if ( $this->command->mutex->exists() )
        {
            return $this->command->mutex->read();
        }
        return 0;
    }

    public function getLastRun()
    {
        $logs = $this->getLogFilenames( true );
        if ( count( $logs ) )
        {
            $l = $logs[ 0 ];
            $l = explode( '__', $l )[ 1 ];
            $l = str_replace( '.log', '', $l );
            $time = Carbon::createFromFormat( 'Y_m_d_H_i_s', $l )->format( 'd.m.Y H:i:s' );
            return $time;
        }
        return 0;
    }

    public function run()
    {
        return $this->callBackground( $this->command->getName() );
    }

    public function unlock()
    {
        if ( $this->command->mutex->exists() )
        {
            return $this->command->mutex->unlock();
        }
        return false;
    }

    public function stop()
    {
        return file_put_contents( storage_path( 'framework/stop-' . sha1( $this->command->getName() ) ), 1 );
    }

    public function is_locked()
    {
        return $this->command->mutex->exists();
    }

    public function is_running()
    {
        return $this->command->mutex->exists();
    }

    public function getLogFilenames( $base = false )
    {
        $names = $this->command->getLogFilenames();
        if ( $base )
        {
            $names = array_map( 'basename', $names );
        }

        return $names;
    }

    public function getCurrentLog( $short = false )
    {
        if ( !$this->is_running() )
        {
            return '';
        }
        $files = $this->getLogFilenames();
        if ( !count( $files ) )
        {
            return '';
        }

        $file = $files[ 0 ];

        if ( !$short )
        {
            $lines = file( $file );
            return $lines;
        }

        $lines = [];
        $fp = fopen( $file, "r" );
        while ( !feof( $fp ) )
        {
            $line = fgets( $fp, 4096 );
            if(!$line){
                break;
            }
            array_push( $lines, $line );
            if ( count( $lines ) > 30 )
            {
                array_shift( $lines );
            }
        }
        fclose( $fp );

        return $lines;
    }
}
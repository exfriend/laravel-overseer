<?php

namespace Exfriend\Overseer;


use Carbon\Carbon;
use Monolog\Handler\StreamHandler;
use Symfony\Bridge\Monolog\Handler\ConsoleHandler;

trait ProducesLogFiles
{
    protected function clearOldLogs()
    {
        $files = $this->getLogFilenames();

        $oldLogs = array_slice( $files, 3 );
        foreach ( $oldLogs as $oldLog )
        {
            unlink( $oldLog );
        }
    }

    public function getLogFilenames()
    {
        $files = glob( $this->getLogFolder() . '/' . $this->getName() . '__*.log' );
        usort( $files, function ( $a, $b )
        {
            return filemtime( $a ) < filemtime( $b );
        } );

        return $files;

    }

    protected function getLogFilename()
    {
        $path = $this->getLogFolder() . '/' . $this->getName() . '__' . Carbon::now()->format( 'Y_m_d_H_i_s' ) . '.log';
        return $path;
    }

    protected function getLogFolder()
    {
        $path = storage_path( 'logs/tasks' );
        if ( !file_exists( $path ) )
        {
            mkdir( $path, 0777, true );
        }
        return $path;
    }

    protected function prepareLogger()
    {
        $this->clearOldLogs();
        $this->logger->getMonolog()->pushHandler( new StreamHandler( $this->getLogFilename() ) );
        $this->logger->getMonolog()->pushHandler( new ConsoleHandler( $this->getOutput() ) );
    }

}
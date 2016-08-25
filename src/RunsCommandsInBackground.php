<?php

namespace Exfriend\Overseer;

use Symfony\Component\Process\Process;
use Symfony\Component\Process\ProcessUtils;

trait RunsCommandsInBackground
{

    public $user;

    public function callBackground( $command )
    {
        $cmd = $this->buildCommand( $command );
        $p = ( new Process(
            $cmd, base_path(), null, null, null
        ) );
        $p->run();
        return true;

    }

    public function buildCommand( $command )
    {
        $output = ProcessUtils::escapeArgument( $this->getDefaultOutput() );

        $redirect = ' > ';

        $command = PHP_BINARY . ' ' . base_path() . '/artisan ' . $command . $redirect . $output . ' 2>&1 &';
        //        $command = 'bash -c '.base_path() . '/run.sh ' . PHP_BINARY . base_path().'/artisan ' . $command . $redirect . $output . ' 2>&1 &';
        //        xd( $command );


        return $this->user && !windows_os() ? 'sudo -u ' . $this->user . ' -- sh -c \'' . $command . '\'' : $command;
    }


    protected function getDefaultOutput()
    {
        return ( DIRECTORY_SEPARATOR == '\\' ) ? 'NUL' : '/dev/null';
    }


}
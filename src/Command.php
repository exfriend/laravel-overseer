<?php

namespace Exfriend\Overseer;

use Carbon\Carbon;
use Exfriend\ArtisanMutex\Mutex;
use Exfriend\ArtisanMutex\PreventRealOverlapping;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

abstract class Command extends \Illuminate\Console\Command
{
    use PreventRealOverlapping, ProducesLogFiles;

    public $logger;
    protected $title;
    /**
     * @var Mutex
     */
    public $mutex;

    public function getTitle()
    {
        return $this->title;
    }

    public function setProgress( $percent )
    {
        return $this->mutex->write( $percent );
    }

    public function __construct()
    {
        $this->logger = app( 'log' );
        $this->mutex = new Mutex( $this );

        return parent::__construct();
    }

    protected function checkpoint()
    {
        $f = storage_path( 'framework/stop-' . sha1( $this->getName() ) );
        if ( file_exists( $f ) )
        {
            unlink( $f );
            $this->mutex->unlock();
            $this->info( 'Terminated by CommandManager' );
            die();
        }
    }

    protected function execute( InputInterface $input, OutputInterface $output )
    {

        $this->mutex = new Mutex( $this );

        if ( !$this->checkMutex() )
        {
            return false;
        }
        $this->mutex->lock();
        $f = storage_path( 'framework/stop-' . sha1( $this->getName() ) );
        if ( file_exists( $f ) )
        {
            unlink( $f );
        }

        $this->prepareLogger();

        $exec = false;
        try
        {
            $exec = parent::execute( $input, $output );
        }
        catch ( \Exception $e )
        {
            $this->logger->critical( (string)$e );
            $this->logger->info( 'Bye' );
        }
        $this->mutex->unlock();

        return $exec;

    }

    public function line( $string, $style = null, $verbosity = null )
    {
        $styled = $style ? "<$style>$string</$style>" : $string;
        $time = Carbon::now()->format( 'd.m H:i:s' );

        $this->logger->info( $string );
        $this->output->writeln( '[' . $time . '] ' . $styled, $this->parseVerbosity( $verbosity ) );
    }

}

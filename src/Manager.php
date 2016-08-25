<?php

namespace Exfriend\Overseer;


class Manager
{

    public function getAllRegisteredCommands()
    {
        $children = [ ];

        $all = \Artisan::all();

        foreach ( $all as $class )
        {
            $class = get_class($class);
            if ( is_subclass_of( $class, Command::class ) )
            {
                $children[] = $class;
            }
        }
        return $children;
    }

}
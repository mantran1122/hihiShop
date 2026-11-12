<?php

namespace App\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class AssignDefaultRole
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     * 
     * @param  \Illuminate\Auth\Events\Registered  $event
     * @return void
     */
    
    public function handle(object $event): void
    {
        //
        $event->user->assignRole('Customer');
    }
}

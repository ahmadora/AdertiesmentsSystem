<?php

namespace App\Events;

use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class AdvertisementRemoved implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    private $screen_id;
    public $advertisement;

    /**
     * Create a new event instance.
     *
     * @param $screen_id
     * @param $advertisement
     */
    public function __construct($screen_id, $advertisement)
    {
        $this->screen_id = $screen_id;
        $this->advertisement = $advertisement;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('device.'.$this->screen_id);
    }
}


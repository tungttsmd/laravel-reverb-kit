<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MessageSent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;
    private $message;
    private $chanel;
    private $event;

    /**
     * Create a new event instance.
     */
    public function __construct($message)
    {
        $this->message = $message;
        $this->chanel = 'reverb-websocket-chanel';
        $this->event = 'reverb-websocket-event';
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new Channel($this->chanel),
        ];
    }
    public function broadcastAs(): string
    {
        return $this->event;
    }

    public function broadcastWith(): array
    {
        return [
            'message' => $this->message,
        ];
    }
}

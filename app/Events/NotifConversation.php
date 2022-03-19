<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NotifConversation implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $id_conversation;
    public $data;
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($id_conversation,$data)
    {
        $this->id_conversation = $id_conversation;
        $this->data = $data;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('Conversation-'.$this->id_conversation);
    }

    public function broadcastAs()
    {
        return 'newConversation';
    }

    public function broadcastWith()
    {
        return ['data' => $this->data];
    }
}

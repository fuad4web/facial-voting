<?php

namespace App\Events;

use App\Models\Vote;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class VoteCast implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $vote;

    /**
     * Create a new event instance.
     */
    public function __construct(Vote $vote)
    {
        $this->vote = $vote;
    }

    /**
     * Get the channels the event should broadcast on.
     */
    public function broadcastOn()
    {
        // Broadcast on a channel per category for targeted updates
        return new Channel('category.' . $this->vote->category_id);
    }

    /**
     * The event's broadcast name.
     */
    public function broadcastAs()
    {
        return 'vote.cast';
    }

    /**
     * Data to broadcast.
     */
    public function broadcastWith()
    {
        // Send only necessary data (don't expose user info)
        return [
            'candidate_id' => $this->vote->candidate_id,
            'category_id' => $this->vote->category_id,
            'voted_at' => $this->vote->voted_at->toISOString(),
        ];
    }
}

<?php

namespace App\Events;
use App\Models\Attachment;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;

class AttachmentReady implements ShouldBroadcastNow
{
    public function __construct(public Attachment $att){}
    public function broadcastOn() { return new PrivateChannel('user.' . $this->att->user_id); }
    public function broadcastWith() { return ['id'=>$this->att->id, 'url'=>$this->att->url]; }
}

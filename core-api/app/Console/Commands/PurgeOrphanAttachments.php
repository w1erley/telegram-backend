<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Attachment;
use Illuminate\Support\Facades\Storage;

class PurgeOrphanAttachments extends Command
{
    protected $signature='attachments:purge-orphans';
    protected $description='Delete stale init attachments';

    public function handle()
    {
        $old=Attachment::where('status','init')
            ->where('created_at','<',now()->subDay())->get();
        foreach($old as $a){
            if($a->path) Storage::disk('s3')->delete($a->path);
            $a->delete();
        }
        $this->info("Purged {$old->count()} attachments");
    }
}

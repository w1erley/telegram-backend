<?php

namespace App\Http\Controllers;

use App\Http\Requests\Attachment\InitRequest;
use App\Http\Requests\Attachment\CompleteRequest;
use App\Services\Web\AttachmentService;
use Illuminate\Support\Facades\Log;

class AttachmentController extends Controller
{
    public function __construct(private AttachmentService $svc) {}

    public function init(InitRequest $r)
    {
        [$att, $url] = $this->svc->init([
            ...$r->validated(),
            'user_id' => auth()->id(),
        ]);
        return response()->json(['attachment' => $att, 'uploadUrl' => $url]);
    }

//    public function complete(CompleteRequest $r)
//    {
//        $this->svc->complete($r->uploadKey, ['mime'=>$r->mime]);
//        return ['ok'=>true];
//    }
}

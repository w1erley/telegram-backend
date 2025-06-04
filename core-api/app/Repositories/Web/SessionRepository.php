<?php

namespace App\Repositories\Web;

use App\Models\Session;
use App\Repositories\BaseRepository;
class SessionRepository extends BaseRepository
{
    public function __construct(Session $session)
    {
        parent::__construct(
            $session,
            'session'
        );
    }
}

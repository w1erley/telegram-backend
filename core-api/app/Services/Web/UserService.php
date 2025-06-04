<?php

namespace App\Services\Web;

use App\Repositories\Web\UserRepository;
use App\Services\BaseService;

class UserService extends BaseService
{
    public function __construct(
        private readonly UserRepository $userRepository
    )
    {
        parent::__construct($userRepository);
    }
}

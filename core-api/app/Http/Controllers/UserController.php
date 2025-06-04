<?php

namespace App\Http\Controllers;

use App\Services\Web\UserService;

class UserController extends Controller
{
    public function __construct(private UserService $svc) {}
}

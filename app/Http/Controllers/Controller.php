<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

/**
 * @class Controller
 *
 * @Brief Controller base
 */
class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;
}

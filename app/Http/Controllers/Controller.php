<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    /**
     * @OA\Info(title="TaskApi", version="0.1")
     */

    /**
     * @OA\Get(
     *     path="/projects",
     *     @OA\Response(response="200", description="Documentation of a crud task api.")
     * )
     */

    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
}

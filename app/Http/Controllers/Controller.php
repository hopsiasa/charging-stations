<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use OpenApi\Annotations as OA;

/**
 * @OA\OpenApi(
 *     @OA\Info(
 *         version="1.0",
 *         title="Charging Stations API Overview",
 *         description="Charging Stations API documentation",
 *     )
 * )
 */
class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;
}

<?php

namespace App\Controllers;

/**
 * Landing Page Controller
 *
 * Route to add in app/Config/Routes.php:
 *   $routes->get('/', 'LandingController::index');
 *   $routes->get('/landing', 'LandingController::index');
 */
class LandingController extends BaseController
{
    public function index()
    {
        return view('landing/index');
    }
}

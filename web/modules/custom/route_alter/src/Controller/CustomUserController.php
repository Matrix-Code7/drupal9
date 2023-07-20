<?php

namespace Drupal\route_alter\Controller;

use Drupal\user\Controller\UserAuthenticationController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Returns responses for Route Alter routes.
 */
class CustomUserController extends  UserAuthenticationController {

    public function customLogin(Request $request){
        dd('adasasdasada');
        parent::login($request);
    }

}

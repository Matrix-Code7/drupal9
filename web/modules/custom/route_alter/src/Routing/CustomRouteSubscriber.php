<?php

namespace Drupal\route_alter\Routing;

use Drupal\Core\Routing\RouteSubscriberBase;


class CustomRouteSubscriber extends RouteSubscriberBase{


    /**
     * {@inheritDoc}
     */
    protected function alterRoutes(\Symfony\Component\Routing\RouteCollection $routeCollection){
        $deprecatedModuleConfirmationRoute = $routeCollection->get('system.modules_list_non_stable_confirm');
        $deprecatedModuleConfirmationRoute->setDefault('_form', '\Drupal\route_alter\Form\CustomForm');

        $userloginAPIRoute = $routeCollection->get('user.login.http');
        $userloginAPIRoute->setDefault('_controller', '\Drupal\route_alter\Controller\CustomUserController::customLogin');
    }

}
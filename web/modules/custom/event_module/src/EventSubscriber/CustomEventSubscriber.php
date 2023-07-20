<?php

namespace Drupal\event_module\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Drupal\event_module\Event\UserLoginEvent;

class CustomEventSubscriber implements EventSubscriberInterface {
  /**
   * {@inheritdoc}
   *
   * @return array
   */

   public static function getSubscribedEvents()
   {
    return [
      UserLoginEvent::SUBMIT => 'configSubmit',
      UserLoginEvent::VALIDATE => 'configValidate'
    ];
   }

   public function configSubmit(UserLoginEvent $event){
    $msg = $event->getMessage();
    \Drupal::messenger()->addMessage($msg);
   }

   public function configValidate(UserLoginEvent $event){
    $msg = $event->getMessage();
    \Drupal::messenger()->addError($msg);
   }
}

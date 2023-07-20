<?php


namespace Drupal\event_module\Event;

use Symfony\Contracts\EventDispatcher\Event;

class UserLoginEvent extends Event {
 const SUBMIT = 'event.submit';
 const VALIDATE = 'event.validate';
 protected $msg;

 public function __construct($msg)
 {
   $this->msg = $msg;
 }

 public function getMessage() {
  return $this->msg;
 }

 public function myEventDescription() {
  return 'this is custom event on user submit';
 }


}

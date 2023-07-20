<?php

namespace Drupal\event_module\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\event_module\Event\UserLoginEvent;

class UserForm extends FormBase{

  /**
   * {@inheritdoc}
   */

   public function getFormId()
   {
    return 'custom_form';
   }

   /**
    * {@inheritdoc}
    */

    public function buildForm(array $form, FormStateInterface $form_state)
    {
      $form['info'] = [
        '#markup' => 'Fill the Form'
      ];

      $form['name'] = [
        '#type' => 'textfield',
        '#title' => $this->t('Your Name')
      ];

      $form['age'] = [
        '#type' => 'number',
        '#title' => $this->t('Your Age')
      ];

      $form['actions']['#type'] = 'actions';
      $form['actions']['submit'] = [
        '#type' => 'submit',
        '#value' => $this->t('SUBMIT')
      ];

      return $form;
    }

    /**
     * {@inheritdoc}
     */

     public function submitForm(array &$form, FormStateInterface $form_state)
     {

       # Load the dispatcher from services by creation dispatcher object

       $dispatcher = \Drupal::service('event_dispatcher');
       $error = false;

      if($form_state->getValue('name') == ''){
        $error = true;
        $event = new UserLoginEvent('Please Enter the name');
        $dispatcher->dispatch(UserLoginEvent::VALIDATE, $event);
      }

      if($form_state->getValue('age') == ''){
        $error = true;
        $event = new UserLoginEvent('Please Enter the age');
        $dispatcher->dispatch(UserLoginEvent::VALIDATE, $event);
      }

      if(!$error){
      # creating object of your event class
        $event = new UserLoginEvent('Your Form Is Submitted');
        $dispatcher->dispatch(UserLoginEvent::SUBMIT, $event);
      }
     }

}

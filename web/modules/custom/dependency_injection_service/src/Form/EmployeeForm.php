<?php

/**
 * @file
 * Contains \Drupal\EmployeeForm\Form\EmployeeForm.
 */
namespace Drupal\dependency_injection_service\Form;

use Drupal\node\NodeInterface;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\dependency_injection_service\services\DatabaseService;
use Symfony\Component\DependencyInjection\ContainerInterface;

class EmployeeForm extends FormBase
{

    protected $db;
    protected $node;

    public function __construct(DatabaseService $db)
    {
        // $this->db = \Drupal::service('dependency_injection_service.custom_service');
        $this->db = $db;
    }


    public static function create(ContainerInterface $container){
        return new Static(
            $container->get('dependency_injection_service.custom_service')
        );
    }

    public function buildForm(array $form, FormStateInterface $form_state, NodeInterface $node = NULL)
    {
        $form['email'] = [
            '#type' => 'textfield',
            '#title' => $this->t('Email'),
            '#description' => $this->t('Your email address'),
            '#required' => TRUE,
        ];

        $form['name'] = [
            '#type' => 'textfield',
            '#title' => $this->t('Name'),
            '#description' => $this->t('Your name'),
            '#required' => TRUE
        ];

        $form['actions']['submit'] = [
            '#type' => 'submit',
            '#value' => $this->t('Send'),
        ];

        return $form;
    }

    public function getFormId()
    {
        return 'employee_form';
    }

    public function validateForm(array &$form, FormStateInterface $form_state)
    {
        $name = $form_state->getValue('name');
        $email = $form_state->getValue('email');
        if (empty($name)) {
            $form_state->setErrorByName('name', 'Name field is empty');
        }
        if (empty($email)) {
            $form_state->setErrorByName('email', 'Email field is empty');
        }
        return;
    }

    public function submitForm(array &$form, FormStateInterface $form_state)
    {
        $this->db->insertData($form_state);
        \Drupal::messenger()->addMessage('Custom Service has successfully inserted your data!');
    }

}

    
?>
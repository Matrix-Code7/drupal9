<?php


namespace Drupal\dependency_injection_service\services;
use Drupal\Core\Database\Connection;

class DatabaseService
{

    protected $database;

    public function __construct(Connection $database)
    {
        $this->database = $database;
    }

    /**
     * insertData service
     */
    public function insertData($form_state)
    {
        $query = $this->database->insert('employee')
            ->fields([
                'name' => $form_state->getValue('name'),
                'email' => $form_state->getValue('email'),
                'created' => time(),
            ]);
        $query->execute();
    }


    /**
     * getData service
     */
    public function getData()
    {
        $result = $this->database->select('employee', 't')
            ->fields('t')
            ->execute()
            ->fetchAll();
        return $result;
    }
}

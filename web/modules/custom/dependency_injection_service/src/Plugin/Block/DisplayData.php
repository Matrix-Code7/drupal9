<?php

namespace Drupal\dependency_injection_service\Plugin\Block;

use Drupal\Core\Block\BlockBase;

/**
 * Provides a display employee block.
 *
 * @Block(
 *   id = "display_employee",
 *   admin_label = @Translation("Display Employee"),
 *   category = @Translation("Custom")
 * )
 */
class DisplayData extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
    $employees = \Drupal::service('dependency_injection_service.custom_service')->getData();
   
    return [
      '#theme' => 'employee_template',
      '#employees' => $employees
    ];
  
  }

}

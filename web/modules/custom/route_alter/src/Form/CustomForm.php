<?php

namespace Drupal\route_alter\Form;

use Drupal\Core\Extension\ExtensionLifecycle;
use Drupal\system\Form\ModulesListNonStableConfirmForm;

/**
 * Builds a confirmation form for enabling experimental and deprecated modules.
 *
 * @internal
 */
class CustomForm extends ModulesListNonStableConfirmForm {

  /**
   * {@inheritdoc}
   */
  public function getQuestion() {

    parent::getQuestion();

    $hasDeprecatedModulesToEnable = !empty($this->groupedModuleInfo[ExtensionLifecycle::DEPRECATED]);

    if ($hasDeprecatedModulesToEnable) {
      return $this->formatPlural(
        count($this->groupedModuleInfo[ExtensionLifecycle::DEPRECATED]),
        'Kya ap sach me ek deprecate module Bhari nuksan ka samna krte hye bharna chahte hai?',
        'Kya ap sach me ek deprecate modules Bhari nuksan ka samna krte hye bharna chahte hai?'
      );
    }
  }

}

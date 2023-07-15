<?php

namespace Drupal\secondary_menu\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Language\LanguageManagerInterface;
use Drupal\Core\Url;

/**
 * Provides an example block.
 *
 * @Block(
 *   id = "secondary_menu",
*   admin_label = @Translation("Secondary Menu"),
 *   category = @Translation("Custom")
 * )
 */
class SecondaryMenu extends BlockBase implements ContainerFactoryPluginInterface
{

  private $languageManager;

  public function __construct(array $configuration, $plugin_id, $plugin_definition, LanguageManagerInterface $languageManager) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->languageManager = $languageManager;
  }

  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('language_manager')
    );
  }

  public function getCacheMaxAge() {
    return 0;
  }

  /**
   * {@inheritdoc}
   */
  public function build()
  {
    $values['telephone'] = theme_get_setting('telephone');
    $values['mail'] = theme_get_setting('mail');

    $languages = $this->languageManager->getNativeLanguages();
    $langs = [];

    foreach ($languages as $languageId => $language) {
      $languageName = $language->getName();
      $languageUrl = Url::fromRoute('<current>', [], ['language' => $language])->toString();
      $languageIcons = '/modules/contrib/languageicons/flags/'.$languageId.'.png';
      $langs[] = ['name' => $languageName, 'url' => $languageUrl, 'icon_src' => $languageIcons];
    }

    $build['content'] = [
      '#theme' => 'secondary_menu_template',
      '#data' => $values,
      '#languages' => $langs
    ];

    return $build;
  }
}




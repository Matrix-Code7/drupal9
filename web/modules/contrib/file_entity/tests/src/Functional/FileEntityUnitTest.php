<?php

namespace Drupal\Tests\file_entity\Functional;

use Drupal\file\Entity\File;

/**
 * Test basic file entity functionality.
 *
 * @todo Convert to Unit or Kernel test?
 *
 * @group file_entity
 */
class FileEntityUnitTest extends FileEntityTestBase {

  function setUp(): void {
    parent::setUp();
    $this->setUpFiles();
  }

  /**
   * Regression tests for core issue http://drupal.org/node/1239376.
   */
  function testMimeTypeMappings() {
    $tests = array(
      'public://test.ogg' => 'audio/ogg',
      'public://test.m4v' => 'video/x-m4v',
      'public://test.mka' => 'audio/x-matroska',
      'public://test.mkv' => 'video/x-matroska',
      'public://test.webp' => 'image/webp',
    );
    /** @var \Symfony\Component\Mime\MimeTypes $guesser */
    $guesser = $this->container->get('file.mime_type.guesser.extension');
    foreach ($tests as $input => $expected) {
      $this->assertEquals($expected, $guesser->guessMimeType($input));
    }
  }

  function testFileEntity() {
    $file = reset($this->files['text']);

    // Test entity ID, revision ID, and bundle.
    $this->assertEquals($file->id(), $file->fid->value);
    $this->assertEquals($file->getRevisionId(), NULL);
    $this->assertEquals($file->bundle(), 'document');

    // Test the entity URI callback.
    /*$uri = entity_uri('file', $file);
    $this->assertEqual($uri['path'], "file/{$file->fid}");*/
  }

  function testImageDimensions() {
    $files = array();
    $text_fids = array();
    // Test hook_file_insert().
    // Files have been saved as part of setup (in FileEntityTestHelper::setUpFiles).
    foreach ($this->files['image'] as $file) {
      $files[$file->id()] = $file->getAllMetadata();
      $this->assertTrue(
        $file->hasMetadata('height'),
        'Image height retrieved on file save for an image file.'
      );
      $this->assertTrue(
        $file->hasMetadata('width'),
        'Image width retrieved on file save for an image file.'
      );
    }
    foreach ($this->files['text'] as $file) {
      $text_fids[] = $file->id();
      $this->assertFalse(
        $file->hasMetadata('height'),
        'No image height retrieved on file save for an text file.'
      );
      $this->assertFalse(
        $file->hasMetadata('width'),
        'No image width retrieved on file save for an text file.'
      );
    }

    // Test hook_file load.
    // Clear the cache and load fresh files objects to test file_load behavior.
    \Drupal::entityTypeManager()->getStorage('file')->resetCache();
    foreach (File::loadMultiple(array_keys($files)) as $file) {
      $this->assertTrue(
        $file->hasMetadata('height'),
        'Image dimensions retrieved on file load for an image file.'
      );
      $this->assertTrue(
        $file->hasMetadata('width'),
        'Image dimensions retrieved on file load for an image file.'
      );
      $this->assertEquals($file->getMetadata('height'), $files[$file->id()]['height'], 'Loaded image height is equal to saved image height.');
      $this->assertEquals($file->getMetadata('width'), $files[$file->id()]['width'], 'Loaded image width is equal to saved image width.');
    }
    foreach (File::loadMultiple($text_fids) as $file) {
      $this->assertFalse(
        $file->hasMetadata('height'),
        'No image height retrieved on file load for an text file.'
      );
      $this->assertFalse(
        $file->hasMetadata('width'),
        'No image width retrieved on file load for an text file.'
      );
    }

    // Test hook_file_update().
    // Load the first image file and resize it.
    $image_files = array_keys($files);
    $file = File::load(reset($image_files));
    $image = \Drupal::service('image.factory')->get($file->getFileUri());
    $image->resize($file->getMetadata('width') / 2, $file->getMetadata('height') / 2);
    $image->save();
    $file->save();
    $this->assertEquals($file->getMetadata('height'), $files[$file->id()]['height'] / 2, 'Image file height updated by file save.');
    $this->assertEquals($file->getMetadata('width'), $files[$file->id()]['width'] / 2, 'Image file width updated by file save.');
    // Clear the cache and reload the file.
    \Drupal::entityTypeManager()->getStorage('file')->resetCache();
    $file = File::load($file->id());
    $this->assertEquals($file->getMetadata('height'), $files[$file->id()]['height'] / 2, 'Updated image height retrieved by file load.');
    $this->assertEquals($file->getMetadata('width'), $files[$file->id()]['width'] / 2, 'Updated image width retrieved by file load.');

    //Test hook_file_delete().
    $file->delete();
    $this->assertEquals(0, \Drupal::database()->query(
        'SELECT COUNT(*) FROM {file_metadata} WHERE fid = :fid',
        array(':fid' => 'fid')
      )->fetchField(),
      'Row deleted in {file_dimensions} when deleting the file.'
    );
  }
}

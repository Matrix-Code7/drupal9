<?php

namespace Drupal\Tests\file_entity\Functional;

use Drupal\file\Entity\File;

/**
 * Tests file replace functionality.
 *
 * @group file_entity
 */
class FileEntityReplaceTest extends FileEntityTestBase {

  /**
   * {@inheritdoc}
   */
  public function setUp(): void {
    parent::setUp();
    $this->setUpFiles();
  }

  /**
   * @todo Test image dimensions for an image field are reset when a file is replaced.
   * @todo Test image styles are cleared when an image is updated.
   */
  public function testReplaceFile() {
    // Select the first text test file to use.
    $file = reset($this->files['text']);

    // Create a user with file edit permissions.
    $user = $this->drupalCreateUser(array('edit any document files'));
    $this->drupalLogin($user);

    // Test that the Upload widget appears for a local file.
    $this->drupalGet('file/' . $file->id() . '/edit');
    $this->assertSession()->fieldExists('files[replace_upload]');

    // Test that file saves without uploading a file.
    $this->submitForm(array(), t('Save'));
    $this->assertSession()->pageTextContains(t('@file has been updated.', array('@file' => $file->getFilename())));

    // Get the next text file to use as a replacement.
    $original = clone $file;
    $replacement = next($this->files['text']);

    // Test that the file saves when uploading a replacement file.
    $edit = array();
    $edit['files[replace_upload]'] = \Drupal::service('file_system')->realpath($replacement->getFileUri());
    $this->drupalGet('file/' . $file->id() . '/edit');
    $this->submitForm($edit, t('Save'));
    $this->assertSession()->pageTextContains(t('@file has been updated.', array('@file' => $file->getFilename())));

    // Re-load the file from the database.
    /** @var \Drupal\file\FileInterface $file */
    $file = File::load($file->id());

    // Test how file properties changed after the file has been replaced.
    $this->assertEquals($file->getFilename(), $original->getFilename(), 'Updated file name did not change.');
    $this->assertNotEquals($file->getSize(), $original->getSize(), 'Updated file size changed from previous file.');
    $this->assertEquals($file->getSize(), $replacement->getSize(), 'Updated file size matches uploaded file.');
    $this->assertEquals(file_get_contents($file->getFileUri()), file_get_contents($replacement->getFileUri()), 'Updated file contents matches uploaded file.');
    $this->assertEmpty(\Drupal::entityQuery('file')->condition('status', 0)->accessCheck(FALSE)->execute(), 'Temporary file used for replacement was deleted.');

    // Get an image file.
    $image = reset($this->files['image']);
    $edit['files[replace_upload]'] = \Drupal::service('file_system')->realpath($image->getFileUri());
    $this->drupalGet('file/' . $file->id() . '/edit');

    // Test that validation works by uploading a non-text file as a replacement.
    $this->submitForm($edit, t('Save'));
    $this->assertSession()->responseContains(t('The specified file %file could not be uploaded.', array('%file' => $image->getFilename())));
    $this->assertSession()->pageTextContains('Only files with the following extensions are allowed: txt.');

    $replacement = next($this->files['text']);

    // Test the file upload.
    $edit = array();
    $edit['files[replace_upload]'] = \Drupal::service('file_system')->realpath($replacement->getFileUri());
    $this->drupalGet('file/' . $file->id() . '/edit');
    $this->submitForm($edit, t('Upload'));
    $this->assertSession()->pageTextContains('text-2.txt');
    $this->submitForm(array(), t('Save'));
    $this->assertSession()->pageTextContains(t('@file has been updated.', array('@file' => $file->getFilename())));

    // Create a non-local file record.
    /** @var \Drupal\file\FileInterface $file2 */
    $file2 = File::create(array('type' => 'image'));
    $file2->setFileUri('http://' . $this->randomMachineName());
    $file2->getFilename(\Drupal::service('file_system')->basename($file2->getFileUri()));
    $file2->setMimeType('image/oembed');
    $file2->setOwnerId(1);
    $file2->getSize(0);
    $file2->save();

    // Test that Upload widget does not appear for non-local file.
    $this->drupalGet('file/' . $file2->id() . '/edit');
    $this->assertSession()->fieldNotExists('files[replace_upload]');

  }
}

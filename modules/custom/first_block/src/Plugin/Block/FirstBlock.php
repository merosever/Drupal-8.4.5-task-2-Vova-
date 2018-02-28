<?php

/**
 * @file
 * Contains \Drupal\first_block\Plugin\Block\firstBlock.
 */

namespace Drupal\first_block\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\file\Entity\File;
/**
 * Добавляем простой блок с текстом.
 * Ниже - аннотация, она также обязательна.
 *
 * @Block(
 *   id = "simple_block_example",
 *   admin_label = @Translation("Simple block example"),
 * )
 */
class FirstBlock extends BlockBase {

    /**
     * {@inheritdoc}
     */
  public function blockForm($form, FormStateInterface $form_state) {
    $form = parent::blockForm($form, $form_state);
    //$config = $this->getConfiguration();

    $form['#attributes'] = ['enctype' => 'multipart/form-data'];

    $form['textfield'] = [
      '#type' => 'textfield',
      '#title' => t('title'),
      //'#default_value' => $config['textfield'],
    ];

    $form['image'] = [
      '#type' => 'managed_file',
      '#title' => t('File *'),
      '#size' => 20,
      '#description' => t('PDF format only'),
      '#upload_validators' => [
        'file_validate_extensions' => [
          'jpg',
          'JPG',
          'jpeg',
          'gif',
          'bmp',
          'png',
        ],
      ],
      '#upload_location' => 'public://my_files/',
    ];

    $form['textarea'] = [
      '#type' => 'textarea',
      '#title' => t('textarea'),
      //'#default_value' => $config['textarea'],
    ];

    $form['date'] = [
      '#type' => 'date',
      '#title' => t('date'),
      //'#default_value' => $config['date'],
    ];

    $form['submit'] = [
      '#type' => 'submit',
      '#value' => t('Submit'),
    ];

    return $form;
  }

    /**
     * {@inheritdoc}
     */
    public function blockValidate($form, FormStateInterface $form_state)
    {
      $textfield_count = $form_state->getValue('textfield_count');
      $textarea_count = $form_state->getValue('textarea_count');

      if(strlen($textfield_count) > 50) {
        $form_state->setErrorByName('textfield',t('This field can not contain more than 50 characters.'));
      }

      if(strlen($textarea_count) > 255) {
        $form_state->setErrorByName('textarea',t('This field can not contain more than 255 characters.'));
      }
    }

    /**
     * {@inheritdoc}
     */
    public function blockSubmit($form, FormStateInterface $form_state)
    {
        $this->configuration['textfield'] = $form_state->getValue('textfield');
        $this->configuration['image'] = $form_state->getValue('image');
        $this->configuration['textarea'] = $form_state->getValue('textarea');
        $this->configuration['date'] = $form_state->getValue('date');
    }

    /**
     * {@inheritdoc}
     */
    public function build()
    {

      $config = $this->getConfiguration();
      $textarea = $config['textarea'];
      $image_id = $config['image'][0];
      $textfield = $config['textfield'];
      $date = $config['date'];
      $file = File::load($image_id);


      $variables = array(
        'style_name' => 'tes_',
        'uri' => $file -> getFileUri(),
      );

      // The image.factory service will check if our image is valid.
      $image = \Drupal::service('image.factory')->get($file->getFileUri());
      if ($image -> isValid()) {
        $variables['width'] = $image -> getWidth();
        $variables['height'] = $image -> getHeight();
      }
      else {
        $variables['width'] = $variables['height'] = NULL;
      }

      $logo_render_array = [
        '#theme' => 'image_style',
        '#width' => $variables['width'],
        '#height' => $variables['height'],
        '#style_name' => $variables['style_name'],
        '#uri' => $variables['uri'],
      ];

      $renderer = \Drupal::service('renderer');

      $block = array();

      $block['textarea'] = [
        '#type' => 'markup',
        '#markup' => t("$textarea"),
      ];

      $block['image'] = [
        '#type' => 'markup',
        '#markup' => $renderer->render($logo_render_array),
      ];

      $block['textfield'] = [
        '#type' => 'markup',
        '#markup' => t("$textfield"),
      ];

      $block['date'] = [
        '#type' => 'markup',
        '#markup' => t("<br>"."$date"),
      ];

      return $block;
    }

}

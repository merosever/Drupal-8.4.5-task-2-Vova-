<?php

/**
 * @file
 * Contains \Drupal\first_block\Plugin\Block\firstBlock.
 */

namespace Drupal\first_block\Plugin\Block;
//Подключили классы методы которых будем использовать в нашем коде.
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
    //Указываем в атрибутах enctype для того чтоб можно было загружать файлы.Без этого атрибута форма с загрузки файлов работать не будет.
    $form['#attributes'] = ['enctype' => 'multipart/form-data'];

    $form['textfield'] = [
      '#type' => 'textfield',
      '#title' => t('My textfield'),
    ];

    $form['image'] = [
      '#type' => 'managed_file',
      '#title' => t('File'),
      '#size' => 20,
      '#description' => t('PDF format only'),
      '#required' => TRUE,
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
      '#title' => t('My textarea'),
    ];

    $form['date'] = [
      '#type' => 'date',
      '#title' => t('date'),
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function blockValidate($form, FormStateInterface $form_state) {
    $textfield_count = $form_state->getValue('textfield_count');
    $textarea_count = $form_state->getValue('textarea_count');

    //В валидаторе указываем логику обработки наших полей(textfield,textarea)
    if (strlen($textfield_count) > 50) {
      $form_state->setErrorByName('textfield', t('This field can not contain more than 50 characters.'));
    }

    if (strlen($textarea_count) > 255) {
      $form_state->setErrorByName('textarea', t('This field can not contain more than 255 characters.'));
    }
  }

  /**
   * {@inheritdoc}
   */
  public function blockSubmit($form, FormStateInterface $form_state) {
    //Сохраняем наши данные в массив
    $this->configuration['textfield'] = $form_state->getValue('textfield');
    $this->configuration['image'] = $form_state->getValue('image');
    $this->configuration['textarea'] = $form_state->getValue('textarea');
    $this->configuration['date'] = $form_state->getValue('date');
  }

  /**
   * {@inheritdoc}
   */
  public function build() {
    $block = [];

    $config = $this->getConfiguration();

    $textarea = $config['textarea'];
    $textfield = $config['textfield'];
    $date = $config['date'];


    if (isset($config['image'][0])) {

      $file = File::load($config['image'][0]);

      $variables = [
        'style_name' => 'tes_',
        'uri' => $file->getFileUri(),
      ];

      $image = \Drupal::service('image.factory')->get($file->getFileUri());
      if ($image->isValid()) {
        $variables['width'] = $image->getWidth();
        $variables['height'] = $image->getHeight();
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


      $block['image'] = [
        '#type' => 'markup',
        '#markup' => $renderer->render($logo_render_array),
        '#weigth' => 100,
      ];

    }

    /** @var \Drupal\Core\Datetime\DateFormatter $ConclusionDate */
    $ConclusionDate = \Drupal::service('date.formatter');
    //var_dump($ConclusionDate);

    $block['textarea'] = [
      '#type' => 'markup',
      '#markup' => $textarea,
      '#weigth' => 99,
    ];
    $block['textfield'] = [
      '#type' => 'markup',
      '#markup' => $textfield,
      '#weigth' => -101,
    ];

    $block['date'] = [
      '#type' => 'markup',
      '#markup' => $ConclusionDate->format(strtotime($date), 'my_custom_format'), //Вывели дату через созданный нами формат my_custom_format в "Формат даты и времени".
      '#weigth' => 102,
    ];


    return $block;
  }

}

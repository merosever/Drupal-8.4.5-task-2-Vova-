<?php

/**
 * @file
 * Contains \Drupal\ModuleMy\Plugin\Block\BlockMy.
 */

// Пространство имён для нашего блока.
// helloworld - это наш модуль.
namespace Drupal\ModuleMy\Plugin\Block;

use Drupal\Core\Block\BlockBase;

/**
 * Provides a custom_block.
 *
 * @Block(
 *   id = "custom_block",
 *   admin_label = @Translation("Custom block"),
 *   category = @Translation("Custom block example")
 * )
 */
class BlockMy extends BlockBase {

    public function build() {
        $block = [
            '#type' => 'markup',
            '#markup' => '<strong>Hello World!</strong>'
        ];
        return $block;
    }

}

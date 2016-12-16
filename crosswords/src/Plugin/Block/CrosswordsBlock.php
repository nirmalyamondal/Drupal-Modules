<?php

/**
 * @file
 * Contains \Drupal\crosswords\Plugin\Block\CrosswordsBlock.
 */

namespace Drupal\crosswords\Plugin\Block;

use Drupal\Core\Block\BlockBase;

/**
 * Provides a 'Crosswords' Block
 *
 * @Block(
 *   id = "crosswords_block",
 *   admin_label = @Translation("Crosswords block"),
 * )
 */
class CrosswordsBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
    return [
      '#markup' => $this->t('Hello World BLOCK for Crosswords!'),
    ];
  }

}

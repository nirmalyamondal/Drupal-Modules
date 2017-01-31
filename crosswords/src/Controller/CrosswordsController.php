<?php

namespace Drupal\crosswords\Controller;

/**
 * @file
 * Contains \Drupal\crosswords\Controller\CrosswordsController.
 */

use Drupal\Core\Controller\ControllerBase;
use Drupal\node\NodeInterface;

/**
 * Returns responses for crosswords controller routes.
 */
class CrosswordsController extends ControllerBase {

  /**
   * {@inheritdoc}
   */
  public $crosswordsHintAll = [];

  /**
   * {@inheritdoc}
   */
  public function showCrosswords(NodeInterface $cross_node) {
    if (!is_object($cross_node)) {
      return [];
    }
    if ($cross_node->getType() !== 'crosswords') {
      return [];
    }
    $savedCrosswords = $cross_node->get('field_crosswords_text')->value;
    $crosswordsHcolumn = $cross_node->get('field_crosswords_hcolumn')->value;
    $crosswordsHrow = $cross_node->get('field_crosswords_hrow')->value;
    $crosswordsTitle = $cross_node->get('title')->value;
    if (strlen($savedCrosswords) <= 2) {
      return [];
    }
    if (strpos($savedCrosswords, '###') !== FALSE) {
      $explodedSavedCrosswords = explode('###', $savedCrosswords);
    }
    $crosswordsArray = explode(",", $explodedSavedCrosswords[1]);
    $crosswordsData = '';
    $counter = 1;
    $arrayCounted = count($crosswordsArray);
    foreach ($crosswordsArray as $cArrayValue) {  
      $crosswordsData .= $cArrayValue ? $cArrayValue : '#';
      if (($counter % $explodedSavedCrosswords[0] == 0) && ($counter < $arrayCounted)) {
        $crosswordsData .= "\n";
      }
      $counter++;
    }
    $numCols = $explodedSavedCrosswords[0];
    $numRows = $arrayCounted / $numCols;
    // Process Hints.
    $this->getCrosswordsHintRow($crosswordsHrow, $numRows, $numCols, 'hor');
    $this->getCrosswordsHintRow($crosswordsHcolumn, $numRows, $numCols, 'ver');
    $crosswordsHintVh = $this->processCrosswordsHintForHorizontalVerticle();
    return [
      '#theme' => 'crosswords-crosswords',
      '#crosswords_variable_title' => $crosswordsTitle,
      '#attached' => [
        'drupalSettings' => [
          'crosswordsData' => $crosswordsData,
          'crosswordsCaption' => $crosswordsTitle,
          'crosswordsVerticle' => $crosswordsHintVh['ver'],
          'crosswordsHorizontal' => $crosswordsHintVh['hor'],
        ],
      ],
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function getCrosswordsHintRow($crosswordsHint, $rows, $cols, $horVer) {
    if (strpos($crosswordsHint, '###RANDC###') !== FALSE) {
      $crosswordsHintArray = explode('###RANDC###', $crosswordsHint);
    }
    $crosswordsHintRowArray = explode(',', $crosswordsHintArray[0]);
    $crosswordsHintColArray = explode(',', $crosswordsHintArray[1]);
    if (strpos($crosswordsHintArray[2], '###') !== FALSE) {
      $crosswordsHintDataArray = explode('###', $crosswordsHintArray[2]);
    }
    $maxCell = $rows * $cols;
	foreach ($crosswordsHintRowArray as $key => $value) {
      if ($value >= 1) {
        $cellNumber = ($value - 1) * $cols + $crosswordsHintColArray[$key];
        if (($cellNumber <= $maxCell) && ($crosswordsHintDataArray[$key] != '')) {
          $this->crosswordsHintAll[$cellNumber][$horVer] = $crosswordsHintDataArray[$key];
        }
      }
    }
  }

  /**
   * {@inheritdoc}
   */
  public function processCrosswordsHintForHorizontalVerticle() {
    ksort($this->crosswordsHintAll);
    $finalHintStringHor = $finalHintStringVer = '';
    $counter = 1;
    foreach ($this->crosswordsHintAll as $value) {	
      if (isset($value['hor'])) {
        $finalHintStringHor .= $counter . ' ' . $value['hor'] . "\n";
      }
      if (isset($value['ver'])) {
        $finalHintStringVer .= $counter . ' ' . $value['ver'] . "\n";
      }
      $counter++;
    }
    $finalHintArray = [];
    $finalHintArray['hor'] = $finalHintStringHor;
    $finalHintArray['ver'] = $finalHintStringVer;
    return $finalHintArray;
  }

  /**
   * {@inheritdoc}
   */
  public function processCrosswordsHints($savedCrosswordsHint) {
    // Processing Crosswords Row HINT.
    if (strpos($savedCrosswordsHint, '###RANDC###') !== FALSE) {
      $explodedSavedCrosswordsHint = explode('###RANDC###', $savedCrosswordsHint);
    }
    $savedCrosswordsHintRow['row'] = '[' . $explodedSavedCrosswordsHint[0] . ']';
    $savedCrosswordsHintRow['col'] = '[' . $explodedSavedCrosswordsHint[1] . ']';
    if (strpos($explodedSavedCrosswordsHint[2], '###') !== FALSE) {
      $explodedSavedCrosswordsHintInputArray = explode('###', $explodedSavedCrosswordsHint[2]);
      $explodedSavedCrosswordsHintInputString = implode('","', $explodedSavedCrosswordsHintInputArray);
    }
    $savedCrosswordsHintRow['data'] = '["' . $explodedSavedCrosswordsHintInputString . '"]';
    return $savedCrosswordsHintRow;
  }

}

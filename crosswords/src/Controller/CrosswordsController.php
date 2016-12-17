<?php

namespace Drupal\crosswords\Controller;

use Drupal\node\NodeInterface;
/**
  @file
  Contains \Drupal\crosswords\Controller\CrosswordsController.
 */
use Drupal\Core\Controller\ControllerBase;

class CrosswordsController extends ControllerBase {

    var $crosswords_hint_all = [];

    /**
     * action showCrosswords
     *
     * @return array
     */
    public function showCrosswords(NodeInterface $cross_node) {
        if (!is_object($cross_node)) {
            return [];
        }
        if ($cross_node->getType() !== 'crosswords') {
            return [];
        }
        $savedCrosswords = $cross_node->get('field_crosswords_text')->value;
        $crosswords_hcolumn = $cross_node->get('field_crosswords_hcolumn')->value;
        $crosswords_hrow = $cross_node->get('field_crosswords_hrow')->value;
        $crosswords_title = $cross_node->get('title')->value;
        if (strpos($savedCrosswords, '###') !== false) {
            $explodedSavedCrosswords = explode('###', $savedCrosswords);
        }
        $crosswordsArray = explode(",", $explodedSavedCrosswords[1]);
        $crosswordsData = '';
        $counter = 1;
        $arrayCounted = count($crosswordsArray);
        foreach ($crosswordsArray as $cArrayKey => $cArrayValue) {
            $crosswordsData .= $cArrayValue ? $cArrayValue : '#';
            if (($counter % $explodedSavedCrosswords[0] == 0) && ($counter < $arrayCounted)) {
                $crosswordsData .= "\n";
            }
            $counter++;
        }
        $num_cols = $explodedSavedCrosswords[0];
        $num_rows = $arrayCounted / $num_cols;
        // Process Hints
        $this->getCrosswordsHintRow($crosswords_hrow, $num_rows, $num_cols, 'hor');
        $this->getCrosswordsHintRow($crosswords_hcolumn, $num_rows, $num_cols, 'ver');
        $crosswords_hint_vh = $this->processCrosswordsHintForHorizontalVerticle();
        return [
            //'#type' => 'markup',
            '#theme' => 'crosswords-crosswords',
            '#crosswords_variable_title' => $crosswords_title,
            '#attached' => [
                'drupalSettings' => [
                    'crosswordsData' => $crosswordsData,
                    'crosswordsCaption' => $crosswords_title,
                    'crosswordsVerticle' => $crosswords_hint_vh['ver'],
                    'crosswordsHorizontal' => $crosswords_hint_vh['hor'],
                ],
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getCrosswordsHintRow($crosswords_hint, $rows, $cols, $hor_ver) {
        if (strpos($crosswords_hint, '###RANDC###') !== false) {
            $crosswords_hint_array = explode('###RANDC###', $crosswords_hint);
        }
        $crosswords_hint_row_array = explode(',', $crosswords_hint_array[0]);
        $crosswords_hint_col_array = explode(',', $crosswords_hint_array[1]);
        if (strpos($crosswords_hint_array[2], '###') !== false) {
            $crosswords_hint_data_array = explode('###', $crosswords_hint_array[2]);
        }
        $max_cell = $rows * $cols;
        $final_hint_string = '';
        foreach ($crosswords_hint_row_array as $key => $value) {
            if ($value >= 1) {
                $cell_number = ($value - 1) * $cols + $crosswords_hint_col_array[$key];
                if (($cell_number <= $max_cell) && ($crosswords_hint_data_array[$key] != '')) {
                    $this->crosswords_hint_all[$cell_number][$hor_ver] = $crosswords_hint_data_array[$key];
                }
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function processCrosswordsHintForHorizontalVerticle() {
        ksort($this->crosswords_hint_all);
        $final_hint_string_hor = $final_hint_string_ver = '';
        $counter = 1;
        foreach ($this->crosswords_hint_all as $key => $value) {
            if (isset($value['hor'])) {
                $final_hint_string_hor .= $counter . ' ' . $value['hor'] . "\n";
            }
            if (isset($value['ver'])) {
                $final_hint_string_ver .= $counter . ' ' . $value['ver'] . "\n";
            }
            $counter++;
        }
        $final_hint_array = [];
        $final_hint_array['hor'] = $final_hint_string_hor;
        $final_hint_array['ver'] = $final_hint_string_ver;
        return $final_hint_array;
    }

}

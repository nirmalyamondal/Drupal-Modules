<?php

/**
 * @file
 * Contains \Drupal\migrate_custom\Plugin\migrate\source\Solutions.
 */

namespace Drupal\migrate_custom\Plugin\migrate\source;

use Drupal\migrate\Row;
use Drupal\migrate\Plugin\migrate\source\SqlBase;
use Drupal\migrate_custom\Plugin\migrate\source\MigrationLibrary as migLib;

/**
 * Drupal 7 Solutions node source plugin
 *
 * @MigrateSource(
 *   id = "custom_solutions"
 * )
 */
class Solutions extends SqlBase {

  /**
   * {@inheritdoc}
   */
  public function query() {	
    // this queries the built-in metadata, but not the body, tags, or images.
    $query = $this->select('node', 'n')
        ->condition('n.type', 'solution')
        ->fields('n', ['nid','type','language','title','uid','status','created','changed','promote','sticky']);
    $query->orderBy('nid');
    return $query;
  }

  /**
   * {@inheritdoc}
   */
  public function fields() {
    $fields = $this->baseFields();
    $fields['body/format'] = $this->t('Format of body');
    $fields['body/value'] = $this->t('Full text of body');
    $fields['body/summary'] = $this->t('Summary of body');
    $fields['field_image_fid'] = $this->t('Image');
    return $fields;
  }

  /**
   * {@inheritdoc}
   */
  public function prepareRow(Row $row) {
	$objMigLib = new migLib;	
	$nid = $row->getSourceProperty('nid');
	// body (compound field with value, summary, and format)
	$result = $this->getDatabase()->query('SELECT body_value,body_summary,body_format FROM {field_data_body} WHERE entity_id = :nid',[':nid' => $nid]);
    foreach ($result as $record) {
	  $processed_body_value	= $objMigLib->processD7bodyToD8($record->body_value,$this->getDatabase());
      $row->setSourceProperty('body_value', $processed_body_value);
      $row->setSourceProperty('body_summary', $record->body_summary);
      $row->setSourceProperty('body_format', $record->body_format);
    }
	// field_highlight D8:node__field_highlight
    $result = $this->getDatabase()->query('SELECT * FROM {field_data_field_highlight} WHERE entity_id = :nid', [':nid' => $nid]);
    // Create an associative array for each row in the result. The keys
    // here match the last part of the column name in the field table.
    $field_highlights = [];
    foreach ($result as $record) {
      $field_highlights[] = [
        'bundle' => $record->bundle,
        'deleted' => $record->deleted,
		'langcode' => 'en',
        'delta' => $record->delta,
        'value' => $record->field_highlight_value,
      ];
    }
    $row->setSourceProperty('sourceHighlight', $field_highlights);
	$row->setSourceProperty('setType', 'solutions');
	//print_r($row); die();
    return parent::prepareRow($row);
  }

  /**
   * {@inheritdoc}
   */
  public function getIds() {
    $ids['nid']['type'] = 'integer';
    $ids['nid']['alias'] = 'n';
    return $ids;
  }

  /**
   * {@inheritdoc}
   */
  public function bundleMigrationRequired() {
    return FALSE;
  }

  /**
   * {@inheritdoc}
   */
  public function entityTypeId() {
    return 'node';
  }

  /**
   * Returns the user base fields to be migrated.
   *
   * @return array
   *   Associative array having field name as key and description as value.
   */
  protected function baseFields() {
    $fields = [
      'nid' => $this->t('Node ID'),
      //'vid' => $this->t('Version ID'),
      'type' => $this->t('Type'),
      'title' => $this->t('Title'),
      'format' => $this->t('Format'),
      'teaser' => $this->t('Teaser'),
      'uid' => $this->t('Authored by (uid)'),
      'created' => $this->t('Created timestamp'),
      'changed' => $this->t('Modified timestamp'),
      'status' => $this->t('Published'),
      'promote' => $this->t('Promoted to front page'),
      'sticky' => $this->t('Sticky at top of lists'),
      'language' => $this->t('Language (fr, en, ...)'),
    ];
    return $fields;
  }

}

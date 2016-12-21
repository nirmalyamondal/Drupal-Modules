<?php

/**
 * @file
 * Contains \Drupal\migrate_custom\Plugin\migrate\source\Blog.
 */

namespace Drupal\migrate_custom\Plugin\migrate\source;

use Drupal\migrate\Row;
use Drupal\migrate\Plugin\migrate\source\SqlBase;
use Drupal\migrate_custom\Plugin\migrate\source\MigrationLibrary as migLib;

/**
 * Drupal 7 Blog node source plugin
 *
 * @MigrateSource(
 *   id = "custom_client"
 * )
 */
class Client extends SqlBase {

  /**
   * {@inheritdoc}
   */
  public function query() {	
    // this queries the built-in metadata, but not the body, tags, or images.
    $query = $this->select('node', 'n')
        ->condition('n.type', 'client')
        ->fields('n', ['nid','type','language','title','uid','status','created','changed','promote','sticky']);
    $query->orderBy('nid');
    return $query;
  }

  /**
   * {@inheritdoc}
   */
  public function fields() {
    $fields = $this->baseFields();
    return $fields;
  }

  /**
   * {@inheritdoc}
   */
  public function prepareRow(Row $row) {
	$objMigLib = new migLib;	
	$nid = $row->getSourceProperty('nid');
    // images field_data_field_client_logo >> node__field_client_logo`
    $result = $this->getDatabase()->query('SELECT * FROM {field_data_field_client_logo} WHERE entity_id = :nid', [':nid' => $nid]);
    // Create an associative array for each row in the result. The keys
    // here match the last part of the column name in the field table.
    $images = [];
    foreach ($result as $record) {
      $images[] = [
        'target_id' => $record->field_client_logo_fid,
        'alt' => $record->field_client_logo_alt,
        'title' => $record->field_client_logo_title,
        'width' => $record->field_client_logo_width,
        'height' => $record->field_client_logo_height,
      ];
		$fidFile = $record->field_client_logo_fid;
    }
	// Reset with new field_client_logo so do it after file_managed
    //$row->setSourceProperty('images', $images);
    $fresultx = $this->getDatabase()->query('SELECT * FROM {file_managed} WHERE fid = :ffid', [':ffid' => $fidFile]);
	foreach ($fresultx as $frecord) {
		$fresult	= $frecord;
	}
	if($fresult->uri) {
		$fileId	= $objMigLib->handleFileImage($fresult);
		//Considering 1 record
		$images[0]['target_id'] = $fileId;
		$row->setSourceProperty('images', $images);
	}//if($fresult->uri)
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
	  'field_client_logo' => $this->t('Client Logo'),
    ];
    return $fields;
  }

}

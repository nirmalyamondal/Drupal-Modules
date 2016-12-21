<?php

/**
 * @file
 * Contains \Drupal\migrate_custom\Plugin\migrate\source\Blog.
 */

namespace Drupal\migrate_custom\Plugin\migrate\source;

use Drupal\migrate\Row;
use Drupal\migrate\Plugin\migrate\source\SqlBase;
use Drupal\Component\Serialization\Json;
use Drupal\migrate_custom\Plugin\migrate\source\MigrationLibrary as migLib;

/**
 * Drupal 7 Blog node source plugin
 *
 * @MigrateSource(
 *   id = "custom_event"
 * )
 */
class Event extends SqlBase {

  /**
   * {@inheritdoc}
   */
  public function query() {	
    // this queries the built-in metadata, but not the body, tags, or images.
    $query = $this->select('node', 'n')
        ->condition('n.type', 'event')
		//->condition('n.nid', 1754, '>')
		//->condition('n.created', 1475756375, '>')
        ->fields('n', ['nid','type','language','title','uid','status','created','changed','promote','sticky']);
	//$query->range(0, 1);
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
	
	// field_date D8:node__field_to_date
    $result = $this->getDatabase()->query('SELECT * FROM {field_data_field_date} WHERE entity_id = :nid', [':nid' => $nid]);
    // Create an associative array for each row in the result. The keys
    // here match the last part of the column name in the field table.
    $field_date = [];
    foreach ($result as $record) {
      $field_date[] = [
        'bundle' => $record->bundle,
        'deleted' => $record->deleted,
		'langcode' => 'en',
        'delta' => $record->delta,
        'value' => str_replace(" ","T",$record->field_date_value),
      ];
    }
    $row->setSourceProperty('sourceDate', $field_date);

	// field_slideshare_ppt_id D8:node__field_slideshare_ppt_id
    $result = $this->getDatabase()->query('SELECT * FROM {field_data_field_slideshare_ppt_id} WHERE entity_id = :nid', [':nid' => $nid]);
    // Create an associative array for each row in the result. The keys
    // here match the last part of the column name in the field table.
    $field_slideshare_ppt_id = [];
    foreach ($result as $record) {
      $field_slideshare_ppt_id[] = [
        'bundle' => $record->bundle,
        'deleted' => $record->deleted,
		'langcode' => 'en',
        'delta' => $record->delta,
        'value' => $record->field_slideshare_ppt_id_value,
      ];
    }
    $row->setSourceProperty('sourcePpt', $field_slideshare_ppt_id);

	// field_author_name D8:node__field_author_name
    $result = $this->getDatabase()->query('SELECT * FROM {field_data_field_author_name} WHERE entity_id = :nid', [':nid' => $nid]);
    // Create an associative array for each row in the result. The keys
    // here match the last part of the column name in the field table.
    $field_author_name = [];
    foreach ($result as $record) {
      $field_author_name[] = [
        'bundle' => $record->bundle,
        'deleted' => $record->deleted,
		'langcode' => 'en',
        'delta' => $record->delta,
        'value' => $record->field_author_name_value,
      ];
    }
    $row->setSourceProperty('sourceAuthor', $field_author_name);

	// field_highlight D8:node__field_linkedin_link
    $result = $this->getDatabase()->query('SELECT * FROM {field_data_field_linkedin_link} WHERE entity_id = :nid', [':nid' => $nid]);
    // Create an associative array for each row in the result. The keys
    // here match the last part of the column name in the field table.
    $field_linkedin_link = [];
    foreach ($result as $record) {
      $field_linkedin_link[] = [
        'bundle' => $record->bundle,
        'deleted' => $record->deleted,
		'langcode' => 'en',
        'delta' => $record->delta,
        'uri' => $record->field_linkedin_link_url,
		'title' => $record->field_linkedin_link_title,
		'options' => $record->field_linkedin_link_options,
      ];
    }
    $row->setSourceProperty('sourceLinkedin', $field_linkedin_link);

	// field_drupal_give D8:node__field_drupal_give
    $result = $this->getDatabase()->query('SELECT * FROM {field_data_field_drupal_give} WHERE entity_id = :nid', [':nid' => $nid]);
    // Create an associative array for each row in the result. The keys
    // here match the last part of the column name in the field table.
    $field_drupal_give = [];
    foreach ($result as $record) {
      $field_drupal_give[] = [
        'bundle' => $record->bundle,
        'deleted' => $record->deleted,
		'langcode' => 'en',
        'delta' => $record->delta,
        'value' => $record->field_drupal_give_value,
      ];
    }
    $row->setSourceProperty('sourceDgive', $field_drupal_give);
	
	// images field_data_field_display_image >> node__field_image`
    $result = $this->getDatabase()->query('SELECT * FROM {field_data_field_display_image} WHERE entity_id = :nid', [':nid' => $nid]);
    // Create an associative array for each row in the result. The keys
    // here match the last part of the column name in the field table.
    $images = [];
    foreach ($result as $record) {
      $images[] = [
        'target_id' => $record->field_display_image_fid,
        'alt' => $record->field_display_image_alt,
        'title' => $record->field_display_image_title,
        'width' => $record->field_display_image_width,
        'height' => $record->field_display_image_height,
      ];
		$fidFile = $record->field_display_image_fid;
    }
	
	// Reset with new field_image_fid so do it after file_managed
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
	} //if($fresult->uri)
	
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
	  'field_display_image' => $this->t('Image path'),
    ];
    return $fields;
  }

}

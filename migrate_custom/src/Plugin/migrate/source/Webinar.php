<?php

/**
 * @file
 * Contains \Drupal\migrate_custom\Plugin\migrate\source\Webinar.
 */

namespace Drupal\migrate_custom\Plugin\migrate\source;

use Drupal\migrate\Row;
use Drupal\migrate\Plugin\migrate\source\SqlBase;
use Drupal\migrate_custom\Plugin\migrate\source\MigrationLibrary as migLib;


/**
 * Drupal 7 Webinar node source plugin
 *
 * @MigrateSource(
 *   id = "custom_webinar"
 * )
 */
class Webinar extends SqlBase {

  /**
   * {@inheritdoc}
   */
  public function query() {
    // this queries the built-in metadata, but not the body, tags, or images.
    $query = $this->select('node', 'n')
		->condition('n.type', 'webinar')
        //->condition('created', 1372055672, '<=')
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
	  $processed_body_value = $objMigLib->processD7bodyToD8($record->body_value,$this->getDatabase());
      $row->setSourceProperty('body_value', $processed_body_value);
      $row->setSourceProperty('body_summary', $record->body_summary);
      $row->setSourceProperty('body_format', $record->body_format);
    }

	// D7:field_date >> D8:node__field_date
    $d_result = $this->getDatabase()->query('SELECT * FROM {field_data_field_date} WHERE entity_id = :nid', [':nid' => $nid]);
    // Create an associative array for each row in the result. The keys
    // here match the last part of the column name in the field table.
    $field_date = [];
    foreach ($d_result as $d_record) {
      $field_date[] = [
        'bundle' => $d_record->bundle,
        'deleted' => $d_record->deleted,
		'langcode' => 'en',
        'delta' => $d_record->delta,
		// D7: MM/DD/YYYY HH:MM >> D8: DD/MM/YYYYTHH:MM
        'value' => str_replace(" ","T",$d_record->field_date_value),
      ];
    }
    $row->setSourceProperty('date', $field_date);

    // D7:field_highlight >> D8:node__field_teaser_text
    $h_result = $this->getDatabase()->query('SELECT * FROM {field_data_field_highlight} WHERE entity_id = :nid', [':nid' => $nid]);
    // Create an associative array for each row in the result. The keys
    // here match the last part of the column name in the field table.
    $field_highlights = [];
    foreach ($h_result as $h_record) {
      $field_highlights[] = [
        'bundle' => $h_record->bundle,
        'deleted' => $h_record->deleted,
		'langcode' => 'en',
        'delta' => $h_record->delta,
        'value' => $h_record->field_highlight_value,
      ];
    }
    $row->setSourceProperty('highlight', $field_highlights);
	
	// D7:field_upload_video_in_colorbox >> D8:field_upload_video
    $v_result = $this->getDatabase()->query('SELECT * FROM {field_data_field_upload_video_in_colorbox} WHERE entity_id = :nid', [':nid' => $nid]);
    // Create an associative array for each row in the result. The keys
    // here match the last part of the column name in the field table.
    $field_video = [];
    foreach ($v_result as $v_record) {
      $field_video[] = [
        'bundle' => $v_record->bundle,
        'deleted' => $v_record->deleted,
		'langcode' => 'en',
        'delta' => $v_record->delta,
        'value' => $v_record->field_upload_video_in_colorbox_video_url,
      ];
    }
    $row->setSourceProperty('upload_video_in_colorbox', $field_video);

	// D7:tag_presenter_name >> D8:field_speaker
	$s_result = $this->getDatabase()->query('SELECT * FROM {field_data_field_presenter_name} WHERE entity_id = :nid', [':nid' => $nid]);
    // Create an associative array for each row in the result. The keys
    // here match the last part of the column name in the field table.	
    $s_record_id	= 0;
    foreach ($s_result as $s_record) {
		//$s_record->field_presenter_name_value = 'Morten DK';
		$speaker_ref	= $objMigLib->mapThisPresenterTerm($s_record->field_presenter_name_value);
		$s_record_id	= $speaker_ref?$speaker_ref:0;
	}
	$row->setSourceProperty('tag_presenter_name', [0=>$s_record_id]);

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

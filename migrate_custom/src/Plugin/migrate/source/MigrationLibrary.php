<?php

/**
 * @file
 * Contains \Drupal\migrate_custom\Plugin\migrate\source\MigrationLibrary.
 */

namespace Drupal\migrate_custom\Plugin\migrate\source;

use Drupal\file\Entity\File;

/**
 * Drupal 8 Migration Library
 * 
 */

class MigrationLibrary {
	
   /**
   * {@inheritdoc}
   */
	public function processD7bodyToD8($bodyString,$sourceDbConObj) {
		$needle_start	= '[[';
		$needle_end		= ']]';
		$serializData	= $this->get_string_between($bodyString, $needle_start, $needle_end);		
		$serializDataObj = json_decode($serializData);	//print_r($serializDataObj);
		if(!$serializDataObj->fid){ return $bodyString;	}
			$fresult_f = $sourceDbConObj->query('SELECT * FROM {file_managed} WHERE fid = :ffid', [':ffid' => $serializDataObj->fid]);
		foreach ($fresult_f as $frecord) {
			$rowImg	= $frecord;
		}
		$filename = $rowImg->filename;
		$posPublic = strpos($rowImg->uri, 'public://');
		// For IMG
		if($filename && $posPublic) {	// Do we need to move file			
			$newDestPath	= \Drupal::service('file_system')->realpath($rowImg->uri);
			//Store in the filesystem.
			$fpath	= str_replace('public://', 'http://yourdomain.com/'.'sites/default/files/', $rowImg->uri);
			//Get the file
			$fcontent = @file_get_contents($fpath);
			$fp = @fopen($newDestPath, "w");
			@fwrite($fp, $fcontent);
			@fclose($fp);			
		}
		$replacement	= '<img alt="'.$serializDataObj->attributes->alt.'" data-entity-type="file" src="/sites/default/files/'.$filename.'" width="'.$serializDataObj->attributes->width.'" height="'.$serializDataObj->attributes->height.'"  class="'.$serializDataObj->attributes->class.' custom_migrated" />';
		
		$bodyString	= $this->replace_string_between($bodyString, $needle_start, $needle_end, $replacement);
		$pos		= strpos($bodyString, '[[');	
		if ($pos !== false) {
			$bodyString	= substr_replace($bodyString,'',$pos,strlen('[['));
			$posE		= strpos($bodyString, ']]');
			$bodyString	= substr_replace($bodyString,'',$posE,strlen(']]'));
			if (strpos($bodyString, '[[{') !== false) {
			 	return $this->processD7bodyToD8($bodyString,$sourceDbConObj);
			}
		}
	return $bodyString;
	}
	
	public function get_string_between($string, $start, $end) {
		$string = ' ' . $string;
		$ini = strpos($string, $start);
		if ($ini == 0) return '';
		$ini += strlen($start);
		$len = strpos($string, $end, $ini) - $ini;
		return substr($string, $ini, $len);
	}

	public function replace_string_between($str, $needle_start, $needle_end, $replacement){
		$pos = strpos($str, $needle_start);
		$start = $pos === false ? 0 : $pos + strlen($needle_start);
		$pos = strpos($str, $needle_end, $start);
		$end = $pos === false ? strlen($str) : $pos;
		return substr_replace($str, $replacement, $start, $end - $start);
	}

	public function handleFileImage($fresult) {
	  $uuid_service = \Drupal::service('uuid');
      $uuid = $uuid_service->generate();
      $fresult->filename = $fresult->filename ? $fresult->filename : 'Logo_Big.png';
      $fresult->status = $fresult->status ? $fresult->status : 1;
      $fresult->timestamp = $fresult->timestamp ? $fresult->timestamp : time();
      $newSourcePath = str_replace('public://', 'http://yourdomain.com/' . 'sites/default/files/', $fresult->uri);
      $newDestPath = \Drupal::service('file_system')->realpath($fresult->uri);
      //FILE_EXISTS_RENAME
      $fcontent = @file_get_contents($newSourcePath);
      $fp = @fopen($newDestPath, "w");
      @fwrite($fp, $fcontent);
      @fclose($fp);
      $file = File::Create([
            'uuid' => $uuid, //The users.uid of the user who is associated with the file.
            'langcode' => 'en',
            'uri' => $fresult->uri,
            'filename' => $fresult->filename,
            'filemime' => $fresult->filemime,
            'filesize' => $fresult->filesize,
            'status' => $fresult->status,
            'created' => $fresult->timestamp,
            'changed' => $fresult->timestamp,
      ]);
      $file->save();
      $fileId = $file->id();
	return $fileId;
	}

   /**
   * Webinar Speficic
   *
   * @return array
   *   Associative array having field name as key and description as value.
   */ 
	public function mapThisPresenterTerm($presenter_name){
		//$presenter_name	= 'My Name';
		$query = \Drupal::database()->select('taxonomy_term_field_data', 'ttfd');
		$query->fields('ttfd', ['tid']);
		$query->condition('ttfd.name', $presenter_name, '=');
		$query->range(0, 1);
		$tid = $query->execute()->fetchField();
		return $tid;
	}

}

<?php

namespace Drupal\custom_migrate_tool\Controller;

/**
  @file
  Contains \Drupal\custom_migrate_tool\Controller\CustomMigrateToolBodyMediaController.
 */
use Drupal\Core\Controller\ControllerBase;
use \Drupal\node\Entity\Node;

class CustomMigrateToolBodyMediaController extends ControllerBase {

  /**
   * Processing request bodymedia
   * 
   * @return array
   */
  public function bodyMediaProcess() {
	$query = \Drupal::entityQuery('node');
	$query->condition('status', 1);
	$query->condition('type', 'blog');
	//$query->condition('nid', 0, '>'); When we need all node entity
	//$query->fields('', ['entity_id', 'body']);	// Doesn't support.
	$query->sort('nid', 'ASC');
    $query->range(0, 1);
	$entity_ids = $query->execute();
$nid= $_GET['nid'];//6169
$nid =array($nid);
	$nodes = \Drupal::entityTypeManager()->getStorage('node')->loadMultiple($nid);
	echo '<pre>',print_r($nodes);
die();
	foreach($nodes as $node_id=>$node) {
		//set value for field
		//$processed_body = $this->processD7bodyToD8($node->body->value);
		//$node->set('body', $processed_body);
		//save to updated node
		$node->save();
	}
	
  }

	public function processD7bodyToD8($bodyString){
		$needle_start	= '[[';
		$needle_end		= ']]';
		$serializData	= $this->get_string_between($bodyString, $needle_start, $needle_end);		
		$serializDataObj = json_decode($serializData);	//print_r($serializDataObj);
		if(!$serializDataObj->fid){
			//return 
		}		
	//Source + Destination
		$servername	= "localhost";
		$username 	= "root";
		$password	= "nirmalya";
		$sourceDb	= 'sd7migrate.local';
		// Create connection
		$connObj = new \mysqli($servername, $username, $password, $sourceDb);
		$res	= $connObj->query("SELECT * FROM `file_managed` WHERE `fid` = ".$serializDataObj->fid);
		if ($res->num_rows > 0) {	
			$rowImg = $res->fetch_assoc();
			$filename = $rowImg['filename'];
		}
		$connObj->close();	
		$posPublic = strpos($rowImg['uri'], 'public://');
		// For IMG
		if($filename && $posPublic) {
			$newDestPath	= \Drupal::service('file_system')->realpath($rowImg['uri']);
			//$uri = file_unmanaged_copy($newSourcePath, $newDestPath, FILE_EXISTS_REPLACE);
			//Store in the filesystem.
			$fpath	= str_replace('public://', 'http://mywite.com/'.'sites/default/files/', $rowImg['uri']);
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
			 	return $this->processD7bodyToD8($bodyString);
			}
		}
	return $bodyString;
	}

	public function get_string_between($string, $start, $end){
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


}

<?php

namespace Drupal\custom_migrate_tool\Controller;

/**
  @file
  Contains \Drupal\custom_migrate_tool\Controller\CustomMigrateToolController.
 */
use Drupal\Core\Controller\ControllerBase;
use \Drupal\node\Entity\Node;

class CustomMigrateToolController extends ControllerBase {

  /**
   * action processWebinarFix
   * 
   * @return array
   */
  public function processWebinarFix() {
	//Source + Destination
	$servername	= "localhost";
	$username 	= "root";
	$password	= "password";
	$sourceDb	= 'sourcedb';
	// Create connection
	$conn = new \mysqli($servername, $username, $password, $sourceDb);
	//Change database to "Source"
	//mysqli_select_db($conn,$sourceDb);
	// It'll load from Dest db as conn didn't get SORRY!! $node = Node::load(1);	$node->set("title", 'New value'); $node->setTitle('new Title');	print_r($node->title->value); die();
	$sqlSourcePresenter = "SELECT entity_id, field_presenter_name_value FROM `field_data_field_presenter_name` WHERE bundle='webinar' ORDER BY field_presenter_name_value ASC LIMIT 500";
	$sqlSourceUrl	= "SELECT entity_id, field_upload_video_in_colorbox_video_url FROM field_data_field_upload_video_in_colorbox WHERE bundle='webinar' AND field_upload_video_in_colorbox_video_url != '' AND deleted=0 ORDER BY entity_id ASC LIMIT 500";
	$sqlSourceTitle	= "SELECT nid, title FROM node WHERE type= 'webinar' ORDER BY nid LIMIT 500";

	$resultSourceT 	= $conn->query($sqlSourceTitle); 
	if ($resultSourceT->num_rows > 0) {	
		$rowSourceRecT		= [];
		$sourceMasterRow	= [];
		// output data of each row
		while($rowSourceT = $resultSourceT->fetch_assoc()) {		
			$rowSourceRecT[$rowSourceT["nid"]] 					= $rowSourceT['title'];		
			//$sourceMasterRow[$rowSourceT["nid"]]['title']		= $rowSourceT['title'];
			$sourceMasterRow[$rowSourceT["nid"]]['presenter']	= '';
			$sourceMasterRow[$rowSourceT["nid"]]['url']			= '';
		}
	}
	//'[ nid => title]'
	$resultSourceP 	= $conn->query($sqlSourcePresenter); 
	if ($resultSourceP->num_rows > 0) {	
		//$rowSourceRec	= [];
		// output data of each row
		while($rowSourceP = $resultSourceP->fetch_assoc()) {	
			//$rowSourceRec[$rowSourceRecT[$rowSourceP["entity_id"]]]	= $rowSourceP['field_presenter_name_value'];	
			if($rowSourceRecT[$rowSourceP["entity_id"]]){	
				$sourceMasterRow[$rowSourceP["entity_id"]]['presenter']	= $rowSourceP['field_presenter_name_value'];
			}
		}
	}
	$resultSourceU 	= $conn->query($sqlSourceUrl); 
	if ($resultSourceU->num_rows > 0) {	
		//$rowSourceRec	= [];
		// output data of each row
		while($rowSourceU = $resultSourceU->fetch_assoc()) {	
			if($rowSourceRecT[$rowSourceU["entity_id"]]){	
				$sourceMasterRow[$rowSourceU["entity_id"]]['url']	= $rowSourceU['field_upload_video_in_colorbox_video_url'];
			}
		}
	}
	print_r($sourceMasterRow);
	//'[ title => presenter]'
	$conn->close();
    echo 'Calledxxx'; print_r($data_row);
	die();
    return [
      '#theme' => 'custom-migrate-tools',
      '#custom_migrate_tool_variable' => '',
      '#attached' => [
        'drupalSettings' =>  [
          'migrateSettings' => '',
        ],
      ],
    ];
  }

	/**
	* Request thanks
	* 
	* @return array
	*/
	public function showThanks() {
	}


}

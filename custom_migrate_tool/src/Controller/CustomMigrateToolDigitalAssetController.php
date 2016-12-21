<?php

namespace Drupal\custom_migrate_tool\Controller;

/**
  @file
  Contains \Drupal\custom_migrate_tool\Controller\CustomMigrateToolDigitalAssetController.
 */
use Drupal\Core\Controller\ControllerBase;
use \Drupal\node\Entity\Node;

class CustomMigrateToolDigitalAssetController extends ControllerBase {

  /**
   * action processWebinarFix
   * 
   * @return array
   */
  public function digitalAsset() {
	//Source + Destination
	$servername	= "localhost";
	$username 	= "root";
	$password	= "password";
	$sourceDb	= 'sourcedb';
	// Create connection
	$conn = new \mysqli($servername, $username, $password, $sourceDb);
	//Change database to "Source"
	//mysqli_select_db($conn,$sourceDb);
	// It'll load from Dest db as conn didn't get $node = Node::load(1);	$node->set("title", 'New value'); $node->setTitle('new Title');	print_r($node->title->value); die();
	$sqlSource 		= "SELECT entity_id, field_presenter_name_value FROM `field_data_field_presenter_name` WHERE bundle='webinar' ORDER BY field_presenter_name_value ASC LIMIT 500";
	$sqlSourceUrl	= "SELECT entity_id, field_upload_video_in_colorbox_video_url FROM field_data_field_upload_video_in_colorbox WHERE bundle='webinar' AND field_upload_video_in_colorbox_video_url != '' AND deleted=0 ORDER BY entity_id ASC LIMIT 500";
	$sqlSourceTitle	= "SELECT nid, title FROM node WHERE type= 'webinar' ORDER BY title LIMIT 500";
	$resultSourceT 	= $conn->query($sqlSourceTitle); 
	if ($resultSourceT->num_rows > 0) {	
		$rowSourceRecT	= [];
		$sourceResltRow	= [];
		// output data of each row
		while($rowSourceT = $resultSourceT->fetch_assoc()) {		
			$rowSourceRecT[$rowSourceT["nid"]] = $rowSourceT['title'];		
			$sourceResltRow[$rowSourceT["nid"]]	= $rowSourceT['title'];
		}
	}
	//'[ nid => title]'
	$resultSource 	= $conn->query($sqlSource); 
	if ($resultSource->num_rows > 0) {	
		$rowSourceRec	= [];
		// output data of each row
		while($rowSource = $resultSource->fetch_assoc()) {	
			//if(!$rowSourceRecT[$rowSource["entity_id"]]){ echo $rowSource['field_presenter_name_value'].'<br/>';}		
			$rowSourceRec[$rowSourceRecT[$rowSource["entity_id"]]] = $rowSource['field_presenter_name_value'];		
		}
	}
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
	* action processWebinarFix
	* 
	* @return array
	*/
	public function showThanks() {
	}


}

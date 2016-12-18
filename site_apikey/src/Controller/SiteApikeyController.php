<?php

namespace Drupal\site_apikey\Controller;

/**
  @file
  Contains \Drupal\site_apikey\Controller\SiteApikeyController.
 */
use Drupal\Core\Controller\ControllerBase;
use Drupal\node\NodeInterface;

class SiteApikeyController extends ControllerBase {

    /**
     * action accessChecker
     *
     * Serve node JSON data on uri http://mysite.local/page_json/apikey/nid
     * 
     * @return array
     */
    public function accessChecker($apikey, NodeInterface $page_node) {
        $siteapikey = \Drupal::config('system.site')->get('siteapikey');
        if ($apikey === $siteapikey) {
            if (!is_object($page_node)) {   // Drupal will handle this for us ;)
                echo t('Problem in Content loading ...!');
                die();
            }
            if ($page_node->getType() === 'page') {
                $serializer = \Drupal::service('serializer');
                $node_jsondata = $serializer->serialize($page_node, 'json', ['plugin_id' => 'entity']);
                echo $node_jsondata;
                die();
            }
            echo t('Content Type Mismatch!');
            die();
        }
        echo t('Access Denied!');
        die();
    }

    /**
     * {@inheritdoc}
     */
    public function site_apikey_form_validate($form, \Drupal\Core\Form\FormStateInterface $form_state) {
        if (strlen($form['site_information']['siteapikey']['#value']) !== 16) {
            $form_state->setErrorByName('siteapikey', t('Not Less or Greater then 16 characters !!!'));
        }
    }

    /**
     * {@inheritdoc}
     */
    public function site_apikey_form_submit($form, \Drupal\Core\Form\FormStateInterface $form_state) {
        $site_apikey_setting = $form['site_information']['siteapikey']['#value'] ? $form['site_information']['siteapikey']['#value'] : t('No API Key yet');
        \Drupal::configFactory()->getEditable('system.site')->set('siteapikey', $site_apikey_setting)->save();
    }

}

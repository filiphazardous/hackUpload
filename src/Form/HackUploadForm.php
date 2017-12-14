<?php

namespace Drupal\hack_upload\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Implements the fool file upload form
 */

class HackUploadForm extends FormBase {

  public function getFormId() {
    return 'hack_upload_form';
  }

  public function buildForm(array $form, FormStateInterface $form_state) {
    $form['new_file'] = array(
      '#type' => 'file',
      '#title' => 'A file',
    );

    $form['actions']['#type'] = 'actions';
    $form['actions']['submit'] = array(
      '#type' => 'submit',
      '#value' => $this->t('Upload'),
      '#button_type' => 'primary',
    );
    return $form;
  }

  private function _grep_to_log($str, $data, $c_b, $c_a) {
    if (preg_match_all(
      "~((?:[^\n]*\n){0,$c_b})[^\n]*$str.[^\n]*\n((?:[^\n]*\n){0,$c_a})~",
       $data, $arr)) {
      error_log(print_r($arr[0],TRUE));
    }
  }

  public function submitForm(array &$form, FormStateInterface $form_state) {
    #$data = print_r($form_state, TRUE);
    #$str = '\[input.protected';
    #$this->_grep_to_log($str, $data, 3, 10);

    $file = NULL;
    $ret = file_save_upload('new_file', array(), 'public://field/files/');
    #$ret = file_save_upload('new_file', array('file_validate_extensions'=>array(0=>'jpeg jpg gif png')), 'public://field/proof/');
    if (is_array($ret) && is_object($ret[0])) {
      $file = $ret[0];
    }
    else {
      error_log(print_r($ret, TRUE));
      if (is_array($ret)) {
        error_log('$ret is an array of length '.count($ret));
      }
      $uuid = 0;
      $uri = '';
      $error = 'No useful file retrieved. Bug in uploading script? (Tip: Check form key of the binary.)';
      error_log($error); 
      die('{ "uri" : "", "uuid" : "0", "error" : "'. $error .'"}');
    }

    $user = \Drupal::currentUser();
    if (!$user->isAuthenticated()) {
      if ($_SERVER['PHP_AUTH_USER']) {
        $user = user_load_by_name($_SERVER['PHP_AUTH_USER']);
      } else {
        die('{"error" : "Login required", "uuid" : "0", "uri" : "" }');
      }
    }
    $uid = $user->id();

    $file->setOwnerId($uid);
    $file->save();
    $uuid = $file->uuid();
    $uri = $file->destination;
    $fid = $file->id();

    $return_data = \Drupal::service('serializer')->serialize($file, 'json');

    print json_encode($return_data);
    exit;
  }

}

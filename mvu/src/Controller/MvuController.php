<?php
/**
 * Created by PhpStorm.
 * User: victor.unda
 */

namespace Drupal\mvu\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Database\Database;
use Symfony\Component\HttpFoundation\Response;

class MvuController extends ControllerBase {

  /**
   * @return array
   */
  public function mvlist() {

    /**
     * Marging two functions. See below the functions:  retrieveArrayOne() AND retrieveArrayTwo()
     *
     * function drupal_get_path(): returns the path to a system item(module, theme, etc.)
     * function base_path(): returns the base URL path (directory).
     */
    $retrieveData = $this->mergeTwoArrays();
    $content = "<H2></H2>";
    foreach ($retrieveData as $key1 => $finalValues) {

      if ($key1 == 0) {
        foreach ($finalValues as $disCrop) {
          $content .= "<details>";
          $content .= '<summary class="crop-title" crop-value="'.$disCrop.'">';
          $content .= $disCrop;
          $content .= '  <img class="loading" src="' . base_path(). drupal_get_path('module', 'mvu') . '/images/26.gif" alt="Loading data"/>';
          $content .= '</summary>';
          $content .= "</details>";


        }
      }
    }

    return array(
      '#type' => 'markup',
      '#markup' => $content,
      '#attached' => array(
        'library' => array(
          'mvu/mvu'
        ),
      ),
    );

  }

  /**
   * @return string
   */
  public function loadCultivar() {
    $cropValue = $_POST['cropValue'];

    $query = \Drupal::entityQuery('node');
    $query->condition('status',1);
    $query->condition('type','mlfruitandnut');
    $query->sort('title', 'ASC');
    $entity = $query->execute();
    $content = '<div class="crop-detail"><p class="dt-body-justify" style="width:80%">';


    foreach ($entity as $n) {
      $node = \Drupal\node\Entity\Node::load($n);
      if($node->get('field_mlfruitandnut_crop')->getValue()[0]['value'] == $cropValue) {
        $content .= '<div class="media">';
        $content .= '<div class="media-body">';
        $content .= '<h4 class="media-heading">' . $node->get('title')->value . '</h4>';
        $bodySeeValue = $node->get('body')->value;
        /**
         * Searching for "See " and space...
         * strpos — Find the position of the first occurrence of a substring in a string
         */
        $valueComp = "See ";
        $FinalValueCompBody = strpos($bodySeeValue, $valueComp);
        if($FinalValueCompBody === false) {
          $content .= $node->get('body')->value;
          $content .= '<p></p>';
          $content .= '<a href="#0" class="cd-top js-cd-top">Top</a>';
        }
        else {

          //$content .= '<p>'.$secondlevelCrop['body'].'</p>';
          //$content .= '<a href="/node/' . $secondlevelCrop['nid']. '" target="_blank">';
          //$content .= $secondlevelCrop['body'] .'</a>';
          //$content .= "<p></p>";
        }

        $nidEntity_id = $node->get('nid')->value;
        $dataTitle = $node->get('title')->value;
        $str = preg_replace('/\(([^\)]*)\)/', '', $dataTitle);
        $field_linking = $node->get('field_link_the_site')->value;

        if(isset($field_linking) || $FinalValueCompBody === False) {
          $connection = \Drupal::database();
          $query = $connection->query("SELECT * FROM {node__field_link_the_site} INNER JOIN node_field_data ON node_field_data.title = node__field_link_the_site.field_link_the_site_value WHERE node_field_data.type = node__field_link_the_site.bundle");
          $resultRecords = $query->fetchAll();

          foreach ($resultRecords as $obj) {
            if($nidEntity_id == $obj->entity_id) {
              $content .= '<a href="/node/' .$obj->nid. '" target="_blank">';
              $content .= "<b>See: </b>" .$obj->field_link_the_site_value .'</a>';
              $content .= "<p></p>";
              $content .= '<a href="#0" class="cd-top js-cd-top">Top</a>';
            }
          }
        }
        else {
          $content .= $this->t("<mark>Please, add the correct name to link a Cultivar/s. " .$bodySeeValue. "</mark>");
        }

        $content .= '</div>';
        $content .= '</div>';
      }
    }
    $content .= '</div>';
    $response = new Response();
    // Valid types are strings, numbers, null, and objects that implement a __toString() method.
    $response->setContent(json_encode(array('data' => $content)));
    /**
     * https://www.drupal.org/docs/8/modules/jsonapi/creating-new-resources-post
     */
    $response->headers->set('Content-Type', 'application/json'); // to read json data. headers are required on all POST request to get a proper JSON:API request and response..
    return $response;

  }
  /**
   * @return string
   */
  public function retrieveArrayOne() {

    $query = \Drupal::entityQuery('node');
    $query->condition('status',1);
    $query->condition('type','mlfruitandnut');
    $query->sort('title', 'ASC');
    $entity = $query->execute();
    $options = array();

    foreach ($entity as $n) {
      $node = \Drupal\node\Entity\Node::load($n);
      $options[$node->id()] = $node->getTitle();
      $elementA[] = [//
        'nid' => $node->get('nid')->value,
        'title' => $node->get('title')->value,
        'body' => $node->get('body')->value,
        'field_link_the_site' => $node->get('field_link_the_site')->value,
        'field_mlfruitandnut_crop' => $node->get('field_mlfruitandnut_crop')->value,
      ];
    }

    return $elementA;


  }

  /**
   * @return array
   */
  public function retrieveArrayTwo() {
    $query = \Drupal::entityQuery('node');
    $query->condition('status',1);
    $query->condition('type','mlfruitandnut');
    $query->sort('field_mlfruitandnut_crop', 'ASC');
    $entity = $query->execute();
    $options = array();
    foreach ($entity as $n) {
      $node = \Drupal\node\Entity\Node::load($n);
      $options[$node->id()] = $node->get('field_mlfruitandnut_crop')->value;
    }

    $arrayUniqueRecord = array_unique($options, SORT_REGULAR);

    return $arrayUniqueRecord;

  }

  /**
   * @return array
   */
  public function mergeTwoArrays() {

    $array1 = array($this->retrieveArrayTwo());
    $array2 = array($this->retrieveArrayOne());
    $resultArrays = array_merge($array1, $array2);
    return $resultArrays;
  }

}

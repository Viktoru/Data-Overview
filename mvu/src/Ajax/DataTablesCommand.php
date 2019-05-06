<?php
namespace Drupal\mvu\Ajax;

use Drupal\Core\Ajax\CommandInterface;

/**
 * Class DataTablesCommand
 *
 * @package Drupal\mvu\Ajax
 */
class DataTablesCommand implements CommandInterface {
  protected $content;
  // Constructs a ReadMessageCommand object.
  public function __construct($content) {
    $this->content = $content;
  }
  // Implements Drupal\Core\Ajax\CommandInterface:render().
  public function render() {
    return array(
      'command' => 'customDataTables',
      'content' => $this->content,
    );
  }
}
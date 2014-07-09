<?php
/**
 * @package		MiwoEvents
 * @copyright	2009-2014 Miwisoft LLC, miwisoft.com
 * @license		GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */
# No Permission
defined('MIWI') or die ;

$param = null;

//Load greybox lib
?>

<link href="<?php echo MURL_MIWOEVENTS; ?>/site/assets/css/stylesheet.css" rel="stylesheet" type="text/css" />
<div id="container_oc">
<div id="content_oc">
  <div class="box_oc">
  <div class="box-heading"><h1 class="miwoshop_heading_h1"><?php echo $this->data['heading_title']; ?></h1></div>
  <div class="box-content">
  <?php echo $this->data['text_message']; ?>
  <div class="buttons">
    <div class="right"><a href="<?php echo $this->data['continue']; ?>" class="button button-primary"><?php echo $this->data['button_continue']; ?></a></div>
  </div>
  </div>
  </div>
  </div>
  </div>

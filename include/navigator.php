<?php
  $href_string = 'href="?page_id=' . "%d" . ($query ? "&query=" . $query : "") . ($rated ? "&rated=" . $rated : "") . '"';
  // Left button.
  $left_title = $rated ? _("Higher") : _("Newer");
  if($page_id <= 1) {
    $left_flag = "disabled";
    $left_href = "";
  } else {
    $left_flag = "";
    $left_href = sprintf($href_string, $page_id - 1);
  }
  // Right button.
  $right_title = $rated ? _("Lower") : _("Older");
  if($page_id >= $pages_total) {
    $right_flag = "disabled";
    $right_href = "";
  } else {
    $right_flag = "";
    $right_href = sprintf($href_string, $page_id + 1);
  }
  include ('./templates/pager.php');
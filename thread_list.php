<?php
	include_once './connect_db.php';

	$sql = "SELECT * FROM threads";
	$result = mysqli_query($link, $sql);
	if (!$result) {
	    die(' Incorrect query: ' . mysql_error());
	}
	
	while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
	  if(strlen($json) > 0) {
	    $json .= ', ';
	  }
	  $json .= json_encode($row);
	}
	
	mysqli_free_result($result);
	
	$json = '{"threads": ['.$json.']}';
	
	echo $json;

	mysqli_close($link);
?>

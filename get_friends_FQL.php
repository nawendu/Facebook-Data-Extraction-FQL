<?php

  include_once "facebook.php";

  $app_id = 'your app id'; //  Update your app id
  $app_secret = 'your app seceret'; //  Update your app id
  $my_url = 'http://your URL here/get_friends_FQL.php' // Give absolute path of current file

  $code = $_REQUEST["code"];
  
  
  	function db_insert($insert_query)
		{
		// Instantiate the mysqli class
		$mysqli = new mysqli();
		

//		Provide your DB credentials here

		// Connect to the database server and select a database
		$mysqli->connect('your db server', 'your db id', 'your password', 'your db name');
	
		//Execute query 

		$result = $mysqli->query($insert_query);

		//commit
		$commit = $mysqli->commit();
	
	    	// Close the connection
    		$mysqli->close();    		
 		
		}

?>

<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" xmlns:fb="http://www.facebook.com/2008/fbml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
        <title>Purple BI: Facebook Data Extraction & Analytics </title>
</head> 
<body> 
	<h2>Facebook Data Extraction using FQL</h2> 
	
	<h3>Retrieves and saves friends details to database</h3> 

<?php


 
 //auth user
 if(empty($code)) {
    $dialog_url = 'https://www.facebook.com/dialog/oauth?client_id=' 
    . $app_id . '&redirect_uri=' . urlencode($my_url) ;
    echo("<script>top.location.href='" . $dialog_url . "'</script>");
  }

  //get user access_token
  $token_url = 'https://graph.facebook.com/oauth/access_token?client_id='
    . $app_id . '&redirect_uri=' . urlencode($my_url) 
    . '&client_secret=' . $app_secret 
    . '&code=' . $code;
  $access_token = file_get_contents($token_url);
 
  // Run fql query
  $fql_query_url = 'https://graph.facebook.com/'
    . '/fql?q=SELECT+uid,+first_name,+last_name+FROM+user+WHERE+uid+in+(SELECT+uid2+FROM+friend+WHERE+uid1=me())'
    . '&' . $access_token;
  $fql_query_result = file_get_contents($fql_query_url); // Reading file output to a string
  $fql_query_obj = json_decode($fql_query_result, true); // Converting string to an array

  $count = count($fql_query_obj[data]);

  print_r("Number of friends retrieved : ");

  print_r($count);


//      Prepare insert query string	
	$query = "INSERT INTO friends_name(first_name, last_name, uid) VALUES ('Nawendu','Bharti', 1),";
	
	for ($i = 0; $i < $count; $i++) {
    	$query .= '(' . "'" . $fql_query_obj[data][$i]['first_name'] . "'" . ',' . "'" . $fql_query_obj[data][$i]['last_name'] . "'" . ',' . $fql_query_obj[data][$i]['uid'] . ')' ;
    	  	if ( $i !==$count -1 ) $query.= ', '; 
		}

// Making a call to insert function
	db_insert($query);


	echo '<a href="http://nawendubharti.com"><p>Go back to Purple BI</a>';

?>

</body> 
</html>

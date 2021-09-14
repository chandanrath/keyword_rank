<?php



$gstart_time = microtime(true); 
ini_set('display_errors', 1); ini_set('display_startup_errors', 1); error_reporting(E_ALL); 

//You can download this file from here https://api.dataforseo.com/_examples/php/_php_RestClient.zip
require('connect.php');


function _in_logit_POST($id_message, $data, $taskid) {
	@file_put_contents(__DIR__ . "/data/postdata_".$taskid.".txt", json_encode($data), FILE_APPEND);
}


$post_data_in = file_get_contents('php://input');




if (!empty($post_data_in)) {
    
	$post_arr = json_decode(gzdecode($post_data_in), true);
	
	
	// you can find the full list of the response codes here https://docs.dataforseo.com/v3/appendix/errors
	
	if (isset($post_arr['status_code']) AND $post_arr['status_code'] === 20000) {
	    
	    $taskid = $post_arr['tasks'][0]['id'];
	    
		_in_logit_POST("result", $post_arr,$taskid);
	
		
		//do something with results
		
		// status=2 means got reaponse of data and inserted into data/.txt  //
		
		$update2 = "update tbl_keywords set status=2 where taskid='".$taskid."'";	// user data for post api //
        mysqli_query($conn, $update2);
		
		
		echo "ok";
	
	} else {
	    
		//_in_logit_POST('error decode', $post_data_in);
		
		echo "error";
	}
	
} else {
	echo "empty POST";
}


    
?>

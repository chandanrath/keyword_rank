<?php

$start_time = microtime(true); 

ini_set('display_errors', 1); ini_set('display_startup_errors', 1); error_reporting(E_ALL); 

ini_set ( 'max_execution_time', 0); 



require('connect.php');

require('RestClient.php');

$created_date	  = date('Y-m-d H:i:s');

$api_url        = 'https://api.dataforseo.com/';



$server = 1;

if($server==1)
{
    // authenticate dataforseo login & password//
    
    try {
        
    //Instead of 'login' and 'password' use your credentials from https://my.dataforseo.com/#api_dashboard
  
    
    $client = new RestClient($api_url, NULL, 'sumeet@rathinfotech.com', '0e58c3bb0deccf3b');

	

    } catch (RestClientException $e) {
        echo "\n";
        print "HTTP code: {$e->getHttpCode()}\n";
        print "Error code: {$e->getCode()}\n";
        print "Message: {$e->getMessage()}\n";
        print  $e->getTraceAsString();
        echo "\n";
        exit();
    }
}

    
    $Arrkeywords = array();
	
    
    $keySql ="SELECT keyid,keywords FROM tbl_keywords WHERE status=0 order by keyid ASC limit 0,10";
    $resData = mysqli_query($conn,$keySql);
    $numRows = mysqli_num_rows($resData);	
    
    if($numRows > 0)
	{
		while($DataVal = mysqli_fetch_array($resData))
		{
			$Arrkeywords[$DataVal['keyid']] = $DataVal['keywords'];
			
			$arrKeyId[] = $DataVal['keyid'];
		
		}
		
			$ArrKeyId = "'".implode("'".','."'",$arrKeyId)."'";
			
		
			$update = "update tbl_keywords set status=6 where keyid in(".$ArrKeyId.")";	
        	mysqli_query($conn, $update);
	}
	else
    {
       echo"No keywords found!";
       exit;
    }
    
    //echo"<pre>Arrkeywords==";print_r($Arrkeywords); //exit;
   
    foreach($Arrkeywords as $keyid=> $keyVal)
    {
        
        $post_array = array();
	   
		// your unique ID. we will return it with all results. you can set your database ID, string, etc.
		
		$my_unq_id = mt_rand(0, 30000000); 
	
    	
       $post_array[$my_unq_id] = array(
            "language_name"     => "English",
            "location_name"     => "Mumbai,Maharashtra,India",	
            "keyword"           => mb_convert_encoding(urlencode($keyVal), "UTF-8"),
            "priority"          => 1,
            "tag"               => "keyword_rank_".$my_unq_id,
            "postback_data"     => "advanced",
        	"postback_url"      => 'http://164.52.209.32/~rathtest/v3/postback_url.php?id=$id&tag=$tag'
        );
        
        
        if (count($post_array) > 0)
		{
        		

			try {
        		// POST /v3/serp/google/organic/task_post
        		// in addition to 'google' and 'organic' you can also set other search engine and type parameters
        		// the full list of possible parameters is available in documentation
        		
        		$result = $client->post('/v3/serp/google/organic/task_post', $post_array);
        		
        	    //echo"<pre>res==";print_r($result);
        		
        		$taskid=  $result['tasks'][0]['id'];
        		
        		$status_code=  $result['tasks'][0]['status_code'];
        		
        		if($status_code=='20100')
        		{
        		    // status=1 means request for  data //
        		
        		    $update2 = "update tbl_keywords set status=1,taskid='".$taskid."' where keyid='".$keyid."'";	// user data for post api //
        			mysqli_query($conn, $update2);
        		}
        		
        		// do something with post result
        	} catch (RestClientException $e) {
        		echo "\n";
        		print "HTTP code: {$e->getHttpCode()}\n";
        		print "Error code: {$e->getCode()}\n";
        		print "Message: {$e->getMessage()}\n";
        		print  $e->getTraceAsString();
        		echo "\n";
        	}
		
		}
		
		if(!empty($taskid))
    	{
    	
    		echo"<li>".$keyVal." - ".$taskid." have been Posted .";
    	}
    	else
    	{
    		echo"<li>".$keyVal." - have been not Posted";
    	}
            
            
       
     }
    
    
    
    //$client = NULL;
    $end_time = microtime(true); 
      
    // Calculate script execution time 
    $execution_time = ($end_time - $start_time); 
      
    echo " Execution time of script = ".$execution_time." sec <br/><br/>"; 
  
?>
<?php
//echo "start";

ini_set('display_errors', 1); ini_set('display_startup_errors', 1); error_reporting(E_ALL); 
require('connect.php');

ini_set ( 'max_execution_time', 0); 

// status=2 means data is inserted into DB //

$keySql ="SELECT keyid,taskid FROM tbl_keywords where status=2";
$resData = mysqli_query($conn,$keySql);
$numRows = mysqli_num_rows($resData);	

if($numRows > 0)
{
	while($DataVal = mysqli_fetch_array($resData))
	{
 		$taskidpen  = $DataVal['taskid'];
 		$key_id     = $DataVal['keyid'];
 		
		$path= __DIR__ ."/data/postdata_".$taskidpen.".txt";
		
    	//echo $path;
    	//exit;
    
        $json_data = file_get_contents($path);
       
        
        if ($json_data==FALSE)
        {
        	echo "error to open file of postdata_".$taskidpen;
        }
        
        $jsonData = json_decode($json_data,true);
       
       
        $sqlMain = array();
        $sqlPaid = array();
        
        if (isset($jsonData['status_code']) AND $jsonData['status_code'] === 20000)
        {
            
            //echo"code==".$jsonData['status_code'];
            
            foreach($jsonData['tasks'] as $tasks)
            {
                if(isset($tasks['result']))
                {
                    foreach($tasks['result'] as $resdata)
                    {
                        //echo"<pre>DATA==";print_r($resdata);
                        
                        $post_key = $resdata['keyword'];
                        $check_url = $resdata['check_url'];
                        $datetime = date("Y-m-d H:i:s",strtotime($resdata['datetime']));
                        
                        $update1 = "update tbl_keywords set checkurl='".$check_url."' where taskid='".$taskidpen."' and keyid='".$key_id."'";
    		            mysqli_query($conn, $update1);
                        
                        if(isset($resdata['items']))
                        {
                            foreach($resdata['items'] as $value)
                            {
                                //echo"<li>domain==".$value['domain'];
                               
                                if($value['type']=="organic")
                                {
                                    $taskid 	= $taskidpen;
                            		$url 		= mysqli_real_escape_string($conn,$value['url']);
                            		$rootdomain = beliefmedia_get_domain($value['url']);
                            		$title      = mysqli_real_escape_string($conn,$value['title']);
                            		
                            	
                            		// optional//
                            		
                            		if($value['rank_absolute'] <= 30)
                            		{
                            		    $position 	= $value['rank_absolute'];
										$rank_group 	= $value['rank_group'];
										
										$sqlMain[] = "('".$taskid."','".$post_key."','".addslashes($title)."','".$position."','".$rank_group."','".$rootdomain."','".addslashes($url)."',1,'".$datetime."')";
                            		}
                            	
                                }
                                
                            }
                        }
                    }
                }
            }
        }
        
      
        
        if(!empty($sqlMain)) 
        {
        
             $insQuery = 'INSERT INTO tbl_keyword_details1 (taskid,post_key,title,position,rankgroup,domain,url,status,result_date) VALUES '.implode(',', $sqlMain);
        
            $result1 = mysqli_query($conn,$insQuery);
          
        }
        
        
        
        if(!$result1)
        {
		    $update2 = "update tbl_keywords set status=2 where taskid='".$taskidpen."' and keyid='".$key_id."'";
		    mysqli_query($conn, $update2);
	    }
	    else
    	{
    	    // status=3 means data have inserted into DB  //
    	    
    		$update2 = "update tbl_keywords set status=3 where taskid='".$taskidpen."' and keyid='".$key_id."'";
    		mysqli_query($conn, $update2);
    
            // delete taskid txt file //
    		//$deletepath="data/".$json_data['results']['organic'][0]['task_id'].".txt";
    		//unlink($deletepath);
    	}
        
       // exit;

    


    echo "<br/>".$taskidpen." has been inserted <br>";


	}
}
else
{
    echo" No data found to inerted into DB";
    exit;

    
}

echo "<br/>DONE ALL";

function beliefmedia_get_domain($url, $tld = false) {
  $pieces = parse_url($url);
  $domain = isset($pieces['host']) ? $pieces['host'] : '';
  if (preg_match('/(?P<domain>[a-z0-9][a-z0-9\-]{1,63}\.[a-z\.]{2,6})$/i', $domain, $m)) {
    return ($tld === true) ? substr($m['domain'], ($pos = strpos($m['domain'], '.')) !== false ? $pos + 1 : 0) : $m['domain'];
  }
 return false;
}



?>
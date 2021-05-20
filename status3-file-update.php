<?php
//echo "start";

ini_set('display_errors', 1); ini_set('display_startup_errors', 1); error_reporting(E_ALL); 
require('connect.php');

ini_set ( 'max_execution_time', 0); 


$keySql ="SELECT keyid,taskid FROM tbl_keywords where status=2 order by keyid desc";	//and livekey_id=0 
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
        	echo "error in opening file";
        }
        
        $jsonData = json_decode($json_data,true);
       
        // echo"<pre>decode==";print_r($jsonData);
        
        

        
        $sqlorg = array();
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
                        
                        $update1 = "update tbl_keywords set checkurl='".$check_url."',created_date='".$datetime."' where taskid='".$taskidpen."' and keyid='".$key_id."'";
    		            mysqli_query($conn, $update1);
                        
                        if(isset($resdata['items']))
                        {
                            foreach($resdata['items'] as $value)
                            {
                                //echo"<li>domain==".$value['domain'];
                               
                                if($value['type']=="organic")
                                {
                                    $taskid 	= $taskidpen;
                            		$absolute 	= $value['rank_absolute'];
                            		$position 	= $value['rank_group'];
                            		$url 		= mysqli_real_escape_string($conn,$value['url']);
                            		$rootdomain = beliefmedia_get_domain($value['url']);
                            		$breadcrumb = str_replace(" › "," &gt; ", $value['breadcrumb']);
                            		$title      = mysqli_real_escape_string($conn,$value['title']);
                            		
                            	
                            	
                            		
                            		$sqlorg[] = "('".$taskid."','".$post_key."','".$title."','".$position."','".$absolute."','".$rootdomain."','".$url."',1,'".$datetime."')";
                                }
                                /*else if($value['type']=="paid")
                                {
                                    $taskid 	= $taskidpen;
                            		$position 	= $value['rank_group'];
                            		$url 		= mysqli_real_escape_string($conn,$value['url']);
                            		$rootdomain = beliefmedia_get_domain($value['url']);
                            		$breadcrumb = str_replace(" › "," &gt; ", $value['breadcrumb']);
                            		$title      = mysqli_real_escape_string($conn,$value['title']);                           		
                            		
                            		
                            		$sqlpaid[] = "('".$taskid."','".$post_key."','".$title."','".$position."','".$rootdomain."','".$url."',1,'".$datetime."')";
                                
                                }*/
                        		
                        		
                            }
                        }
                    }
                }
            }
        }
        
        //echo"<pre>sql==";print_r($sql);
        
       // exit;
        
        //$insQuery = 'INSERT INTO tbl_keyword_details (taskid,post_key,title,position,domain,url,breadcrumb,snippet,highlighted,status,result_date) VALUES '.implode(',', $sql);
        
        if(!empty($sqlorg)) 
        {
        
            $insQuery = 'INSERT INTO tbl_keyword_details (taskid,post_key,title,position,absolute,domain,url,status,result_date) VALUES '.implode(',', $sqlorg);
        
            $result = mysqli_query($conn,$insQuery);
        }
        
       /*else if(!empty($sqlpaid)) 
        {
           $insQuery = 'INSERT INTO tbl_keyword_details_paid (taskid,post_key,title,position,domain,url,status,result_date) VALUES '.implode(',', $sqlpaid);
        
            $result1 = mysqli_query($conn,$insQuery); 
        }*/
        
        
        if(!$result)
        {
		    $update2 = "update tbl_keywords set status=2 where taskid='".$taskidpen."' and keyid='".$key_id."'";
		    mysqli_query($conn, $update2);
	    }
	    else
    	{
    		$update2 = "update tbl_keywords set status=3 where taskid='".$taskidpen."' and keyid='".$key_id."'";
    		mysqli_query($conn, $update2);
    
            // delete taskid txt file //
    		//$deletepath="data/".$json_data['results']['organic'][0]['task_id'].".txt";
    		//unlink($deletepath);
    	}
        
       // exit;

// for related //




    echo "<br/>".$taskidpen."<br>";


	}
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
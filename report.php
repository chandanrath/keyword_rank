<?php
//phpinfo();exit;

ini_set('display_errors', 1); ini_set('display_startup_errors', 1); error_reporting(E_ALL); 

ini_set ( 'max_execution_time', 0); 


// check server is on //

require('connect.php');

$created_date	  = date('Y-m-d H:i:s');

    $gstart_time = microtime(true); 

   //$CountStatus3 = GetCountFor3($conn);
    
    //$CountKeywords = GetCountKeywords($conn);
    
    //if($CountStatus3 == $CountKeywords)
    //{
        //$statustop10 = TopKeywordsStatus($conn,'for10');    // if status==0//
        
        //if($statustop10=="no")
       // {
                //$getTop10  = GetTop10($conn);
       // }
        
     
       
       // $statustop30 = TopKeywordsStatus($conn,'for30');    // if status==0//
        
       // if($statustop30=="no")
       // {
                // $getTop30  = GetTop30($conn);
       // }
      
        // makr zip for top10/30
        
        //$indivisualClnt = TopKeywordsStatus($conn,'indiv'); // if status==0//
        
       
          //   $getIndivisual  = GetIndivisualClient($conn);
      
       
       
        
       // $focusKey = TopKeywordsStatus($conn,'focus');   // if status==0//
        
       
                $focusKeyword  = GetFocusKeywords($conn);
       
        
        exit;
    
        
        // rename the table//
        
        //$sqlRename = "RENAME TABLE `tbl_keywords_details` TO `tbl_keywords_details_may`";
        //$res = mysqli_query($conn,$sqlRename);
        
        //$createTable = CreateNewTable($conn);
        
        // ti,e calculate to each focus keyword reports//
        $end_time = microtime(true); 
          
        // Calculate script execution time 
        $execution_time = ($end_time - $gstart_time); 
          
        echo "<li> Execution time of Report = ".$execution_time." sec <br/><br/>"; 
 
    
    //}


echo "<br/>DONE ALL";

function beliefmedia_get_domain($url, $tld = false) {
  $pieces = parse_url($url);
  $domain = isset($pieces['host']) ? $pieces['host'] : '';
  if (preg_match('/(?P<domain>[a-z0-9][a-z0-9\-]{1,63}\.[a-z\.]{2,6})$/i', $domain, $m)) {
    return ($tld === true) ? substr($m['domain'], ($pos = strpos($m['domain'], '.')) !== false ? $pos + 1 : 0) : $m['domain'];
  }
 return false;
}

/*
function CreateNewTable($conn)
 {
	$sql1 = " DROP TABLE IF EXISTS `tbl_keyword_details`;";
	$res1 = mysqli_query($conn,$sql1);
	$sql2 = "CREATE TABLE `tbl_keyword_details` (
	  `id` int(11) NOT NULL,
	  `taskid` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
	  `post_key` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
	  `title` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
	  `position` int(11) DEFAULT 0,
	  `rankgroup` int(11) NOT NULL DEFAULT 0,
	  `domain` text COLLATE utf8_unicode_ci DEFAULT NULL,
	  `url` text COLLATE utf8_unicode_ci DEFAULT NULL,
	  `status` tinyint(1) NOT NULL DEFAULT 0,
	  `result_date` datetime DEFAULT '0000-00-00 00:00:00'
	) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;";
	$res2 = mysqli_query($conn,$sql2);

	$sql3 = "ALTER TABLE `tbl_keyword_details`
	  ADD PRIMARY KEY (`id`),
	  ADD KEY `taskid` (`taskid`),
	  ADD KEY `post_key` (`post_key`),
	  ADD KEY `position` (`position`);";
	 $res3 = mysqli_query($conn,$sql3);


	$sql4 = "ALTER TABLE `tbl_keyword_details`
	  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;";
	  $res4 = mysqli_query($conn,$sql4);
 }*/


function TopKeywordsStatus($conn,$type)
{
    try {
        
            if($type="for10")
            {
                $chkStatus ="SELECT count(*) topcnt FROM tbl_keywords_status where top10=1 and DATE_FORMAT(update_date,'Y-m-d')=  '".date('Y-m-d')."'";
            }
            if($type="for30")
            {
                $chkStatus ="SELECT count(*) topcnt FROM tbl_keywords_status where top30=1 and DATE_FORMAT(update_date,'Y-m-d')=  '".date('Y-m-d')."'";
            }
            if($type="indiv")
            {
                $chkStatus ="SELECT count(*) topcnt FROM tbl_keywords_status where client_report=1 and DATE_FORMAT(update_date,'Y-m-d')=  '".date('Y-m-d')."'";
            }
            
            if($type="focus")
            {
                $chkStatus ="SELECT count(*) topcnt FROM tbl_keywords_status where focus_report=1 and DATE_FORMAT(update_date,'Y-m-d')=  '".date('Y-m-d')."'";
            }
            
            //echo"<li>". $chkStatus;
            
            $resStatus = mysqli_query($conn,$chkStatus);
            $numRows = mysqli_num_rows($resStatus);	
            
            $statusCnt=mysqli_fetch_array($resStatus);
            
            if($statusCnt['topcnt'] ==0 )
            {
               return "no";
            }
            else
            {
               return "yes"; 
            }
    
    } catch (RestClientException $e) {
        		echo "\n";
        		print "HTTP code: {$e->getHttpCode()}\n";
        		print "Error code: {$e->getCode()}\n";
        		print "Message: {$e->getMessage()}\n";
        		print  $e->getTraceAsString();
        		echo "\n";
        	}
}
 
function GetCountFor3($conn)
{
    $dataCount ="SELECT count(*) keycnt FROM tbl_keywords where status=3";
    $resCnt = mysqli_query($conn,$dataCount);
    $numRows = mysqli_num_rows($resCnt);	
    $DataCnt = mysqli_fetch_array($resCnt);
    
    return $DataCnt['keycnt'];
}
function GetCountKeywords($conn)
{
    $KeyCount ="SELECT count(*) keycnt FROM tbl_keywords where status=3 ";
    $resCnt = mysqli_query($conn,$KeyCount);
    $numRows = mysqli_num_rows($resCnt);	
    $DataCnt = mysqli_fetch_array($resCnt);
    
    return $DataCnt['keycnt'];
}

function GetTop10($conn)
{
    $header="";
    $start_time = microtime(true); 
    $upload_dir = "excel/";
		
	if(!is_dir($upload_dir)) {

		mkdir($upload_dir);
	}
		
    
    $sql10 = "SELECT domain ,count(*) as keycnt FROM `tbl_keyword_details` where position<=10 GROUP BY `domain` order by keycnt DESC";
    $resCnt10 = mysqli_query($conn,$sql10);
    $numRows = mysqli_num_rows($resCnt10);	
    
    if($numRows > 0)
    {
        try {
            
            $filename = 'keywordsTop10-'.date('Y-m-d').'.csv';
           
            $header ="Sr No, Domain, Count \n";
        	$i=1;
        	while($spdata=mysqli_fetch_array($resCnt10))
        	{
        	   
    			$header .=$i.",".$spdata["domain"].",".$spdata["keycnt"]."\n";
        	    
        	    $i++;
        	}
        	
           $Newfilename =  $upload_dir.'/'.$filename;
    	   
    	    $csv_handler = fopen ($Newfilename,'w');
    		
    		fwrite ($csv_handler,$header);
    		
    	    $filesize = filesize($Newfilename); // get file size//
    	
    		fclose ($csv_handler);
            
           /* if($filesize > 1)
            {
        		$insert = "insert into tbl_keywords_status(filename,top10,update_date) values('".$filename."',1,'".date('Y-m-d')."')";
            	mysqli_query($conn, $insert);
            }*/
    	
        } catch (RestClientException $e) {
        		echo "\n";
        		print "HTTP code: {$e->getHttpCode()}\n";
        		print "Error code: {$e->getCode()}\n";
        		print "Message: {$e->getMessage()}\n";
        		print  $e->getTraceAsString();
        		echo "\n";
        	}
	  
    }
    
    $end_time = microtime(true); 
          
    // Calculate script execution time 
    $execution_time = ($end_time - $start_time); 
      
    echo "<li> Execution time of top10 Reports = ".$execution_time." sec <br/><br/>"; 
}

function GetFileSize($csv_handler)
{
	$RowCount=0;

    while ((fgetcsv($csv_handler)) !== FALSE) 
    {
        $RowCount++;
    }
    
    return $RowCount;
		
}
function GetTop30($conn)
{
    $header="";
    $start_time = microtime(true); 
   
    $sql10 = "SELECT domain ,count(*) as keycnt FROM `tbl_keyword_details` where position<=30 GROUP BY `domain` order by keycnt DESC";
    $resCnt10 = mysqli_query($conn,$sql10);
    $numRows = mysqli_num_rows($resCnt10);	
    
    if($numRows > 0)
    {
        try
        {
            
            $filename = 'keywordsTop30-'.date('Y-m-d').'.csv';
            
            $upload_dir = "excel/";
    		
        	if(!is_dir($upload_dir)) {
        
        		mkdir($upload_dir);
        	}
           
            $header ="Sr No, Domain, Count \n";
        	$i=1;
        	while($spdata=mysqli_fetch_array($resCnt10))
        	{
        	   
    			$header .=$i.",".$spdata["domain"].",".$spdata["keycnt"]."\n";
        	    
        	    $i++;
        	}
        	
        	$Newfilename = $upload_dir.'/'.$filename;
    	   
    		$csv_handler = fopen ($Newfilename,'w');
    		
    		fwrite ($csv_handler,$header);
    		
    		$filesize = filesize($Newfilename); // get file size//
    		
    		fclose ($csv_handler);
            
           /* if($filesize > 1)
            {
        		$insert = "insert into tbl_keywords_status(filename,top30,update_date) values('".$filename."',1,'".date('Y-m-d')."')";
            	mysqli_query($conn, $insert);
            }*/
        	
        } catch (RestClientException $e) {
        		echo "\n";
        		print "HTTP code: {$e->getHttpCode()}\n";
        		print "Error code: {$e->getCode()}\n";
        		print "Message: {$e->getMessage()}\n";
        		print  $e->getTraceAsString();
        		echo "\n";
        	}
	  
    }
    
    $end_time = microtime(true); 
          
    // Calculate script execution time 
    $execution_time = ($end_time - $start_time); 
      
    echo "<li> Execution time of top30 Reports = ".$execution_time." sec <br/><br/>"; 
    
}

function GetIndivisualClient($conn)
{
    $domain= array();
   
    $sqlcmp = "SELECT domain,id FROM `tbl_domain` where status=1 and report=0 order by id DESC";
    $rescmp = mysqli_query($conn,$sqlcmp);
    $numRows = mysqli_num_rows($rescmp);	
    
    if($numRows > 0)
    {
        $start_time = microtime(true); 
        $upload_dir = "domain/";
		
		// create new main directory//
    	if(!is_dir($upload_dir)) {
    
    		mkdir($upload_dir);
    	}
        
        try 
        {
        	while($cmpdata=mysqli_fetch_array($rescmp))
        	{
    			$domain = $cmpdata['domain'];
    			
    			$filename = $cmpdata['domain'].'-'.date('Y-m-d').'.csv';
    			
    			// create sub directory //
    		/*	$Clientdir = $upload_dir.$cmpdata['domain']."/";
    			
    			if(!is_dir($Clientdir)) {
        
            		mkdir($Clientdir);
            	}
            	*/
            	
            	$Clientdir = $upload_dir;
            	$header='';
            	
    			//$cmpdata['domain']
    			
    			$sqlRept = "SELECT post_key,position FROM `tbl_keyword_details` WHERE domain='".$cmpdata['domain']."'";
    			$resRpt = mysqli_query($conn,$sqlRept);
                $numRpt = mysqli_num_rows($resRpt);
                if($numRpt > 0)
                {
                    $header ="Sr No, Keywords , Position \n";
                    $c=1;
                    while($clientData=mysqli_fetch_array($resRpt))
        	        {
        	           $header .= $c.",".$clientData["post_key"].",".$clientData["position"]."\n";
        	           
        	           $c++; 
        	        }
                
                
                    $Newfilename = $Clientdir.'/'.$filename;
        	   
        		    $csv_handler = fopen ($Newfilename,'w');
                    
                    
            		fwrite ($csv_handler,$header);
            		
            		$filesize = filesize($Newfilename); // get file size//
            		
            		fclose ($csv_handler);
            		
            		if($filesize > 1)
            		{
                		$updDomain = "update tbl_domain set report=1 where domain='".$cmpdata['domain']."' and id='".$cmpdata['id']."'";
            		    mysqli_query($conn, $updDomain);
            		}
        	    }
        	  
        	}
        	
        } catch (RestClientException $e) {
        		echo "\n";
        		print "HTTP code: {$e->getHttpCode()}\n";
        		print "Error code: {$e->getCode()}\n";
        		print "Message: {$e->getMessage()}\n";
        		print  $e->getTraceAsString();
        		echo "\n";
        	}
    	
    	
    	
    	// count of file name//
    	
     /*	$countFile = CountFileName($conn,$upload_dir);
    	
   	if($countFile > 0)
    	{
    	    $filecnt = $countFile;
    	}
    	
    	// make zip foile//
	  
	   $domainZip = MakeDomainZip($conn);
	   
	   if($domainZip=="completed")
	   {

		$insert = "insert into tbl_keywords_status(filename,client_report,filecnt,update_date) values('client_report',1,'".$filecnt."','".date('Y-m-d')."')";
    	mysqli_query($conn, $insert);
	   }*/
    	
    	 //$client = NULL;
        $end_time = microtime(true); 
          
        // Calculate script execution time 
        $execution_time = ($end_time - $start_time); 
          
        echo "<li> Execution time of Domain keywords = ".$execution_time." sec <br/><br/>"; 
	  
	  
    }
    
    
}

function CountFileName($conn,$dir)
{
    $directory = getcwd()."/".$dir;
    
    // Returns array of files
    $files1 = scandir($directory);
       
    // Count number of files and store them to variable
    $num_files = count($files1) - 2;
      
    return $num_files; 
}

function GetFocusKeywords($conn)
{
    
    $sqlkey = "SELECT keywords,keyid,focuscmp FROM `tbl_keywords` where status=3 and focuskey=1 order by keyid asc";
    $reskey = mysqli_query($conn,$sqlkey);
    $numKey = mysqli_num_rows($reskey);
    if($numKey > 0)
    {
        try
        {
            while($focus=mysqli_fetch_array($reskey))
        	{
        	    $start_time = microtime(true); 
        	    
        	    $focuscmp = $focus['focuscmp'];
        	    
        	    // get report //
        	    $getfocusReport = GetFocusReport($conn,$focuscmp,$focus['keywords']);
        	    
        	   
        	    // ti,e calculate to each focus keyword reports//
                $end_time = microtime(true); 
                  
                // Calculate script execution time 
                $execution_time = ($end_time - $start_time); 
                  
                echo "<li> Execution time of Focus Keywords = ".$execution_time." sec <br/><br/>"; 
                
                if($getfocusReport =="done")
        	    {
        	        $updKeyword = "update tbl_keywords set focuskey=2 where keywords='".$focus['keywords']."' and keyid='".$focus['keyid']."'";
    	            mysqli_query($conn, $updKeyword);
    	            
    	             //$makezip = MakeFocusKeyZip($conn,$focus['keywords']);
        	    }
        	    
        	    
        	    
        	    
        	}
        	
        } catch (RestClientException $e) {
        		echo "\n";
        		print "HTTP code: {$e->getHttpCode()}\n";
        		print "Error code: {$e->getCode()}\n";
        		print "Message: {$e->getMessage()}\n";
        		print  $e->getTraceAsString();
        		echo "\n";
        	}
        
    }
    
}


function GetFocusReport($conn,$focuscmp,$keywords)
{
    
    $Arrfocuscmp = explode(',',$focuscmp);
    
    $focuscomp = "'".implode("'".','."'",$Arrfocuscmp)."'";
    
    $header ="";
    
  echo"<li>".  $sqlkey = "SELECT domain,position FROM `tbl_keyword_details` WHERE post_key='".$keywords."' and domain in(".$focuscomp.") order by position desc";
    $reskey = mysqli_query($conn,$sqlkey);
    $numKey = mysqli_num_rows($reskey);
    
    if($numKey > 0)
    {
        try
        {
            $filename = str_replace(" ","_",$keywords).'-'.date('Y-m-d').'.csv';
            
            //$upload_dir = "focuskey/".$keywords."-".date('Ymd')."/";
            $upload_dir = "focuskey/";
            
            // create new directory//
        
            /*if(!is_dir($upload_dir)) {
            
            	mkdir($upload_dir);
            }
            */
            
            $header ="Sr No, Domain , Position \n";
            $c=1;
            while($clientData=mysqli_fetch_array($reskey))
            {
               $header .= $c.",".$clientData["domain"].",".$clientData["position"]."\n";
               
               $c++; 
            }
            
            $csv_handler = fopen ($upload_dir.'/'.$filename,'w');
    		
    		fwrite ($csv_handler,$header);
    		
    		fclose ($csv_handler);
    		
    		// total file count//
    	/*	$countFile = CountFileName($conn,$upload_dir);
    		if($countFile > 0)
        	{
        	    $filecnt = $countFile;
        	}
    		
    		$insert = "insert into tbl_keywords_status(filename,focus_report,filecnt,update_date) values('".$filename."',1,'".$filecnt."','".date('Y-m-d')."')";
        	mysqli_query($conn, $insert);
    	      */      
    		
    		return "done";
    		
        } catch (RestClientException $e) {
        		echo "\n";
        		print "HTTP code: {$e->getHttpCode()}\n";
        		print "Error code: {$e->getCode()}\n";
        		print "Message: {$e->getMessage()}\n";
        		print  $e->getTraceAsString();
        		echo "\n";
        	}
    
    }
}

function MakeFocusKeyZip($conn,$keywords)
{
    
     $start_time = microtime(true); 
    $sqlkey = "SELECT keywords FROM `tbl_keywords` WHERE focuskey='2' and status=3 order by keyid desc";
    $reskey = mysqli_query($conn,$sqlkey);
    $numKey = mysqli_num_rows($reskey);
    
    if($numKey > 0)
    {
        try
        {
            $keyword = str_replace(" ","_",trim($keywords));
            
            $pathdir = "focuskey/"; 
            
             // Create new zip class
            $zip = new ZipArchive;
            
            $zip_name = "focuskey/".$keyword.".zip"; 
            
             if($zip->open($zip_name, ZIPARCHIVE::CREATE)==TRUE){       // Opening zip file to load files
        	    // open zip folde to ceate//
            }
            
            $dir = opendir($pathdir);
            
            while($file = readdir($dir)) {
                    
            	//echo"<pre>file==";print_r($file);
            	
            	if(is_file($pathdir.$file)) {
            	    
            		$zip->addFile($pathdir.$file);
            	}
            	
            }
            
        } catch (RestClientException $e) {
        		echo "\n";
        		print "HTTP code: {$e->getHttpCode()}\n";
        		print "Error code: {$e->getCode()}\n";
        		print "Message: {$e->getMessage()}\n";
        		print  $e->getTraceAsString();
        		echo "\n";
        	}
        
    }
    
    
    $zip->close();
    
     // ti,e calculate to each focus keyword reports//
    $end_time = microtime(true); 
      
    // Calculate script execution time 
    $execution_time = ($end_time - $start_time); 
      
    echo "<li> Execution time of Focus keywords Zip = ".$execution_time." sec <br/><br/>"; 

}

function MakeDomainZip($conn)
{
    
    $start_time = microtime(true); 
    $sqlkey = "SELECT count(*) cnt FROM `tbl_domain` WHERE report='1' and status=1 limit 0,1";
    $reskey = mysqli_query($conn,$sqlkey);
    $numKey = mysqli_num_rows($reskey);
    $Counts = mysqli_fetch_array($reskey);
   
   //echo"ccvnt==".$Counts['cnt'];
    
    if($Counts['cnt'] > 0)
    {
        try
        {
            //$keyword = str_replace(" ","_",trim($keywords));
            
            $pathdir = "domain/"; 
            
           
            
             // Create new zip class
            $zip = new ZipArchive;
            
            
            $zip_name = "domain/domain-".date('Y-m-d').".zip";
            
             if($zip->open($zip_name, ZipArchive::CREATE)==TRUE){       // Opening zip file to load files
        	        //exit("cannot open <$filename>\n");
                }
            
            $dir = opendir($pathdir);
            
            while($file = readdir($dir)) {
                    
            	//echo"<pre>file==";print_r($file);
            	
            	if(is_file($pathdir.$file)) {
            	    
            		$zip->addFile($pathdir.$file);
            	}
            	
            }
            
        } catch (RestClientException $e) {
        		echo "\n";
        		print "HTTP code: {$e->getHttpCode()}\n";
        		print "Error code: {$e->getCode()}\n";
        		print "Message: {$e->getMessage()}\n";
        		print  $e->getTraceAsString();
        		echo "\n";
        	}
        
    }
    
    
    $zip->close();
    
    // ti,e calculate to each focus keyword reports//
    $end_time = microtime(true); 
      
    // Calculate script execution time 
    $execution_time = ($end_time - $start_time); 
      
    echo "<li> Execution time of Domain Zip = ".$execution_time." sec <br/><br/>"; 
    
    return "completed";

}

?>
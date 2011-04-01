<?php

	/**
     * Queue.Component.php
     *
     * @category   RCS Framework 
 	 * @package    Components
     * @subpackage Queue
 	 * @copyright  Copyright (c) 2009 RCS
     * @author RCS
     * @version 0.1.2
     */
	Class QueueComponent 
	{
		
		/**
		 * @var string $SandboxDirectory
		 */
		Public $SandboxDirectory;
		
		/**
		 * @var string $jobId
		 */
		Private $jobId;
		
		/**
		 * @var int $position
		 */
		Private $position;
		
		/**
		 * @var string $status
		 */
		Private $status;
		
		/**
		 * @var string $output
		 */
		Private $output;
		
		/**
		 * @var string $instanceIP
		 */
		Private $instanceIP;
		
		/**
         * Queue Object Constructor
         * 
         * @return void
         */
		Public Function __construct()
        {
        	if(func_num_args() > 0)
        	{
        		trigger_error('Sorry, but '.__CLASS__.'::'.__FUNCTION__.' does not accept any parameters', E_USER_ERROR);
        		
        	}//if(func_num_args() > 0)
        	
        	$IFConfig = `/sbin/ifconfig`;
			$Matches = array();
			preg_match('|inet addr:[0-9]+.[0-9]+.[0-9]+.[0-9]+|', $IFConfig, $Matches);
			@$this->instanceIP = $Matches[0];

        }//Public Function __construct()
        
        /**
         * CreatejobId Function ( Public )
         *
         * This is called first before submitting the envelope.
         * This generates your random 8 character long jobId.
         * 
         * @return string
         */
        Public Function CreatejobId($type = '', $user = null)
        {
        
        	$DBLink = mysql_connect(ABSDBHOST, ABSDBUSER, ABSDBPASS);
            mysql_select_db(ABSDBNAME, $DBLink);
            
        	
			$Salt = "abcdefghijklmnpqrstuvwxyzABCDEFGHIJKLMNPQRSTUVWXYZ1234567890_";
			srand((double)microtime()*1000000);
			$jobId = "";
			for ($i=0;$i<8;$i++)
			{
				$jobId = $jobId . substr ($Salt, rand() % strlen($Salt), 1);
			}
			$COLS = array('jobId');
			$VALS = array("'{$jobId}'");
			if(!empty($type))
			{
				$COLS[] = 'type';
				$VALS[] = "'$type'";
			}
			if(!empty($user))
			{
				$COLS[] = 'user__id';
				$VALS[] = $user;
			}
			$Query = "
			
			    INSERT INTO
			        bevomedia_queue
			        (".implode($COLS,', ').")
			    VALUES
			        (".implode($VALS,', ').")
			
			";
			        
			
			mysql_query($Query, $DBLink);
    		
    		$this->jobId = $jobId;
    		
			return $jobId;
        	
        }//Public Function CreatejobId()
        
        /**
         * Sendenvelope Function ( Public )
         *
         * @param string $jobId
         * @param string $envelope
         * @return void
         */
        Public Function Sendenvelope($jobId, $envelope)
        {
        	
        	$this->jobId = $jobId;
        	
        	$DBLink = mysql_connect(ABSDBHOST, ABSDBUSER, ABSDBPASS);
            mysql_select_db(ABSDBNAME, $DBLink);
            
            $envelope = mysql_real_escape_string($envelope, $DBLink);
            $jobId = mysql_real_escape_string($jobId, $DBLink);
            
            $Query = "
            
                UPDATE 
                    bevomedia_queue
                SET
                    envelope = '{$envelope}'
                WHERE
                    jobId = '$jobId'
            
            ";
            
            
            mysql_query($Query, $DBLink);
        	
        }//Public Function Sendenvelope()
        
        
        /**
         * ProcessNextInQueue Function ( Public )
         * This should be the function called by the CRON
         * 
         * @return boolean
         */
        Public Function ProcessNextInQueue()
        {
        	//First lets make sure there isn't already a item started
        	$DBLink = mysql_connect(ABSDBHOST, ABSDBUSER, ABSDBPASS);
            mysql_select_db(ABSDBNAME, $DBLink);
			
        	//Lets get all entries that have envelopes and have not been started
        	$Query = "
        		SELECT
        			jobId, id, started
        		FROM
        			bevomedia_queue
        		WHERE
        			started != '0000-00-00 00:00:00'	
        		AND
        			completed = '0000-00-00 00:00:00'
        	    AND
        	        deleted = 0
        	    AND
        	    	instanceIP = '{$this->instanceIP}'
        		
        	";
        	
        	$Result = mysql_query($Query, $DBLink);
        	$Results = array();
        	while($Row = mysql_fetch_object($Result))
        	{
            	$Results[] = $Row;
				if((time() - strtotime($Row->started)) > 3600*6)
				{
				  echo 'Job ran too long, killing it: ' . print_r($Row, true);
				  mysql_query('update bevomedia_queue set completed=NOW() where id='.$Row->id);
				  mysql_query("INSERT INTO bevomedia_queue_log (description, queueId, status) VALUES ('Forcing job to quit, ran too long!', {$Row->id}, 'error')");
				} else {
				  echo 'Other running job: ' . print_r($Row, true);
				}
        	}
        	
        	if(count($Results)>19)
        	{
        		//There is, lets return right now
				echo "\n20 currently running jobs, quitting!";
        		return false;
        	}
        	else
        	{
        		//There isn't, lets get the next in queue and process it
			    mysql_query("LOCK TABLES bevomedia_queue WRITE");
        		$Query2 = "
	        		SELECT
	        			jobId, envelope, id
	        		FROM
	        			bevomedia_queue
	        		WHERE
	        			started = '0000-00-00 00:00:00'	
	        		AND
	        			completed = '0000-00-00 00:00:00'
	        		AND
        	            deleted = 0
                    LIMIT 0, 1000
	        	";
	        	
	        	$Result2 = mysql_query($Query2, $DBLink);
            	$Results2 = array();
            	while($Row2 = mysql_fetch_object($Result2))
            	{
                	$Results2[] = $Row2;
            	}
	        	if(count($Results2)>0)
	        	{
	        		//We got an item for queue
	        		//Lets create the file in the sandbox directory and execute it
	        		$Started = date("Y-m-d H:i:s");
	        		$Query3 = "
            
                        UPDATE 
                            bevomedia_queue
                        SET
                            started = '{$Started}',
                         	instanceIP = '{$this->instanceIP}'
                        WHERE
                            id = '{$Results2[0]->id}'
                    
                    ";
                    
                    mysql_query($Query3, $DBLink);
					mysql_query("UNLOCK TABLES");
					$at = date('c');
					$q = ("INSERT INTO bevomedia_queue_log (description, queueId, status) VALUES ('Started job at $at', {$Results2[0]->id}, 'message')");
					mysql_query($q);
                    global $QueryComponentjobId;
	        		$QueryComponentjobId = $Results2[0]->jobId;
                    //DEFINE('jobId', $Results2[0]->jobId);
	        		file_put_contents($this->SandboxDirectory.DIRECTORY_SEPARATOR.$Results2[0]->jobId.'.php', $Results2[0]->envelope);
	        		
					$outputs = array();
					$status = 'success';
					try {
					  exec("php " .$this->SandboxDirectory.DIRECTORY_SEPARATOR.$Results2[0]->jobId.'.php', $outputs);
					} catch (Exception $e) {
					  $status = 'error';
					  $outputs[] = $e->getMessage();
					}

					$output = mysql_real_escape_string(implode($outputs, "\n"), $DBLink);
	        		$Completed = date("Y-m-d H:i:s");
	        		$Query4 = "
            
                        UPDATE 
                            bevomedia_queue
                        SET
							completed = '{$Completed}',
							output = '{$output}'
                        WHERE
                            id = '{$Results2[0]->id}'
                    
                    ";
                    
                    
                    mysql_query($Query4, $DBLink);
					mysql_query("UPDATE bevomedia_queue_log SET status='$status' WHERE status='in-progress' AND queueId = {$Results2[0]->id}", $DBLink); 
                    exec("rm -rf {$this->SandboxDirectory}/{$Results2[0]->jobId}*");
	        		
                    return $this->ProcessNextInQueue();
	        	}
	        	else 
	        	{
					mysql_query("UNLOCK TABLES");
	        		//There are no items in queue
	        		return false;
	        	}
        	}
        	
        }//Public Function ProcessNextInQueue()
        
        
        
        /**
         * GetQueueposition Function ( Public )
         *
         * @param string $jobId
         * @return int
         */
        Public Function GetQueueposition($jobId)
        {
        	$this->jobId = $jobId;
        	$this->position = -1;
        	
        	$DBLink = mysql_connect(ABSDBHOST, ABSDBUSER, ABSDBPASS);
            mysql_select_db(ABSDBNAME, $DBLink);
			
        	//Lets get all entries that have envelopes and have not been started
        	$Query = "
        		SELECT
        			jobId
        		FROM
        			bevomedia_queue
        		WHERE
        			envelope != ''
        		AND
        			started = '0000-00-00 00:00:00'	
        		AND
        		    deleted = 0
        		
        	";
        	
        	$Result = mysql_query($Query, $DBLink);
        	$Results = array();
        	while($Row = mysql_fetch_object($Result))
        	{
            	$Results[] = $Row;
        	}
        	
        	if(count($Results)>0)
        	{
	        	foreach($Results as $Key=>$Value)
	        	{
	        		if($Value->jobId == $this->jobId)
	        		{
	        			$this->position = $Key+1;
	        		}
	        	}
        	}
        	
        	
        	
        	return $this->position;
        	
        }//Public Function GetQueueposition($jobId)
        
        
        /**
         * GetJoboutput Function ( Public )
         *
         * @param string $jobId
         * @return string
         */
        Public Function GetJoboutput($jobId)
        {
            $this->jobId = $jobId;
            $this->output = '';
            
            $DBLink = mysql_connect(ABSDBHOST, ABSDBUSER, ABSDBPASS);
            mysql_select_db(ABSDBNAME, $DBLink);
			
        	//Lets get all entries that have envelopes and have not been started
        	$Query = "
        		SELECT
        			output
        		FROM
        			bevomedia_queue
        		WHERE
        			jobId = '{$this->jobId}'	
        		
        	";
        	
        	
        	$Result = mysql_query($Query, $DBLink);
        	$Results = array();
        	while($Row = mysql_fetch_object($Result))
        	{
            	$Results[] = $Row;
        	}
        	
        	if(count($Results)>0)
        	{
                foreach($Results as $Value)
	        	{
    	        	$this->output = $Value->output;
	        		
	        	}
        	}

        	return $this->output;  
        	
            
            
        }//Public Function GetJoboutput($jobId)
            
        /**
         * GetJobstatus Function ( Public )
         *
         * @param string $jobId
         * @return string
         */
        Public Function GetJobstatus($jobId)
        {
        	$this->jobId = $jobId;
        	$this->status = 'Not in Queue';
        	
       		$DBLink = mysql_connect(ABSDBHOST, ABSDBUSER, ABSDBPASS);
            mysql_select_db(ABSDBNAME, $DBLink);
			
        	//Lets get all entries that have envelopes and have not been started
        	$Query = "
        		SELECT
        			started, completed, deleted
        		FROM
        			bevomedia_queue
        		WHERE
        			jobId = '{$this->jobId}'	
        		
        	";
        	
        	$Result = mysql_query($Query, $DBLink);
        	$Results = array();
        	while($Row = mysql_fetch_object($Result))
        	{
            	$Results[] = $Row;
        	}
        	
        	if(count($Results)>0)
        	{
	        	foreach($Results as $Value)
	        	{
    	        	if($Value->Deleted == 1)
	        		{
	        			$this->status = 'Deleted';
	        		}
	        		elseif($Value->Started == '0000-00-00 00:00:00' && $Value->Completed == '0000-00-00 00:00:00')
	        		{
	        			$this->status = 'Queued';
	        		}
	        		elseif($Value->Started != '0000-00-00 00:00:00' && $Value->Completed == '0000-00-00 00:00:00')
	        		{
	        			$this->status = 'Processing';
	        		}
	        		elseif($Value->Started != '0000-00-00 00:00:00' && $Value->Completed != '0000-00-00 00:00:00')
	        		{
	        			$this->status = 'Completed';
	        		}
	        		else 
	        		{
	        			//Not sure what this could be
	        			$this->status = 'Error';
	        		}
	        	}
        	}

        	return $this->status;        	
        	
        }//Public Function GetJobstatus($jobId)
        
        /**
         * RemoveFromQueue Function ( Public )
         *
         * @param string $jobId
         * @return void
         */
        Public Function RemoveFromQueue($jobId)
        {
        	$this->jobId = $jobId;
        	
        	$DBLink = mysql_connect(ABSDBHOST, ABSDBUSER, ABSDBPASS);
            mysql_select_db(ABSDBNAME, $DBLink);
       		
            $Query = "
            
                UPDATE 
                    bevomedia_queue
                SET
                    deleted = 1
                WHERE
                    jobId = '{$jobId}'
            
            ";
            
            
            mysql_query($Query, $DBLink);
        	
    		
    		$this->status = 'Deleted';
        	
        }//Public Function RemoveFromQueue($jobId)
        
        
        
       /**
         * Queue Object Destructor
         * 
         * @return void
         */
        Public Function __destruct()
        {
            if(func_num_args() > 0)
        	{
        		trigger_error('Sorry, but '.__CLASS__.'::'.__FUNCTION__.' does not accept any parameters', E_USER_ERROR);
        		
        	}//if(func_num_args() > 0)        
            
        }//Public Function __destruct()		
		
	}//Class QueueComponent 
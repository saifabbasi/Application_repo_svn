<?php

	/**
     * bevomedia_queue.Component.php
     *
     * @category   RCS Framework 
 	 * @package    Components
     * @subpackage bevomedia_queue
 	 * @copyright  Copyright (c) 2009 RCS
     * @author RCS
     * @version 0.1.2
     */
	Class bevomedia_queueComponent Extends ClassComponent  
	{
		
		/**
		 * @var string $GUID
		 * From ClassComponent
		 */
		Public 	$GUID 					= NULL;
		
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
         * bevomedia_queue Object Constructor
         * 
         * @return void
         */
		Public Function __construct()
        {
        	if(func_num_args() > 0)
        	{
        		trigger_error('Sorry, but '.__CLASS__.'::'.__FUNCTION__.' does not accept any parameters', E_USER_ERROR);
        		
        	}//if(func_num_args() > 0)

        	parent::GenerateGUID();
            
        }//Public Function __construct()
        
        /**
         * CreatejobId Function ( Public )
         *
         * This is called first before submitting the envelope.
         * This generates your random 8 character long jobId.
         * 
         * @return string
         */
        Public Function CreatejobId()
        {
        	$DatabaseObj = Zend_Registry::get('Instance/DatabaseObj');
        	
			$Salt = "abcdefghijklmnpqrstuvwxyzABCDEFGHIJKLMNPQRSTUVWXYZ1234567890_";
			srand((double)microtime()*1000000);
			$ConfirmationKey = "";
			for ($i=0;$i<8;$i++)
			{
				$ConfirmationKey = $ConfirmationKey . substr ($Salt, rand() % strlen($Salt), 1);
			}
			
			$InsertArray = array(
       			'jobId' => $ConfirmationKey,
    		);
    		
    		$Sql = "SELECT id FROM bevomedia_queue WHERE jobId = '$ConfirmationKey'";
    		$Rows = $DatabaseObj->fetchAll($Sql);
    		if(sizeof($Rows) > 0)
    		{
    			$ConfirmationKey = $this->CreatejobId();
    			return $ConfirmationKey;
    		}
    		
    		$DatabaseObj->insert('bevomedia_queue', $InsertArray);
    		
    		$this->jobId = $ConfirmationKey;
    		
			return $ConfirmationKey;
        	
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
        	
        	$DatabaseObj = Zend_Registry::get('Instance/DatabaseObj');
       		
        	$UpdateArray = array(
    			'envelope' => $envelope,						    			
    		);
    		
    		$DatabaseObj->update('bevomedia_queue', $UpdateArray, "jobId = '{$jobId}'");
    		
        	
        }//Public Function Sendenvelope()
        
        
        /**
         * CleanOverduebevomedia_queueItems Function ( Public ) 
         * This function will remove items in the bevomedia_queue that have processed for more than two hours.
         * 
         * @return void
         */
        Public Function CleanOverduebevomedia_queueItems()
        {
        	// Create a connection to the database
        	$DBLink = mysql_connect(ABSDBHOST, ABSDBUSER, ABSDBPASS);
            mysql_select_db(ABSDBNAME, $DBLink);
            
            // Set any bevomedia_queue items to complete if they have been processing for more than two hours
            $Query = '
					UPDATE
						bevomedia_queue
					SET
						completed = NOW( ),
						output = "//AUTOMATICALLY SET TO COMPLETE BY COMPONENT"
					WHERE
						TIMEDIFF( NOW( ) , started ) > "02:00:00"
					AND
						completed = "0000-00-00 00:00:00"
					';
            
			mysql_query($Query);
		}//Public Function CleanOverduebevomedia_queueItems()
        
        /**
         * ProcessNextInbevomedia_queue Function ( Public )
         * This should be the function called by the CRON
         * 
         * @return void
         */
        Public Function ProcessNextInbevomedia_queue()
        {
        	//First lets make sure there isn't already a item started
        	$DatabaseObj = Zend_Registry::get('Instance/DatabaseObj');
			
        	//Lets get all entries that have envelopes and have not been started
        	$Query = "
        		SELECT
        			jobId
        		FROM
        			bevomedia_queue
        		WHERE
        			started != '0000-00-00 00:00:00'	
        		AND
        			completed = '0000-00-00 00:00:00'
        		
        	";
        	
        	$Results = $DatabaseObj->fetchAll($Query);
        	if(count($Results)>0)
        	{
        		//There is, lets return right now
        		return false;
        	}
        	else
        	{
        		//There isn't, lets get the next in bevomedia_queue and process it
        		$Query = "
	        		SELECT
	        			jobId, envelope
	        		FROM
	        			bevomedia_queue
	        		WHERE
	        			started = '0000-00-00 00:00:00'	
	        		AND
	        			completed = '0000-00-00 00:00:00'
	        		
	        	";
	        	
	        	$Results = $DatabaseObj->fetchAll($Query);
	        	if(count($Results)>0)
	        	{
	        		//We got an item for bevomedia_queue
	        		//Lets create the file in the sandbox directory and execute it
	        		$UpdateArray = array(
		    			'started' => date("Y-m-d H:i:s")						    			
		    		);
		    		
		    		$DatabaseObj->update('bevomedia_queue', $UpdateArray, "jobId = '{$Results[0]->jobId}'");
		    		
	        		file_put_contents($this->SandboxDirectory.DIRECTORY_SEPARATOR.$Results[0]->jobId.'.php', $Results[0]->envelope);
	        		ob_start();
	        		include($this->SandboxDirectory.DIRECTORY_SEPARATOR.$Results[0]->jobId.'.php');
	        		$output = ob_get_clean();
	        		
	        		$UpdateArray = array(
		    			'completed' => date("Y-m-d H:i:s"),
	        			'output'=>$output
		    		);
		    		
		    		$DatabaseObj->update('bevomedia_queue', $UpdateArray, "jobId = '{$Results[0]->jobId}'");
	        		return true;
	        	}
	        	else 
	        	{
	        		//There are no items in bevomedia_queue
	        		return false;
	        	}
        	}
        	
        }//Public Function ProcessNextInbevomedia_queue()
        
        
        
        /**
         * Getbevomedia_queueposition Function ( Public )
         *
         * @param string $jobId
         * @return int
         */
        Public Function Getbevomedia_queueposition($jobId)
        {
        	$this->jobId = $jobId;
        	$this->position = -1;
        	
        	$DatabaseObj = Zend_Registry::get('Instance/DatabaseObj');
			
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
        		
        	";
        	
        	$Results = $DatabaseObj->fetchAll($Query);
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
        	
        }//Public Function Getbevomedia_queueposition($jobId)
        
        /**
         * GetJobstatus Function ( Public )
         *
         * @param string $jobId
         * @return string
         */
        Public Function GetJobstatus($jobId)
        {
        	$this->jobId = $jobId;
        	$this->status = 'Not in bevomedia_queue';
        	
       		$DatabaseObj = Zend_Registry::get('Instance/DatabaseObj');
			
        	//Lets get all entries that have envelopes and have not been started
        	$Query = "
        		SELECT
        			started, completed, Deleted
        		FROM
        			bevomedia_queue
        		WHERE
        			jobId = '{$this->jobId}'	
        		
        	";
        	
        	$Results = $DatabaseObj->fetchAll($Query);
        	if(count($Results)>0)
        	{
	        	foreach($Results as $Value)
	        	{
	        		if($Value->started == '0000-00-00 00:00:00' && $Value->completed == '0000-00-00 00:00:00')
	        		{
	        			$this->status = 'bevomedia_queued';
	        		}
	        		elseif($Value->started == '0000-00-00 00:00:00' && $Value->completed != '0000-00-00 00:00:00')
	        		{
	        			$this->status = 'Processing';
	        		}
	        		elseif($Value->started != '0000-00-00 00:00:00' && $Value->completed != '0000-00-00 00:00:00')
	        		{
	        			$this->status = 'completed';
	        		}
	        		elseif($Value->Deleted == 1)
	        		{
	        			$this->status = 'Deleted';
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
         * RemoveFrombevomedia_queue Function ( Public )
         *
         * @param string $jobId
         * @return void
         */
        Public Function RemoveFrombevomedia_queue($jobId)
        {
        	$this->jobId = $jobId;
        	
        	$DatabaseObj = Zend_Registry::get('Instance/DatabaseObj');
       		
        	$UpdateArray = array(
    			'Deleted' => 1,						    			
    		);
    		
    		$DatabaseObj->update('bevomedia_queue', $UpdateArray, "jobId = '{$jobId}'");
    		
    		$this->status = 'Deleted';
        	
        }//Public Function RemoveFrombevomedia_queue($jobId)
        
        
        
       /**
         * bevomedia_queue Object Destructor
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
		
	}//Class bevomedia_queueComponent 

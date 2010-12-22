<?php

	
    if(!ini_set("max_execution_time", 0))
    {
        //throw new Exception("Failed to set execution time"); 
    }

    error_reporting(E_ALL);
    ini_set('display_errors', '1');


    Class EC2Scale
    {
        
        /**
         * EC2Scale 
         * This is the number of items per instance, before a new Instance is launched
         * @var int $Threshold
         */
        Private     $Threshold              = 25;
        
        /**
         * EC2Scale 
         * This is the expiration ( in Minutes ) of a Instance. To avoid paying any more than we have to
         * @var int $Expiration
         */
        Private     $Expiration             = 55;

        /**
         * EC2Scale 
         * This is the number of instances to launch per integer difference in ($QueueCount/$Threshold)-$InstancesRunning
         * @var int $InstancesToLaunch
         */
        Private     $InstancesToLaunch      = 1;
        
        /**
         * EC2Scale 
         * This is the AMI of the Instance we should launch
         * @var string $AMI
         */
        Private	    $AMI                    = "ami-d520cebc";
        
        /**
         * EC2Scale 
         * This is the SSH Key of the Instance we should launch
         * @var string $SSHKey
         */
        Private	    $SSHKey                 = "bevomedia-default";
        
        /**
         * EC2Scale 
         * This is the Security Group of the Instance we should launch
         * @var string $SecurityGroup
         */
        Private	    $SecurityGroup          = array("webserver","default");
        
        /**
         * EC2Scale 
         * This is the AWS Key
         * @var string $AWSKey
         */
        Private 	$AWSKey                 = "AKIAJT2QDN6UELJQEQIQ";
        
        /**
         * EC2Scale 
         * This is the AWS Secret Key
         * @var string $AWSSecretKey
         */
        Private 	$AWSSecretKey           = "ZYDvHWw1Y+d/5NeShcWnrza7CwcIZ/cz/031Gz3T";
        
        /**
         * EC2Scale 
         * Debug
         * @var int $Debug
         */
        Public  	$Debug                  = 1;
        

        
        /**
         * EC2Scale Object Constructor
         * 
         * @return EC2Scale
         */
		Public Function __construct()
        {
        	if(func_num_args() > 0)
        	{
        		trigger_error('Sorry, but '.__CLASS__.'::'.__FUNCTION__.' does not accept any parameters', E_USER_ERROR);
        		
        	}//if(func_num_args() > 0)
        	
            
        }//Public Function __construct()

        /**
         * checkQueueForThreshold Function ( Public )
         *
         * @return void
         */
        Public Function checkQueueForThreshold()
        {
            
        	$Results = $this->getQueueCount();
        	if($this->Debug){ print __CLASS__."::".__FUNCTION__." ( Queue Count: ".count($Results)." )\n"; 	}
        	if(count($Results)>0)
        	{
        		//We have items, lets see if we have the right number of instances
        		$QueueCount       = count($Results);
        		$InstanceCount    = ( count($this->getInstanceCount()) + 1 );//We add 1 because of the main
        	    
        	    if($this->Debug){ print __CLASS__."::".__FUNCTION__." ( Queue Count: ".$QueueCount." )\n"; 	}
        	    if($this->Debug){ print __CLASS__."::".__FUNCTION__." ( Instance Count: ".$InstanceCount." )\n"; 	}	
        	    
        	    
        		//For each Instance we need to have no more than $this->Threshold items in Queue
        		if( ($QueueCount/$InstanceCount) > $this->Threshold )
        		{
        		  //We need to launch more instances
        		  $InstancesToLaunch = ceil( ($QueueCount/$this->Threshold) );
        		  $InstancesToLaunch = ( ($InstancesToLaunch - $InstanceCount) );
        		  
        		  if($this->Debug){ print __CLASS__."::".__FUNCTION__." ( Launching ".$InstancesToLaunch." more Instance(s) )\n"; 	}
        		  
        		  for($i=0;$i<$InstancesToLaunch;$i++)
        		  {
        		      $this->startThresholdInstance();
        		      
        		  }//for($i=0;$i<$InstancesToLaunch;$i++)
        		  
        		  
        		}
        		else
        		{
        		  if($this->Debug){ print __CLASS__."::".__FUNCTION__." ( We have enough Instances )\n"; 	}
        		  //We don't need to do anything.
        		  return;
        		   
        		}//if( ($QueueCount/$InstanceCount) > $this->Threshold ) 
        		
        	}
        	else
        	{
        	    //Nothing in the queue. Lets make sure we only have our primary Instance running.
        	    if($this->Debug){ print __CLASS__."::".__FUNCTION__." ( Queue at 0 Shutting down all Instances )\n"; 	}  
        	    $this->shutdownAllThresholdInstances();
        	    
        	}//if(count($Results)>0)
               
        }//Public Function checkQueueForThreshold()
        
        /**
         * getQueueCount Function ( Public )
         *
         * @return void
         */
        Public Function getQueueCount()
        {
            $DatabaseObj = Zend_Db::factory('Pdo_Mysql', array(
				   'host' => ABSDBHOST,
		 			'username' => ABSDBUSER,
		 			'password' => ABSDBPASS,
		 			'dbname' => ABSDBNAME,
		 			'port' => 3306
				));	
	    	$DatabaseObj->setFetchMode(Zend_Db::FETCH_OBJ);
	    	$DatabaseObj->query("SET NAMES 'utf8'");
	    	$DatabaseObj->query("SET CHARACTER SET 'utf8'");
				       
            $Query = "
                SELECT 
                    id
                FROM
                    bevomedia_queue
                WHERE
                    started = '0000-00-00 00:00:00'
                AND
                    completed = '0000-00-00 00:00:00' 
            ";
            
            if($this->Debug){ print __CLASS__."::".__FUNCTION__." ( {$Query} )\n"; 	}
               
            $Results = $DatabaseObj->fetchAll($Query);
            
            return $Results;
            
            
        }//Public Function getQueueCount()
        
        /**
         * getExpiredInstanceCount Function ( Public )
         *
         * @return void
         */
        Public Function getExpiredInstanceCount()
        {
            

            $DatabaseObj = Zend_Db::factory('Pdo_Mysql', array(
				   'host' => ABSDBHOST,
		 			'username' => ABSDBUSER,
		 			'password' => ABSDBPASS,
		 			'dbname' => ABSDBNAME,
		 			'port' => 3306
				));            	
	    	$DatabaseObj->setFetchMode(Zend_Db::FETCH_OBJ);
	    	$DatabaseObj->query("SET NAMES 'utf8'");
	    	$DatabaseObj->query("SET CHARACTER SET 'utf8'");
	    	
            $ExpirationTimestamp = date("Y-m-d H:i:s", strtotime("-{$this->Expiration} minutes"));
            
            $Query = "
                SELECT 
                    instanceId
                FROM
                    bevomedia_queue_instances
                WHERE
                    id > 0
                AND
                    created < '{$ExpirationTimestamp}'
            ";

            if($this->Debug){ print __CLASS__."::".__FUNCTION__." ( {$Query} )\n"; 	}
               
            $Results = $DatabaseObj->fetchAll($Query);
            
            return $Results;
            
            
        }//Public Function getExpiredInstanceCount()
        
        /**
         * getInstanceCount Function ( Public )
         *
         * @return void
         */
        Public Function getInstanceCount()
        {
            $DatabaseObj = Zend_Db::factory('Pdo_Mysql', array(
				   'host' => ABSDBHOST,
		 			'username' => ABSDBUSER,
		 			'password' => ABSDBPASS,
		 			'dbname' => ABSDBNAME,
		 			'port' => 3306
				));	
	    	$DatabaseObj->setFetchMode(Zend_Db::FETCH_OBJ);
	    	$DatabaseObj->query("SET NAMES 'utf8'");
	    	$DatabaseObj->query("SET CHARACTER SET 'utf8'");
	    	
			$Query = "
                SELECT 
                    instanceId
                FROM
                    bevomedia_queue_instances
                WHERE
                    ID > 0
            ";
            
            if($this->Debug){ print __CLASS__."::".__FUNCTION__." ( {$Query} )\n"; 	}
               
            $Results = $DatabaseObj->fetchAll($Query);
            
            return $Results;
            
            
        }//Public Function getInstanceCount()
        
        /**
         * shutdownAllThresholdInstances Function ( Public )
         *
         * @return void
         */
        Public Function shutdownAllThresholdInstances()
        {
            
            if($this->Debug){ print __CLASS__."::".__FUNCTION__." ( )\n"; 	}
            
            $Results = $this->getExpiredInstanceCount();
        	
        	if(count($Results)>0)
        	{
        	    if($this->Debug){ print __CLASS__."::".__FUNCTION__." ( Expired items exist Shutting them down )\n"; 	}
        		//There is items running, lets shut them down
        		foreach($Results as $Key=>$Value)
        		{
        		    $this->shutdownThresholdInstance($Value->instanceId);
        		  
        		}//foreach($Results as $Key=>$Value)

        	}
        	else
        	{
        	    if($this->Debug){ print __CLASS__."::".__FUNCTION__." ( No Expired items exist or no items running )\n"; 	}
        	    //No items running
        	    return;
        	            	    
        	}//if(count($Results)>0)

            return;
            
        }//Public Function shutdownAllThresholdInstances()
        
        /**
         * startThresholdInstance Function ( Public )
         * @param void
         * @return void
         */
        Public Function startThresholdInstance()
        {
            if($this->Debug){ print __CLASS__."::".__FUNCTION__." ( Launching Instance )\n"; 	}
            $ZendEC2Obj = new Zend_Service_Amazon_Ec2_Instance($this->AWSKey, $this->AWSSecretKey);
            $Return     = $ZendEC2Obj->run(array('imageId' => $this->AMI,
                                                'keyName' => $this->SSHKey,
            									'placement' => 'us-east-1c',
                                                'securityGroup' => $this->SecurityGroup));
            
            if($this->Debug){ print __CLASS__."::".__FUNCTION__." ( Instance ".$Return['instances'][0]['instanceId']." Launched )\n"; 	}
                                                

            $DatabaseObj = Zend_Db::factory('Pdo_Mysql', array(
				   'host' => ABSDBHOST,
		 			'username' => ABSDBUSER,
		 			'password' => ABSDBPASS,
		 			'dbname' => ABSDBNAME,
		 			'port' => 3306
				));	
	    	$DatabaseObj->setFetchMode(Zend_Db::FETCH_OBJ);
	    	$DatabaseObj->query("SET NAMES 'utf8'");
	    	$DatabaseObj->query("SET CHARACTER SET 'utf8'");
				        	
        	$InsertArray = array(
       			'instanceId' => $Return['instances'][0]['instanceId'],
    		);
    		
    		if($this->Debug){ print __CLASS__."::".__FUNCTION__." ( Inserting Instance ".$Return['instances'][0]['instanceId']." into DB )\n"; 	}
    		$DatabaseObj->insert('bevomedia_queue_instances', $InsertArray);
            
        }//Public Function startThresholdInstance()
        
        /**
         * shutdownThresholdInstance Function ( Public )
         * @param string $instanceId
         * @return void
         */
        Public Function shutdownThresholdInstance($instanceId)
        {
            if($this->Debug){ print __CLASS__."::".__FUNCTION__." ( Shutting down Instance ".$instanceId." )\n"; 	}
            
            $ZendEC2Obj = new Zend_Service_Amazon_Ec2_Instance($this->AWSKey, $this->AWSSecretKey);
            $ZendEC2Obj->terminate($instanceId);

            $DatabaseObj = Zend_Db::factory('Pdo_Mysql', array(
				   'host' => ABSDBHOST,
		 			'username' => ABSDBUSER,
		 			'password' => ABSDBPASS,
		 			'dbname' => ABSDBNAME,
		 			'port' => 3306
				));
				
	    	$DatabaseObj->setFetchMode(Zend_Db::FETCH_OBJ);
	    	$DatabaseObj->query("SET NAMES 'utf8'");
	    	$DatabaseObj->query("SET CHARACTER SET 'utf8'");
            $Query = "
                DELETE FROM
                    bevomedia_queue_instances
                WHERE
                    instanceId = '{$instanceId}'
                    
            ";
            
            if($this->Debug){ print __CLASS__."::".__FUNCTION__." ( Removing Instance ".$instanceId." from DB )\n"; 	}
            $DatabaseObj->query($Query);            
            
        }//Public Function shutdownThresholdInstance($instanceId)
        
        /**
         * EC2Scale Object Destructor
         * 
         * @return void
         */
        Public Function __destruct()
        {
            
                        
        }//Public Function __destruct()
    
        
    }//Class EC2Scale
       
    
?>

<?php

	/**
     * Records.Component.php
     *
     * @category   RCS Framework 
 	 * @package    Components
     * @subpackage Records
 	 * @copyright  Copyright (c) 2009 RCS
     * @author RCS
     * @todo Implement the correct tags on cache cleaning.
     * @todo Implement WhoLockedRecord on Webservice side. This should be a easy implementation,but a fresh mind needs to go in to be sure.
     * @todo Implement Pagintation on BrowseAllRecords
     * @todo Implement Pagintation on BrowseAllActiveRecords
     * @todo Implement Pagintation on BrowseAllDeletedRecords
     * @todo Implement SearchComponent for SearchRecords function 
     * @todo Implement AuditComponent for RetrieveAudit function
     * @version 0.1.2
     */
	Class RecordsComponent Extends ClassComponent 
	{
		
		/**
		 * @var string $GUID
		 * From ClassComponent
		 */
		Public 	$GUID 					= NULL;
		
		/**
    	 * @var resource $SoapClient
    	 */
    	Private $SoapClient;
    	
    	/**
    	 * @var resource $CacheClient
    	 */
    	Private $CacheClient;
    	
    	/**
    	 * @var string $Application
    	 */
    	Private $Application;
    	
    	/**
    	 * @var string $Module
    	 */
    	Private $Module;
    	
    	/**
    	 * @var string $Function
    	 */
    	Private $Function;
    	
    	/**
    	 * @var array FieldList
    	 */
    	Private $FieldList = array();
    	
    	/**
    	 * @var int FieldCount
    	 */
    	Private $FieldCount = 0;  
    	
		/**
    	 * @var RequiredFields
    	 */
    	Private $RequiredFields = array('ID', 'Deleted', 'Locked', 'Created'); 
		
		/**
         * Records Object Constructor
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
        	
        	$this->Application = Zend_Registry::get('Instance/Application');
        	$this->Module = Zend_Registry::get('Instance/Module');
        	$this->Function = Zend_Registry::get('Instance/Function');
        	
        	
   			$AuditObj = new AuditComponent();
       	
        	$SignalSlotObj = Zend_Registry::get('Instance/SignalSlotObj');
        	$SignalSlotObj->connect($this, 'UpdateRecord', $AuditObj, 'CreateAudit');
        	$SignalSlotObj->connect($this, 'DeleteRecord', $AuditObj, 'CreateAudit');
        	$SignalSlotObj->connect($this, 'UnDeleteRecord', $AuditObj, 'CreateAudit');
        	$SignalSlotObj->connect($this, 'LockRecord', $AuditObj, 'CreateAudit');
        	$SignalSlotObj->connect($this, 'UnLockRecord', $AuditObj, 'CreateAudit');
        	
   			$ClassReflection = new ReflectionClass(Zend_Registry::get('Instance/ModelObjectName'));
		
        	foreach($ClassReflection->getProperties() as $Property)
        	{
        		$PropertyDoc = $Property->getDocComment();
        		
        		$this->FieldList[$this->FieldCount] = new RecordsComponentStruct();
        		$this->FieldList[$this->FieldCount]->Name = $Property->name;
        		
        		if($Property->isPublic())
        		{
	        		$Match = array();
	        		if(preg_match('|@var\s+(?:object\s+)?(\w+)|', $PropertyDoc, $Match)) 
	        		{
	        			$this->FieldList[$this->FieldCount]->Type = $Match[1];
	        			
	      			}//if(preg_match('|@var\s+(?:object\s+)?(\w+)|', $PropertyDoc, $Match))
	      			
	        		if(preg_match('|@db relationship\s+(?:object\s+)?(\w+)|', $PropertyDoc, $Match)) 
	        		{
	        			$this->FieldList[$this->FieldCount]->Relationship = $Match[1];
	        			
	      			}
	        		
	      			$this->FieldCount++;
	      			
        		}//if($Property->isPublic())
      			
        	}//foreach($ClassReflection->getProperties())
	        	
    		
    		
            
        }//Public Function __construct()
        
        /**
    	 * CreateRecord Function ( Public )
    	 *
    	 * @param $ClassObj
    	 * @return  
    	 */
    	Public Function CreateRecord($ClassObj)
    	{
    		if(func_num_args() > 1)
        	{
        		trigger_error('Sorry, but '.__CLASS__.'::'.__FUNCTION__.' does not accept more than one parameter', E_USER_ERROR);
        		
        	}//if(func_num_args() > 1)
        	
        	       	
    		
   			/**
    		 * First lets clean out old Locks
    		 */
    		$this->CleanLocks();
    		
    		/**
    		 * Lets create the Field List, and Question List
    		 */
    		$BindArray = array();
    		$QueryFieldList = '';
    		$QueryQuestionList = '';
    		
    		foreach($this->FieldList as $FieldObj)
    		{
    			if( (!in_array($FieldObj->Name, $this->RequiredFields)) )
    			{
    			    if($FieldObj->Relationship != 'multiple')
	    			{
	    				if($ClassObj->{$FieldObj->Name} == NULL)
    			        {

    			        }
    			        else
    			        {
	    				    $QueryFieldList .= '`'.$FieldObj->Name.'`,'; 
    					    $QueryQuestionList .= '?,';
	    				    $BindArray[] = $ClassObj->{$FieldObj->Name};
	    				}
	    				
	    			}
	    			else
	    			{
    					$RelationshipObj = $FieldObj;
	    			}
    				
    			}//if( (!in_array($FieldObj->Name, $this->RequiredFields)) )
    			
    		}//foreach($this->FieldList as $FieldObj)
    		$QueryFieldList = rtrim($QueryFieldList, ',');
    		$QueryQuestionList = rtrim($QueryQuestionList, ',');
    		
    		$Query = "
    		 	INSERT INTO
    		 		{$this->Application}_{$this->Module}
    		 	({$QueryFieldList})
    		 		VALUES
    		 	({$QueryQuestionList});    		 			
    		";
    		 
    		$DatabaseComponent = Zend_Registry::get('Instance/DatabaseObj');
    		
    		print $Query;die;
    		$ClassObj->ID = $DatabaseComponent->Query($Query, $BindArray);
    		 
    		if($RelationshipObj)
    		{
        		 
    		 	$RelationshipMatch = array();        			
        		preg_match('|(.*)_(.*)__(.*)|', $RelationshipObj->Name, $RelationshipMatch);
    		 	//foreach($ClassObj->{$RelationshipObj->Name} as $Key=>$RelationEntry)
    		 	foreach($ClassObj->{$RelationshipObj->Name} as $RelationEntry)
    			{
        			
    				$RelationshipBindArray = array();
    				$Query = "
		    		 	INSERT INTO
		    		 		`{$this->Application}_{$this->Module}_To_{$RelationshipMatch[1]}_{$RelationshipMatch[2]}`
		    		 	(`{$this->Application}_{$this->Module}_To_{$RelationshipMatch[1]}_{$RelationshipMatch[2]}`.`{$this->Application}_{$this->Module}__ID`, `{$this->Application}_{$this->Module}_To_{$RelationshipMatch[1]}_{$RelationshipMatch[2]}`.`{$RelationshipObj->Name}`)
		    		 		VALUES
		    		 	(?, ?) 		 			
		    		 ";
    			
    				$RelationshipBindArray[] = $ClassObj->ID;
    				$RelationshipBindArray[] = $RelationEntry;
    				
    				$DatabaseComponent = Zend_Registry::get('Instance/DatabaseObj');
    				$DatabaseComponent->Query($Query, $RelationshipBindArray);
    		 		
    			}
    		}
    		 
			$ClassObj = $this->RetrieveRecord($ClassObj);
			 
			$SignalSlotObj = Zend_Registry::get('Instance/SignalSlotObj');
       		$SignalSlotObj->emit($this, __FUNCTION__, $ClassObj);
    		     		 
    		return $ClassObj;
    		
    		
    	}//Public Function CreateRecord($ClassObj)
    	
    	/**
    	 * RetrieveRecord Function ( Public )
    	 *
    	 * @param $ClassObj
    	 * @return void
    	 */
    	Public Function RetrieveRecord($ClassObj)
    	{
    		if(func_num_args() > 1)
        	{
        		trigger_error('Sorry, but '.__CLASS__.'::'.__FUNCTION__.' does not accept more than one parameter', E_USER_ERROR);
        		
        	}//if(func_num_args() > 1)
        	
        	
    		
    			
   			/**
    		 * First lets clean out old Locks
    		 */
    		$this->CleanLocks();
    		
    		/**
    		 * Lets create the Field List, and Question List
    		 */
    		$QueryFieldList = '';
    		foreach($this->FieldList as $FieldObj)
    		{
    			if($FieldObj->Relationship != 'multiple')
    			{
    				$QueryFieldList .= "`{$this->Application}_{$this->Module}`.`{$FieldObj->Name}`,";
    			}
    			else
    			{
    				$RelationObject = $FieldObj;
    				 
    			} 
    			    			
    		}//foreach($this->FieldList as $FieldObj)
    		
    		$QueryFieldList = rtrim($QueryFieldList, ',');
	    	
	    	$Query = "
    		 	SELECT 
    		 		{$QueryFieldList}
    		 	FROM
    		 		{$this->Application}_{$this->Module} 
    		 	WHERE ID = ?
    		";
    		 
    		 	
    		$DatabaseComponent = Zend_Registry::get('Instance/DatabaseObj');
    		$Result = $DatabaseComponent->Query($Query, array($ClassObj->ID));
    		$Result = $Result[0];
    		 
    		foreach ($Result as $Key => $Value)
    		{
    		 	$ClassObj->{$Key} = $Value;
    		}
    		 
    		if($RelationObject)
    		{
    		 	$RelationshipMatch = array();
        		preg_match('|(.*)_(.*)__(.*)|', $RelationObject->Name, $RelationshipMatch);
        		
        		$Query = "
	    		 	SELECT 
        				{$RelationshipMatch[0]}
	    		 	FROM
    		 			{$this->Application}_{$this->Module}_To_{$RelationshipMatch[1]}_{$RelationshipMatch[2]} 
	    		 	WHERE {$this->Application}_{$this->Module}__ID = ?
	    		 ";
    		 			
    		 	$Results = $DatabaseComponent->Query($Query, array($ClassObj->ID));
    		 	foreach($Results as $Key=>$Val)
    		 	{
    		 		
    		 		$ClassObj->{$RelationObject->Name}[] = $Val->{$RelationshipMatch[0]};
    		 	}
    		 	
    		 	$ClassObj->{$RelationObject->Name}= array_unique($ClassObj->{$RelationObject->Name});
    		 
    		}
    		 
    		$SignalSlotObj = Zend_Registry::get('Instance/SignalSlotObj'); 
    		$SignalSlotObj->emit($this, __FUNCTION__, $ClassObj);
    		 
    		return $ClassObj;
    		
    		
    	}//Public Function RetrieveRecord($ClassObj)
    	
    	/**
    	 * UpdateRecord Function ( Public )
    	 *
    	 * @param $ClassObj
    	 * @return 
    	 */
    	Public Function UpdateRecord($ClassObj)
    	{
    		
    		if(func_num_args() > 1)
        	{
        		trigger_error('Sorry, but '.__CLASS__.'::'.__FUNCTION__.' does not accept more than one parameter', E_USER_ERROR);
        		
        	}//if(func_num_args() > 1)
        	
        	
    		$OriginalObj = new stdClass();
    			
   			foreach($ClassObj as $Key=>$Val)
   			{
   				$OriginalObj->$Key = $Val;
   			
   			}
   			
    		$OriginalObj = $this->RetrieveRecord($OriginalObj);	
    		
   			/**
    		 * First lets clean out old Locks
    		 */
    		$this->CleanLocks();
	    	
	    	$TempObj = new stdClass();
    		foreach($ClassObj as $Key=>$Val)
    		{
    			$TempObj->{$Key} = $Val;	
    			
    		}//foreach($ClassObj as $Key=>$Val)
    		
    		$this->RetrieveRecord($TempObj);
    		
    		
    		/**
    		 * Lets create the Field List, and Question List
    		 */
    		$BindArray = array();
    		$QueryFieldList = '';
    		foreach($this->FieldList as $FieldObj)
    		{
    			if( (!in_array($FieldObj->Name, $this->RequiredFields)) )
    			{
    				if($FieldObj->Relationship != 'multiple')
	    			{
	    			    if($ClassObj->{$FieldObj->Name} == NULL)
    			        {

    			        }
    			        else
    			        {
	        				$QueryFieldList .= '`'.$FieldObj->Name.'` = ?,'; 
        					$BindArray[] = $ClassObj->{$FieldObj->Name};
	    				}
	    				
	    			}
	    			else
	    			{
    					$RelationshipObj = $FieldObj;
	    			}
    				
    				
    			}//if( (!in_array($FieldObj->Name, $this->RequiredFields)) )
    			
    		}//foreach($this->FieldList as $FieldObj)
    		$BindArray[] = $ClassObj->ID;
    		$QueryFieldList = rtrim($QueryFieldList, ',');

    		
    		$Query = "
    		 	UPDATE
    		 		{$this->Application}_{$this->Module}
    		 	SET
    		 		{$QueryFieldList}
    		 	WHERE ID = ?
    		 ";
    		 
    		$DatabaseComponent = Zend_Registry::get('Instance/DatabaseObj');
    		 
    		$this->UnLockRecord($ClassObj);
    		 
    		$DatabaseComponent->Query($Query, $BindArray);

    		 
    		if($RelationshipObj)
    		{
    		 	
        		
	    		 
    		 	$RelationshipMatch = array();
        			
        		preg_match('|(.*)_(.*)__(.*)|', $RelationshipObj->Name, $RelationshipMatch);
        			
    		 	$Query = "
    		 	DELETE FROM 
    		 		{$this->Application}_{$this->Module}_To_{$RelationshipMatch[1]}_{$RelationshipMatch[2]}
    		 	WHERE
    		 		`{$this->Application}_{$this->Module}_To_{$RelationshipMatch[1]}_{$RelationshipMatch[2]}`.`{$this->Application}_{$this->Module}__ID` = ?
    		 	";
    		 		
    		 	
    		    $DatabaseComponent->Query($Query, array($ClassObj->ID));
    		 	
    		 	
    			foreach($ClassObj->{$RelationshipObj->Name} as $Key=>$RelationEntry)
    			{
    				$RelationshipBindArray = array();
    				
    				$Query = "
		    		 	INSERT INTO
		    		 		`{$this->Application}_{$this->Module}_To_{$RelationshipMatch[1]}_{$RelationshipMatch[2]}`
		    		 	(`{$this->Application}_{$this->Module}_To_{$RelationshipMatch[1]}_{$RelationshipMatch[2]}`.`{$this->Application}_{$this->Module}__ID`, `{$this->Application}_{$this->Module}_To_{$RelationshipMatch[1]}_{$RelationshipMatch[2]}`.`{$RelationshipObj->Name}`)
		    		 		VALUES
		    		 	(?, ?) 		 			
		    		 ";
    				
    				$RelationshipBindArray[] = $ClassObj->ID;
    				$RelationshipBindArray[] = $RelationEntry;
    				
    		 		$DatabaseComponent->Query($Query, $RelationshipBindArray);
    			}
    			
    		}
    		 
    		$SignalSlotObj = Zend_Registry::get('Instance/SignalSlotObj'); 
    		$SignalSlotObj->emit($this, __FUNCTION__, $ClassObj, $OriginalObj);     
    		 
    		return $ClassObj;
    		
    		
    	}//Public Function UpdateRecord($ClassObj)
    	
    	
    	/**
    	 * DeleteRecord Function ( Public )
    	 *
    	 * @param $ClassObj
    	 * @return void
    	 */
    	Public Function DeleteRecord($ClassObj)
    	{
    		
    		if(func_num_args() > 1)
        	{
        		trigger_error('Sorry, but '.__CLASS__.'::'.__FUNCTION__.' does not accept more than one parameter', E_USER_ERROR);
        		
        	}//if(func_num_args() > 1)
        	
        	
    		
   			/**
    		 * First lets clean out old Locks
    		 */
    		$this->CleanLocks();
    		
    		$OldSettingsObj = new stdClass();
    		$OldSettingsObj->ID = $ClassObj->ID;
    		$this->RetrieveRecord($OldSettingsObj);
    		 
    		$Query = "
    		 	UPDATE
    		 		{$this->Application}_{$this->Module}
    		 	SET
    		 		`Deleted` = 1
    		 	WHERE `ID` = ?
    		";
    		 
    		$DatabaseComponent = Zend_Registry::get('Instance/DatabaseObj');
    		$DatabaseComponent->Query($Query, array($ClassObj->ID));

    		$this->RetrieveRecord($ClassObj);
    		
    		$SignalSlotObj = Zend_Registry::get('Instance/SignalSlotObj'); 
    		$SignalSlotObj->emit($this, __FUNCTION__, $ClassObj); 
    		 
    		return $ClassObj;
    		
    		
    	}//Public Function DeleteRecord($ClassObj)
    	
    	
    	/**
    	 * UnDeleteRecord Function ( Public )
    	 *
    	 * @param $ClassObj
    	 * @return void
    	 */
    	Public Function UnDeleteRecord($ClassObj)
    	{
    		
    		if(func_num_args() > 1)
        	{
        		trigger_error('Sorry, but '.__CLASS__.'::'.__FUNCTION__.' does not accept more than one parameter', E_USER_ERROR);
        		
        	}//if(func_num_args() > 1)
        	
        	
        	
    		
   			/**
    		 * First lets clean out old Locks
    		 */
    		$this->CleanLocks();
    		
    		$OldSettingsObj = new stdClass();
    		$OldSettingsObj->ID = $ClassObj->ID;
    		$this->RetrieveRecord($OldSettingsObj);
    		 
    		$Query = "
    		 	UPDATE
    		 		{$this->Application}_{$this->Module}
    		 	SET
    		 		`Deleted` = 0
    		 	WHERE `ID` = ?
    		";
    		 
    		$DatabaseComponent = Zend_Registry::get('Instance/DatabaseObj');
    		$DatabaseComponent->Query($Query, array($ClassObj->ID));

    		$this->RetrieveRecord($ClassObj);
    		 
    		$SignalSlotObj = Zend_Registry::get('Instance/SignalSlotObj'); 
    		$SignalSlotObj->emit($this, __FUNCTION__, $ClassObj);    
    		 
    		return $ClassObj;
    		
    		
    	}//Public Function UnDeleteRecord($ClassObj)
    	
    	/**
    	 * LockRecord Function ( Public )
    	 *
    	 * @param $ClassObj
    	 * @return void
    	 */
    	Public Function LockRecord($ClassObj)
    	{
    		if(func_num_args() > 1)
        	{
        		trigger_error('Sorry, but '.__CLASS__.'::'.__FUNCTION__.' does not accept more than one parameter', E_USER_ERROR);
        		
        	}//if(func_num_args() > 1)
        	
        	
    		
   			/**
    		 * First lets clean out old Locks
    		 */
    		$this->CleanLocks();
    		$DatabaseComponent = Zend_Registry::get('Instance/DatabaseObj');
    		
    		/**
    		 * Now lets lock in the main table
    		 */
    		$Query = "
    		 	UPDATE
    		 		{$this->Application}_{$this->Module}
    		 	SET
    		 		`Locked` = 1
    		 	WHERE `ID` = ?
    		";
    		 
    		$DatabaseComponent->Query($Query, array($ClassObj->ID));
    		 
    		 
    		if("{$this->Application}_{$this->Module}" == "Core_Users")
    		{
        		/**
        		  * Create necessary record in _Lock table
        		  */
        		 $Query = "
        		 	REPLACE INTO
        		 		{$this->Application}_{$this->Module}_Lock
        		 		(`{$this->Application}_{$this->Module}__ID_Locked`, `Core_Users__ID`)
        		 	VALUES
        		 		(?, ?)
        		 		
        		 ";
    		}
    		else
    		{
        		 /**
        		  * Create necessary record in _Lock table
        		  */
        		 $Query = "
        		 	REPLACE INTO
        		 		{$this->Application}_{$this->Module}_Lock
        		 		(`{$this->Application}_{$this->Module}__ID`, `Core_Users__ID`)
        		 	VALUES
        		 		(?, ?)
        		 		
        		 ";
        		 
    		 }
    		 
    		$DatabaseComponent->Query($Query, array($ClassObj->ID, 1));
    		 
    		$this->RetrieveRecord($ClassObj);
    		
    		$SignalSlotObj = Zend_Registry::get('Instance/SignalSlotObj'); 
    		$SignalSlotObj->emit($this, __FUNCTION__, $ClassObj);      

    		return $ClassObj;
    		
    	}//Public Function LockRecord($ClassObj)
    	
    	/**
    	 * UnLockRecord Function ( Public )
    	 *
    	 * @param $ClassObj
    	 * @return void
    	 */
    	Public Function UnLockRecord($ClassObj)
    	{
    		if(func_num_args() > 1)
        	{
        		trigger_error('Sorry, but '.__CLASS__.'::'.__FUNCTION__.' does not accept more than one parameter', E_USER_ERROR);
        		
        	}//if(func_num_args() > 1)
        	
        	
    		
   			$Query = "
    		 	UPDATE
    		 		{$this->Application}_{$this->Module}
    		 	SET
    		 		`Locked` = 0
    		 	WHERE `ID` = ?
    		";
    		 
    		$DatabaseComponent = Zend_Registry::get('Instance/DatabaseObj');
    		$DatabaseComponent->Query($Query, array($ClassObj->ID));
    		 
    		$SignalSlotObj = Zend_Registry::get('Instance/SignalSlotObj'); 
    		$SignalSlotObj->emit($this, __FUNCTION__, $ClassObj);
    		
    		return $ClassObj;
    		
    	}//Public Function UnLockRecord($ClassObj)
    	
    	/**
    	 * WhoLocked Function ( Public )
    	 *
    	 * @param $ClassObj
    	 * @return UserStructure
    	 */
    	Public Function WhoLockedRecord($ClassObj)
    	{
    		if(func_num_args() > 1)
        	{
        		trigger_error('Sorry, but '.__CLASS__.'::'.__FUNCTION__.' does not accept more than one parameter', E_USER_ERROR);
        		
        	}//if(func_num_args() > 1)
        	
        	
        	
    		//NTD
    		
    		
    	}//Public Function WhoLocked()
    	
    	/**
    	 * CleanLocks Function ( Protected )
    	 *
    	 * 
    	 * @return void
    	 */
    	Public Function CleanLocks()
    	{   
    		
        	
        	if(func_num_args() > 0)
        	{
        		trigger_error('Sorry, but '.__CLASS__.'::'.__FUNCTION__.' does not accept any parameters', E_USER_ERROR);
        		
        	}//if(func_num_args() > 0)
        	
    		$Expiry = date('Y-m-d H:i:s' , date('U') - Zend_Registry::get('Application/Max Lock Time'));
	            
    		$DatabaseComponent = Zend_Registry::get('Instance/DatabaseObj');
    		
    		$Expiry = $DatabaseComponent->quote($Expiry);
    		$Query = "
    			SELECT 
    				`{$this->Application}_{$this->Module}__ID`
    			FROM
    				{$this->Application}_{$this->Module}_Lock L
    			WHERE
    				`LockTime` < $Expiry
    		";
    		
    		$ExpiredLocks = $DatabaseComponent->fetchAll($Query);
    		foreach ($ExpiredLocks as $Expired) 
    		{
    			$UnlockObj = new stdClass();
    			$UnlockObj->ID = $Expired->{"{$this->Application}_{$this->Module}__ID"};
    			$this->UnLockRecord($UnlockObj);
    			
    		}//foreach ($ExpiredLocks as $Expired) 
    		
    		$Query = "
    			DELETE FROM
    				{$this->Application}_{$this->Module}_Lock
    			WHERE
    				`LockTime` < ?
    		";
    		
    		$DatabaseComponent->Query($Query, array($Expiry));
    		
    		$SignalSlotObj = Zend_Registry::get('Instance/SignalSlotObj'); 
   			$SignalSlotObj->emit($this, __FUNCTION__, "");      
    		
    		
    	}//Public Function CleanLocks()
    	
    	/**
    	 * BrowseAllRecords Function ( Public )
    	 *
    	 * @param int $Page
    	 * @return array 
    	 */
    	Public Function BrowseAllRecords($Page)
    	{
    		
        	if(func_num_args() > 1)
        	{
        		trigger_error('Sorry, but '.__CLASS__.'::'.__FUNCTION__.' does not accept any parameters', E_USER_ERROR);
        		
        	}//if(func_num_args() > 1)
        	
    		
   			/**
    		 * Lets setup and execute the Query
    		 */
    		$this->CleanLocks();

    		$QueryFieldList = '';
    		$RelationObject = '';
    		foreach($this->FieldList as $FieldObj)
    		{
    			if($FieldObj->Relationship != 'multiple')
    			{
    				$QueryFieldList .= "`{$this->Application}_{$this->Module}`.`{$FieldObj->Name}`,";
    			}
    			else
    			{
    				$RelationObject = $FieldObj; 
    			}
    			
    		}//foreach($this->FieldList as $FieldObj)
    		$QueryFieldList = rtrim($QueryFieldList, ',');
    		
    		$Offset = Zend_Registry::get('Search/Results Per Page');
    		$Page = $Page-1 * $Offset;
    		
    		$Query = "
    		 	SELECT 
    		 		{$QueryFieldList} 
    		 	FROM
    		 		`{$this->Application}_{$this->Module}`  
				LIMIT {$Page}, {$Offset}
				
    		";
			
    		 		
    		$DatabaseComponent = Zend_Registry::get('Instance/DatabaseObj');
    		$ResultArray = array();
    		$ResultArray = $DatabaseComponent->Query($Query, array());
    		 
    		if($RelationObject)
    		{
    		 	
    		 	$RelationshipMatch = array();
        		preg_match('|(.*)_(.*)__(.*)|', $RelationObject->Name, $RelationshipMatch);
        		
        		foreach($ResultArray as $ResKey=>$ResVal)
        		{
        			
        			$Query = "
		    		 	SELECT 
	        				{$RelationshipMatch[0]}
		    		 	FROM
	    		 			{$this->Application}_{$this->Module}_To_{$RelationshipMatch[1]}_{$RelationshipMatch[2]} 
		    		 	WHERE {$this->Application}_{$this->Module}__ID = ?
		    		 ";
	    		 			
	    		 	$Results = $DatabaseComponent->Query($Query, array($ResVal->ID));
	    		 	//foreach($Results as $Key=>$Val)
	    		 	foreach($Results as $Val)
	    		 	{
	    		 		
	    		 		$ResultArray[$ResKey]->{$RelationshipMatch[0]}[] = $Val->{$RelationshipMatch[0]};
	    		 	}
	    		 	
	    		 	//$ClassObj->{$RelationObject->Name}= array_unique($ClassObj->{$RelationObject->Name});
        		}
    		 
    		}
    		 
    		$SignalSlotObj = Zend_Registry::get('Instance/SignalSlotObj'); 
    		$SignalSlotObj->emit($this, __FUNCTION__, $Page);     
    		 
    		return $ResultArray;
    		
    		
    	}//Public Function BrowseAllRecords()
    	
    	/**
    	 * BrowseAllActiveRecords Function ( Public )
    	 *
    	 * @return array 
    	 */
    	Public Function BrowseAllActiveRecords()
    	{
    		
    		if(func_num_args() > 0)
        	{
        		trigger_error('Sorry, but '.__CLASS__.'::'.__FUNCTION__.' does not accept any parameters', E_USER_ERROR);
        		
        	}//if(func_num_args() > 0)
        	
   			/**
    		 * Lets setup and execute the Query
    		 */
    		$QueryFieldList = '';
    		$RelationObject = '';
    		foreach($this->FieldList as $FieldObj)
    		{
    			if($FieldObj->Relationship != 'multiple')
    			{
    				$QueryFieldList .= "`{$this->Application}_{$this->Module}`.`{$FieldObj->Name}`,";
    			}
    			else
    			{
    				$RelationObject = $FieldObj; 
    			}
    			
    		}//foreach($this->FieldList as $FieldObj)
    		$QueryFieldList = rtrim($QueryFieldList, ',');
    		
    		$Query = "
    		 	SELECT 
    		 		{$QueryFieldList} 
    		 	FROM
    		 		`{$this->Application}_{$this->Module}`  
    		 	WHERE
    		 	    Deleted = 0
				
    		 ";
    		 		
    		$DatabaseComponent = Zend_Registry::get('Instance/DatabaseObj');
    		$ResultArray = array();
    		$ResultArray = ($DatabaseComponent->Query($Query, array()));
    		 
    		if($RelationObject)
    		{
    		 	
    		 	$RelationshipMatch = array();
        		preg_match('|(.*)_(.*)__(.*)|', $RelationObject->Name, $RelationshipMatch);
        		
        		foreach($ResultArray as $ResKey=>$ResVal)
        		{
        			
        			$Query = "
		    		 	SELECT 
	        				{$RelationshipMatch[0]}
		    		 	FROM
	    		 			{$this->Application}_{$this->Module}_To_{$RelationshipMatch[1]}_{$RelationshipMatch[2]} 
		    		 	WHERE {$this->Application}_{$this->Module}__ID = ?
		    		 ";
	    		 			
	    		 	$Results = $DatabaseComponent->Query($Query, array($ResVal->ID));
	    		 	//foreach($Results as $Key=>$Val)
	    		 	foreach($Results as $Val)
	    		 	{
	    		 		
	    		 		$ResultArray[$ResKey]->{$RelationshipMatch[0]}[] = $Val->{$RelationshipMatch[0]};
	    		 	}
	    		 	
	    		 	//$ClassObj->{$RelationObject->Name}= array_unique($ClassObj->{$RelationObject->Name});
        		}
    		 
    		}
    		 
    		$SignalSlotObj = Zend_Registry::get('Instance/SignalSlotObj'); 
    		$SignalSlotObj->emit($this, __FUNCTION__, "");     
    		 
    		return $ResultArray;
    		
    	}//Public Function BrowseAllActiveRecords()
    	
    	/**
    	 * BrowseAllDeletedRecords Function ( Public )
    	 *
    	 * @return array 
    	 */
    	Public Function BrowseAllDeletedRecords()
    	{
    		
        	
        	if(func_num_args() > 0)
        	{
        		trigger_error('Sorry, but '.__CLASS__.'::'.__FUNCTION__.' does not accept any parameters', E_USER_ERROR);
        		
        	}//if(func_num_args() > 0)
        	
   			/**
    		 * Lets setup and execute the Query
    		 */
    		$QueryFieldList = '';
    		$RelationObject = '';
    		foreach($this->FieldList as $FieldObj)
    		{
    			if($FieldObj->Relationship != 'multiple')
    			{
    				$QueryFieldList .= "`{$this->Application}_{$this->Module}`.`{$FieldObj->Name}`,";
    			}
    			else
    			{
    				$RelationObject = $FieldObj; 
    			}
    			
    		}//foreach($this->FieldList as $FieldObj)
    		$QueryFieldList = rtrim($QueryFieldList, ',');
    		
    		$Query = "
    		 	SELECT 
    		 		{$QueryFieldList} 
    		 	FROM
    		 		`{$this->Application}_{$this->Module}`  
    		 	WHERE
    		 	    Deleted = 1
				
    		";
    		 		
    		$DatabaseComponent = Zend_Registry::get('Instance/DatabaseObj');
    		$ResultArray = array();
    		$ResultArray = ($DatabaseComponent->Query($Query, array()));
    		 
    		if($RelationObject)
    		{
    		 	
    		 	$RelationshipMatch = array();
        		preg_match('|(.*)_(.*)__(.*)|', $RelationObject->Name, $RelationshipMatch);
        		
        		foreach($ResultArray as $ResKey=>$ResVal)
        		{
        			
        			$Query = "
		    		 	SELECT 
	        				{$RelationshipMatch[0]}
		    		 	FROM
	    		 			{$this->Application}_{$this->Module}_To_{$RelationshipMatch[1]}_{$RelationshipMatch[2]} 
		    		 	WHERE {$this->Application}_{$this->Module}__ID = ?
		    		 ";
	    		 			
	    		 	$Results = $DatabaseComponent->Query($Query, array($ResVal->ID));
	    		 	//foreach($Results as $Key=>$Val)
	    		 	foreach($Results as $Val)
	    		 	{
	    		 		
	    		 		$ResultArray[$ResKey]->{$RelationshipMatch[0]}[] = $Val->{$RelationshipMatch[0]};
	    		 	}
	    		 	
	    		 	//$ClassObj->{$RelationObject->Name}= array_unique($ClassObj->{$RelationObject->Name});
        		}
    		 
    		}
    		 
    		$SignalSlotObj = Zend_Registry::get('Instance/SignalSlotObj'); 
    		$SignalSlotObj->emit($this, __FUNCTION__, "");    
    		 
    		return $ResultArray;
    		
    	}//Public Function BrowseAllDeletedRecords()
    	
    	/**
    	 * Search Function ( Public )
    	 *
    	 * @param string $SearchTerm
    	 * @param int $Page
    	 * @return array
    	 */
    	Public Function SearchRecords($SearchTerm, $Page)
    	{
    		if(func_num_args() > 2)
        	{
        		trigger_error('Sorry, but '.__CLASS__.'::'.__FUNCTION__.' does not accept more than one parameter', E_USER_ERROR);
        		
        	}//if(func_num_args() > 2)
        	
        	
   			/**
    		 * First lets clean out old Locks
    		 */
    		$this->CleanLocks();

    		if(strlen($SearchTerm)<1)
    		{
    			$ResultArray = array();
    			$ResultArray = $this->BrowseAllRecords($Page);
    			$ResultArray['Total'] = count($ResultArray);
    			
    			if(count($ResultArray)>0)
    			{
    				foreach($ResultArray as $Result)
    				{
    					$ResultArray['Results'][] = $Result;
    				}
    			}
    			
    			
    		}
    		else 
    		{
	    		$SearchComponent = new SearchComponent();
	    		
	    		$SignalSlotObj = Zend_Registry::get('Instance/SignalSlotObj'); 
	    		$SignalSlotObj->emit($this, __FUNCTION__, $SearchTerm); 
	    		
	    		$Results = $SearchComponent->search($SearchTerm, $Page);
	    		$ResultArray = array();
	    		$ResultArray['Total'] = $Results['total_found'];
	    		
	    		if ( ! empty($Results['matches']) ) 
	    		{
	    			foreach($Results['matches'] as $Key=>$Val)
		    		{
		    			$TempObj = new stdClass();
		    			$TempObj->ID = $Key;
		    			$ResultArray['Results'][] = $this->RetrieveRecord($TempObj);
		    		}
	    		
	    		}
    		}
    		
    		return $ResultArray;
    		
    		
    	}//Public Function Search($SearchTerm)
    	
    	/**
    	 * RetrieveAudit Function ( Public )
    	 *
    	 * @param string $Object
    	 * @return array
    	 */
    	Public Function RetrieveAudit($ClassObj)
    	{  
    		if(func_num_args() > 1)
        	{
        		trigger_error('Sorry, but '.__CLASS__.'::'.__FUNCTION__.' does not accept more than one parameter', E_USER_ERROR);
        		
        	}//if(func_num_args() > 1)
        	
        	
    		
    		$AuditObj = new AuditComponent();
    			
    			
   			$SignalSlotObj = Zend_Registry::get('Instance/SignalSlotObj'); 
    		$SignalSlotObj->emit($this, __FUNCTION__, $ClassObj);  
			
    		
   			return $AuditObj->RetrieveAudit($ClassObj);
    		
    		
    	}//Public Function Search($SearchTerm)
        
       /**
         * Records Object Destructor
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
		
	}//Class RecordsComponent 
	Class RecordsComponentStruct
    {
    	
    	/**
    	 * @var Name
    	 */
    	Public $Name;
    	
    	/**
    	 * @var string Relationship
    	 */
    	Public $Relationship;
    	
    	/**
    	 * @var Type
    	 */
    	Public $Type;
    	
        
        
    }//Class RecordsComponentStruct
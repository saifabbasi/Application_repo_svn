<?php

	/**
     * Audit.Component.php
     *
     * @category   RCS Webservices 
 	 * @package    Components
     * @subpackage Audit
 	 * @copyright  Copyright (c) 2009 RCS
     * @author RCS
     * @version 0.1.2
     */    
    Class AuditComponent Extends ClassComponent 
    {
    	
    	/**
		 * @var string $GUID
		 * From ClassComponent
		 */
		Public 	$GUID 					= NULL;
    	
    	/**
    	 * @var RequiredFields
    	 */
    	Private $RequiredFields = array('ID', 'Deleted', 'Locked', 'Created');
    	
    	/**
         * AuditComponent Object Constructor
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
    	 * CreateAudit Function ( Public )
    	 * 0 is the new array, 1 is the old one
    	 * @param object SignalSlotStructure $StructureObj
    	 * @return void
    	 */
    	Public Function CreateAudit(SignalSlotStructure $StructureObj)
    	{
    		
    		$this->Application = Zend_Registry::get('Instance/Application');
        	$this->Module = Zend_Registry::get('Instance/Module');
        	$this->Function = Zend_Registry::get('Instance/Function');
    		
    		
        	$DatabaseComponent = new DatabaseComponent();
        	$GotNewRevision = false;
            foreach($StructureObj->Parameters[0] as $Key=>$Val)
            {
            	/**
            	 * We don't need to worry about the Token
            	 */
            	if( ($StructureObj->Parameters[0]->$Key != $StructureObj->Parameters[1]->$Key) && (!in_array($Key, $this->RequiredFields))  )
                {
                	
                	
    				if(!$GotNewRevision)
		            {
		            	$Query = "
		            		INSERT INTO
				            	{$this->Application}_{$this->Module}_Revision
		            		(`{$this->Application}_{$this->Module}__ID`, OldRevision, NewRevision)
		            			VALUES
		            		(?, ?, ?)
			    		";
		            		
		            	$NewRevisionID = $DatabaseComponent->Query($Query, array($StructureObj->Parameters[0]->ID, serialize($StructureObj->Parameters[1]), serialize($StructureObj->Parameters[0])) );
		            	
			    		
		            	$GotNewRevision = true;
		            	
		            }//if(!$GotNewRevision)
                	
		            $Query = "
   		    		 	INSERT INTO
   		    		 		{$this->Application}_{$this->Module}_Audit
   		    		 	(`Core_Users__ID`, `{$this->Application}_{$this->Module}__ID`, Field, OldValue, NewValue, `{$this->Application}_{$this->Module}_Revision__ID`)
   		    		 		VALUES
   		    		 	(?, ?, ?, ?, ?, ?);    		 			
   		    		 ";
		    		
                	
                	
                	$Core_Users__ID = 1;	
                	$DatabaseComponent->Query($Query, array($Core_Users__ID, $StructureObj->Parameters[1]->ID, $Key, $StructureObj->Parameters[1]->$Key, $StructureObj->Parameters[0]->$Key, $NewRevisionID ) ) ;
	            	
                   
                }//if($UserObjNew=>$Key != $UserObjOld->$Key)
                
            }//foreach($UserObjNew as $Key=>$Val)
            
    	}//Public Function CreateAudit(SignalSlotStructure $StructureObj)
    	
    	/**
    	 * RetrieveAudit Function ( Public )
    	 * @param string $Module
    	 * @param int $ID
    	 * @return void
    	 */
    	Public Function RetrieveAudit($ClassObj)
    	{
    		
    		$this->Application = Zend_Registry::get('Instance/Application');
        	$this->Module = Zend_Registry::get('Instance/Module');
        	$this->Function = Zend_Registry::get('Instance/Function');
    		
    		$Query = "
   		     	SELECT 
   		     		`Core_Users__ID`, `Field`, `OldValue`, `NewValue`,  `TimeStamp`
   		     	FROM 
       				{$this->Application}_{$this->Module}_Audit
   		     	WHERE 
   		     		{$this->Application}_{$this->Module}__ID = ?
   		     	ORDER BY 
   		     		TimeStamp DESC
   		     	
   		     ";
                           	
            
            $DatabaseComponent = new DatabaseComponent();
            
            return $DatabaseComponent->Query($Query, array($ClassObj->ID) ) ;
            
    	}//Public Function RetrieveAudit($Module, $ID)
    	
    	/**
         * Audit Object Destructor
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
        
    }//Class AuditComponent
<?php

	/**
     * Session.Component.php
     *
     * @category   RCS Framework 
 	 * @package    Components
     * @subpackage Session
 	 * @copyright  Copyright (c) 2009 RCS
     * @author RCS
     * @version 0.1.2
     */
	Class SessionComponent Extends ClassComponent
	{
		
		/**
		 * @var string $GUID
		 * From ClassComponent
		 */
		Public 	$GUID 					= NULL;
		
		/**
		 * @var int TTL
		 */
		Private $TTL = 1800;
		
		
		/**
		 * SessionComponent Constructor
		 * 
		 * @return void
		 */
		Public Function __construct() 
		{

			ini_set('session.save_handler', 'user');
            session_set_save_handler(
                array($this, '_open'),
                array($this, '_close'),
                array($this, '_read'),
                array($this, '_write'),
                array($this, '_destroy'),
                array($this, '_clean')
            );
            
            //register_shutdown_function('session_write_close');
            session_start();
            
            //parent::GenerateGUID();
           
			
		}//Public Function __construct() 
		
		
		
		/**
		 * Session Open Function ( Public ) 
		 * @return bool
		 */
		Public Function _open()
		{
            return true;
            
        }//Public Function _open()
    
        /**
		 * Session Close Function ( Public ) 
		 * @return bool
		 */
        Public Function _close()
        {
            $this->_clean($this->TTL);
            return true;
            
        }//Public Function _close()
        
        /**
		 * Session Read Function ( Public )
		 * 
		 * @param int $id 
		 * @return string
		 */
        Public Function _read($id)
        {
        	$this->_clean($this->TTL);
        	$DatabaseObj = Zend_Registry::get('Instance/DatabaseObj');
        	$Query = "SELECT sessionData FROM bevomedia_sessions WHERE `id` = '$id'";
        	$Values = $DatabaseObj->fetchAll($Query);
        	
        	if(sizeof($Values) > 0)
        	{
        		return $Values[0]->sessionData;
        	}
        	else
        	{
        		return "";
        	}
        	
            
        }//Public Function _read($id)
  
        /**
         * Session Write Function ( Public )
         * 
         * @param int $id
         * @param string $data
         * @return bool
         */
        Public Function _write($id, $data)
        {
        	
        	$DatabaseObj = Zend_Registry::get('Instance/DatabaseObj');
        	
        	$Query = 'REPLACE INTO 
            					bevomedia_sessions 
            				 VALUES 
            				 	(\''.$id.'\', \''.date('U').'\', \''.$data.'\')';
        	
        	$DatabaseObj->query($Query);
        	
        	return true;
        	
            
        }//Public Function _write($id, $data)

        /**
         * Session Destroy Function ( Public ) 
         * 
         * @param $id
         * @return bool
         */
        Public Function _destroy($id)
        {
        	$DatabaseObj = Zend_Registry::get('Instance/DatabaseObj');
        	$Query = 'DELETE FROM 
            					bevomedia_sessions 
            				WHERE 
            					id = \''.$id.'\'';
        	$Values = $DatabaseObj->fetchAll($Query);
        	
        	return $Values;
        	
        	
            
            
        }//Public Function _destroy($id)
        
        /**
         * Session Clean Function ( Public ) 
         * 
         * @param string $max
         * @return bool
         */
        Public Function _clean($max)
        {	
        	$old = (date('U') - $max);
            
        	$DatabaseObj = Zend_Registry::get('Instance/DatabaseObj');
        	$Query = ' DELETE FROM 
            					bevomedia_sessions 
            				 WHERE 
            				 	expires < '.$old.'';
        	
        	
        	$DatabaseObj->query($Query);
        	
        	
            
            
        }//Public Function _clean($max)
        
        Public Function __destruct()
        {
        	//session_write_close();
        	
        }//Public Function __destruct()
        
		
	}//Class SessionComponent 

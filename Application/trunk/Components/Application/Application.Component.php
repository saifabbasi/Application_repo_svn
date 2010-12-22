<?php

	/**
     * ApplicationComponent.Component.php
     *
     * @category   RCS Framework 
 	 * @package    Components
     * @subpackage ApplicationComponent
 	 * @copyright  Copyright (c) 2009 RCS
     * @author RCS
     * @version 0.1.2
     */
	Class ApplicationComponent Extends ClassComponent
	{
		
		/**
		 * @var string $GUID
		 * From ClassComponent
		 */
		Public 	$GUID 					= NULL;
		
		/**
		 * @var MinPHPVersion
		 */
		Private $MinPHPVersion = '5.2.0';
		
		/**
		 * @var FrameworkVersion
		 */
		Private $FrameworkVersion = '0.1.2';
		
		/**
		 * @var TrueWorkingDirectory
		 */
		Public $TrueWorkingDirectory = NULL;

	
		
		/**
         * ApplicationComponent Object Constructor
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

        	$this->TrueWorkingDirectory = realpath(getcwd()).DIRECTORY_SEPARATOR;
        	
        	$this->checkPHPVersion();
	        $this->checkExtensions();
	          
        }//Public Function __construct()
        
        /**
         * checkExtensions Function ( Private )
         *
         * 
         * @return void
         */
        Private Function checkExtensions()
        {
        	if(func_num_args() > 0)
        	{
        		trigger_error('Sorry, but '.__CLASS__.'::'.__FUNCTION__.' does not accept any parameters', E_USER_ERROR);
        		
        	}//if(func_num_args() > 0)
        	
        	if (!extension_loaded('soap'))
        	{
        		trigger_error('RCS Framework '.$this->FrameworkVersion.' requires the SOAP extension to be loaded', E_USER_ERROR);
        		
        	}//if (!extension_loaded('soap'))
        	
        	if (!extension_loaded('pdo'))
        	{
        		trigger_error('RCS Framework '.$this->FrameworkVersion.' requires the PDO extension to be loaded', E_USER_ERROR);
        		
        	}//if (!extension_loaded('pdo'))
        	
        	
        }//Private Function checkExtensions()
        
        
        /**
         * checkPHPFunction Function ( Private )
         *
         * 
         * @return void
         */
        Private Function checkPHPVersion()
        {
        	if(func_num_args() > 0)
        	{
        		trigger_error('Sorry, but '.__CLASS__.'::'.__FUNCTION__.' does not accept any parameters', E_USER_ERROR);
        		
        	}//if(func_num_args() > 0)
        	
        	/*
			 * If PHP 5.1 is not available, we can not continue
			 */
			if (version_compare(PHP_VERSION, $this->MinPHPVersion, '<')) 
			{
				trigger_error('RCS Framework '.$this->FrameworkVersion.' requires at least PHP '.$this->MinPHPVersion, E_USER_ERROR);
				
			}//if (version_compare(PHP_VERSION, $this->MinPHPVersion, '<'))
        	
        }//Private Function checkPHPVersion()
        
        
        /**
         * Run Function ( Public )
         *
         * 
         * @return void
         */
        Public Function Run()
        {

        	if(func_num_args() > 0)
        	{
        		trigger_error('Sorry, but '.__CLASS__.'::'.__FUNCTION__.' does not accept any parameters', E_USER_ERROR);
        		
        	}//if(func_num_args() > 0)
        	
        	/**
			 * This should hopefully be one of the only places we call regular PHP code
			 * The PHP Component should handle the rest.
			 */
        	
        	$GLOBALS['StartTime'] = microtime(true);
        	
        	require($this->TrueWorkingDirectory.DIRECTORY_SEPARATOR.'Components'.DIRECTORY_SEPARATOR.'Loader'.DIRECTORY_SEPARATOR.'Loader.Component.php');        	
        	$LoaderObj = new LoaderComponent();
        	$LoaderObj->Run();
        }//Public Function Run()
        
        /**
         * populatePageLoadData Function ( Private )
         *
         * 
         * @return array
         */
        Private Function populatePageLoadData()
        {
        	if(func_num_args() > 0)
        	{
        		trigger_error('Sorry, but '.__CLASS__.'::'.__FUNCTION__.' does not accept any parameters', E_USER_ERROR);
        		
        	}//if(func_num_args() > 0)
        	
        	$TotalRequiredFileSize = 0;
			
			
			$_PAGE = array();
					
			$_PAGE['System/Current Memory Usage'] = memory_get_usage().' bytes';
			$_PAGE['System/Max Memory Usage'] = memory_get_peak_usage().' bytes';
			
			
			$EndTime = explode( ' ', microtime()); $EndTime = ( (double)$EndTime[0] + (double)$EndTime[1] );
			
	        $Registry = Zend_Registry::getInstance();
			
			foreach ($Registry as $index => $value) 
			{
			    //$_PAGE[$index] = $value;
			}
			
			$_PAGE['System/Page Load Time'] = number_format(((float)$EndTime - (float)$GLOBALS['StartTime']), 4). ' seconds';
			
			return $_PAGE;
	        	
	        }//Private Function populatePageLoadData()
        
        
       /**
         * ApplicationComponent Object Destructor
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
		
	}//Class ApplicationComponent 
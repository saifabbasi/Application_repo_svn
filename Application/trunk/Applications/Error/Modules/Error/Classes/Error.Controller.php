<?php

    /**
     * ErrorController.Component.php
     *
     * @category   RCS Framework 
     * @package    Controllers
     * @subpackage ErrorController
     * @copyright  Copyright (c) 2009 RCS
     * @author RCS
     * @version 0.1.2
     */
	Class ErrorController Extends ClassComponent
	{
		
		/**
         * ErrorController Object Constructor
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
        	
        	Zend_Registry::set('Instance/LayoutType', 'blank-layout');//In the __construct because all items in the User Module use it.
        	
        	$LanguageComponent = new Zend_Translate('Qt', Zend_Registry::get('Instance/LanguagePath'));
        	$this->{'Instance/PageTitle'} = $LanguageComponent->translate('Global Error Controller Page Title');
        	$this->{'Instance/PageKeywords'} = $LanguageComponent->translate('one, keyword');
        	$this->{'Instance/PageDescription'} = $LanguageComponent->translate('Global Error Controller Page Description');
        	$this->{'Instance/ViewTitle'} = $LanguageComponent->translate('Error');
        	$this->{'Instance/ViewByline'} = $LanguageComponent->translate('Oops.');
            
        }//Public Function __construct()
        
        /**
         * _404 Function ( Public )
         *
         * 
         * @return void
         */
        Public Function _404()
        {
        	header("HTTP/1.0 404 Not Found");
        	$LanguageComponent = new Zend_Translate('Qt', Zend_Registry::get('Instance/LanguagePath'));
        	$this->{'Instance/PageTitle'} = $LanguageComponent->translate('404 Error');
        	$this->{'Instance/ViewByline'} = $LanguageComponent->translate("You can't get there from here.");
        	
        }//Public Function _404()
        
        /**
         * _500 Function ( Public )
         *
         * 
         * @return void
         */
        Public Function _500()
        {
        	header('HTTP/1.1 500 Internal Server Error');
        	$LanguageComponent = new Zend_Translate('Qt', Zend_Registry::get('Instance/LanguagePath'));
        	$this->{'Instance/PageTitle'} = $LanguageComponent->translate('500 Error');
        	
        }//Public Function _500()
        
		
       /**
         * ErrorController Object Destructor
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
		
	}//Class ErrorController 
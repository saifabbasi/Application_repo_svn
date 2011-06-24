<?php

    /**
     * LoaderComponent.Component.php
     *
     * @category   RCS Framework 
     * @package    Components
     * @subpackage LoaderComponent
     * @copyright  Copyright (c) 2009 RCS
     * @author RCS
     * @version 0.1.2
     */
	Class LoaderComponent Extends ClassComponent
	{
		
		/**
		 * @var string $GUID
		 * From ClassComponent
		 */
		Public 	$GUID 					= NULL;
		
		/**
		 * @var TrueWorkingDirectory
		 */
		Public $TrueWorkingDirectory = NULL;
		
		/**
         * LoaderComponent Object Constructor
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

        }//Public Function __construct()
        
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
        	$this->loadZendComponents();
        	$this->loadConfig();
        	$this->loadDatabaseObject();
        	$this->loadRCSComponents();
        	$this->setApplicationModuleFunction();
        	$this->checkApplicationModuleFuncitonForErrors();
        	$this->setViewPath();
        	$this->setEmailPath();
        	$this->setLanguagePath();
        	$this->setThemePath();        	
        	$this->executeController();
        	$this->executeView();		
        	$this->display();
        	
        	
        }//Public Function Run()
        
        /**
         * loadRCSComponents Function ( Private )
         *
         * 
         * @return void
         */
        Private Function loadRCSComponents()
        {
        	if(func_num_args() > 0)
        	{
        		trigger_error('Sorry, but '.__CLASS__.'::'.__FUNCTION__.' does not accept any parameters', E_USER_ERROR);
        		
        	}//if(func_num_args() > 0)
			require($this->TrueWorkingDirectory.'Components'.DIRECTORY_SEPARATOR.'Session'.DIRECTORY_SEPARATOR.'Session.Component.php');
        	$SessionObj = new SessionComponent();
        	require($this->TrueWorkingDirectory.'Components'.DIRECTORY_SEPARATOR.'Audit'.DIRECTORY_SEPARATOR.'Audit.Component.php');
        	require($this->TrueWorkingDirectory.'Components'.DIRECTORY_SEPARATOR.'Mail'.DIRECTORY_SEPARATOR.'Mail.Component.php');
        	require($this->TrueWorkingDirectory.'Components'.DIRECTORY_SEPARATOR.'ACL'.DIRECTORY_SEPARATOR.'ACL.Component.php');
		    require($this->TrueWorkingDirectory.'Components'.DIRECTORY_SEPARATOR.'Records'.DIRECTORY_SEPARATOR.'Records.Component.php');
		    require($this->TrueWorkingDirectory.'Components'.DIRECTORY_SEPARATOR.'Cookies'.DIRECTORY_SEPARATOR.'Cookies.Component.php');

		    
		    /**
		     * This is done so that any components can be "global"'d to perform a Signal/Slot on the global item if necessary
		     */
		    Zend_Registry::set('Instance/ApplicationObj', $this);
		    
		    $AuditObj = new AuditComponent();
		    Zend_Registry::set('Instance/AuditObj', $AuditObj);
		    
		    $ACLObj = new ACLComponent();
		    Zend_Registry::set('Instance/ACLObj', $ACLObj);
		    
		    $LoaderObj = new LoaderComponent();
		    Zend_Registry::set('Instance/LoaderObj', $LoaderObj);
		    
		    Zend_Registry::set('Instance/Session', $_SESSION);
		    Zend_Registry::set('COMMONPATH', $this->TrueWorkingDirectory . 'Applications/BevoMedia/Common/');
			
        	
        }//Private Function loadRCSComponents()
        
        
        /**
         * loadConfig Function ( Private )
         *
         * 
         * @return void
         */
        Private Function loadConfig()
        {
        	if(func_num_args() > 0)
        	{
        		trigger_error('Sorry, but '.__CLASS__.'::'.__FUNCTION__.' does not accept any parameters', E_USER_ERROR);
        		
        	}//if(func_num_args() > 0)
        	
        	Zend_Registry::set('StartTime', $GLOBALS['StartTime']);
        	
        	$ConfigArray = array();
        	$ConfigArray = parse_ini_file($this->TrueWorkingDirectory.DIRECTORY_SEPARATOR.'config.ini', true);
        	
        	foreach ($ConfigArray as $ConfigKey => $ConfigVal) 
        	{
        		foreach ($ConfigVal as $ConfigElementKey=>$ConfigElementVal) 
        		{
        			Zend_Registry::set($ConfigKey.'/'.$ConfigElementKey, $ConfigElementVal);	
        			
        		}//foreach ($ConfigVal as $ConfigElementKey=>$ConfigElementVal) 
        		
        	}//foreach ($ConfigArray as $ConfigKey => $ConfigVal)
        	
        	Zend_Registry::set('Instance/LayoutType', 'main-layout');
        	
        	//Zend_Registry::set('Application/MinPHPVersion', $this->MinPHPVersion);
	        //Zend_Registry::set('Application/FrameworkVersion', $this->FrameworkVersion);
	        Zend_Registry::set('Application/TrueWorkingDirectory', $this->TrueWorkingDirectory);
	        if(isset($_SERVER['HTTPS']))
	        {
		        Zend_Registry::set('System/BaseURL', 'https://'.$_SERVER['HTTP_HOST'].'/');
				Zend_Registry::set('System/FullURL', 'https://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']);
				Zend_Registry::set('System/Prefix', 'https://');
	        }
	        else
	        {
	        	Zend_Registry::set('System/BaseURL', 'http://'.$_SERVER['HTTP_HOST'].'/');
				Zend_Registry::set('System/FullURL', 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']);
				Zend_Registry::set('System/Prefix', 'http://');	        	
	        }
	        
	        Zend_Registry::set('Instance/URI_Token', explode('/', $_SERVER['REQUEST_URI']));
	        
			Zend_Registry::set('System/Domain', $_SERVER['HTTP_HOST']);
        	
        	if ((Zend_Registry::get('Application/Mode') == 'Development') || (Zend_Registry::get('Application/Mode') == 'SelfHosted'))
        	{
        		ini_set('display_errors', 'On');
        		ini_set('display_startup_errors', 'On');
        		ini_set('error_reporting', E_ALL);
        		
        	}
        	else 
        	{
        		//For Staging/Production
        		ini_set('display_errors', 'Off');
        		ini_set('display_startup_errors', 'Off');
        		
        	}
        	
        	
        }//Private Function loadConfig()
        
        
        /**
         * loadZendComponents Function ( Private )
         * Here we have a central location for all 
         * Zend components that will be loaded
         * 
         * @return void
         */
        Private Function loadZendComponents()
        {
        	if(func_num_args() > 0)
        	{
        		trigger_error('Sorry, but '.__CLASS__.'::'.__FUNCTION__.' does not accept any parameters', E_USER_ERROR);
        		
        	}//if(func_num_args() > 0)
        	
			require('Zend/Registry.php');
			require('Zend/Db.php');
			require('Zend/View.php');
			require('Zend/Translate.php');
			require('Zend/Cache.php');
			require('Zend/Service/ReCaptcha.php');
			
        }//Private Function loadZendComponents()
        
        /**
         * loadDatabaseObject Function ( Private )
         *
         * 
         * @return void
         */
        Private Function loadDatabaseObject()
        {
        	switch(strtolower(Zend_Registry::get('Database/'.Zend_Registry::get('Application/Mode').'/Type')))
			{
				case 'mysql':
					if (!extension_loaded('pdo_mysql'))
					{
		        		die('RCS Framework '.$this->FrameworkVersion.' requires the MySQL PDO extension to be loaded');
		        		
		        	}//if (!extension_loaded('pdo_mysql'))
		        	
		        	require_once(Zend_Registry::get('Application/TrueWorkingDirectory') .'Applications/BevoMedia/Common/RCS_Db_Adapter/RCS_Db_Adapter.php');
		        	
		        	$DatabaseObj = Zend_Db::factory('Adapter', array(
					   'host' => Zend_Registry::get('Database/'.Zend_Registry::get('Application/Mode').'/Host'),
		    			'username' => Zend_Registry::get('Database/'.Zend_Registry::get('Application/Mode').'/User'),
		    			'password' => Zend_Registry::get('Database/'.Zend_Registry::get('Application/Mode').'/Pass'),
		    			'dbname' => Zend_Registry::get('Database/'.Zend_Registry::get('Application/Mode').'/Name'),
		    			'port' => Zend_Registry::get('Database/'.Zend_Registry::get('Application/Mode').'/Port'),
		        		'adapterNamespace' => 'RCS_Db',
		        		'slaveServers'        => array(
					        array('host' => Zend_Registry::get('Database/Slave_1/'.Zend_Registry::get('Application/Mode').'/Host'), 'username' => Zend_Registry::get('Database/Slave_1/'.Zend_Registry::get('Application/Mode').'/User'), 'password'=> Zend_Registry::get('Database/Slave_1/'.Zend_Registry::get('Application/Mode').'/Pass'), 'dbname' => Zend_Registry::get('Database/Slave_1/'.Zend_Registry::get('Application/Mode').'/Name')),
 					    )
					));
					
//					$config = array(
//					    'adapter'        => 'Pdo_Mysql',
//					    'driver_options' => array(PDO::ATTR_TIMEOUT=>5),
//					    'username' => Zend_Registry::get('Database/'.Zend_Registry::get('Application/Mode').'/User'),
//		    			'password' => Zend_Registry::get('Database/'.Zend_Registry::get('Application/Mode').'/Pass'),
//		    			'dbname' => Zend_Registry::get('Database/'.Zend_Registry::get('Application/Mode').'/Name'),
//						'port' => Zend_Registry::get('Database/'.Zend_Registry::get('Application/Mode').'/Port'),
//					    'master_servers' => 1,
//					    'servers'        => array(
//					        array('host' => '10.212.115.0'),
//					        array('host' => '10.212.75.6')
// 					    )
//					);
//					
//					$config = array(
//					    'adapter'        => 'Pdo_Mysql',
//					    'driver_options' => array(PDO::ATTR_TIMEOUT=>5),
//					    'dbname'         => Zend_Registry::get('Database/'.Zend_Registry::get('Application/Mode').'/Name'),
//					    'master_servers' => 1,
//					    'servers'        => array(
//					        array('host' => '10.212.115.0', 'username' => Zend_Registry::get('Database/'.Zend_Registry::get('Application/Mode').'/User'), 'password'=> Zend_Registry::get('Database/'.Zend_Registry::get('Application/Mode').'/Pass')),
//					        array('host' => '10.212.75.6', 'username' => Zend_Registry::get('Database/'.Zend_Registry::get('Application/Mode').'/User'), 'password'=> Zend_Registry::get('Database/'.Zend_Registry::get('Application/Mode').'/Pass')),
//					    )
//					);
					
//					$DatabaseObj = Zend_Db::factory('Pdo_Mysql', $config);
					
//					print_r($DatabaseObj);die;
					
			    	$DatabaseObj->setFetchMode(Zend_Db::FETCH_OBJ);
			    	$DatabaseObj->query("SET NAMES 'utf8'");
			    	$DatabaseObj->query("SET CHARACTER SET 'utf8'");
			    	
				break;
			
			}
			Zend_Registry::set('Instance/DatabaseObj', $DatabaseObj);
	    	
        	
        }//Private Function loadDatabaseObject()
        
        /**
         * setApplicationModuleFunction Function ( Private )
         *
         * 
         * @return void
         */
        Private Function setApplicationModuleFunction()
        {
        	
        	if(func_num_args() > 0)
        	{
        		trigger_error('Sorry, but '.__CLASS__.'::'.__FUNCTION__.' does not accept any parameters', E_USER_ERROR);
        		
        	}//if(func_num_args() > 0)
        	
        	$FoundRoute = false;
        	
        	foreach(Zend_Registry::get("Router/Routes") as $Route)
        	{
        		
        		$RouteMatches = array();
        		$RouteArray = array();
        		
        		/*
        		 * 0 - Name
        		 * 1 - RegExp
        		 * 2 - Application Match
        		 * 3 - Module Match
        		 * 4 - Function Match
        		 */
        		$RouteArray = explode(",", $Route);
        		
        		if(preg_match($RouteArray[1], $_SERVER['REQUEST_URI'],$RouteMatches))
        		{	
        			$FoundRoute = true;
        			
        			//Application 
        			if(is_numeric($RouteArray[2]))
        			{
        				Zend_Registry::set('Instance/Application', $RouteMatches[intval($RouteArray[2])]);
        			}
        			else 
        			{
        				Zend_Registry::set('Instance/Application', $RouteArray[2]);
        			}
        			
        			//Module 
        			if(is_numeric($RouteArray[3]))
        			{
        				Zend_Registry::set('Instance/Module', $RouteMatches[intval($RouteArray[3])]);
        			}
        			else 
        			{
        				Zend_Registry::set('Instance/Module', $RouteArray[3]);
        			}
        			
        			//Function 
        			if(is_numeric($RouteArray[4]))
        			{
        				Zend_Registry::set('Instance/Function', $RouteMatches[intval($RouteArray[4])]);
        			}
        			else 
        			{
        				Zend_Registry::set('Instance/Function', $RouteArray[4]);
        			}
        			
        			
        			break;
        		}
        		
        	}
        	
        	if(!$FoundRoute)
        	{
        		Zend_Registry::set('Instance/Application', 'Error');
    			Zend_Registry::set('Instance/Module', 'Error');
    			Zend_Registry::set('Instance/Function', '_404');
        	}
        	
        	Zend_Registry::set('Instance/Function', preg_replace('/[^a-z0-9]/i', '_', Zend_Registry::get('Instance/Function')));
        	
        	
        }//Private Function setApplicationModuleFunction()
        
        /**
         * checkApplicationModuleFuncitonForErrors Function ( Private )
         *
         * 
         * @return void
         */
        Private Function checkApplicationModuleFuncitonForErrors()
        {
        	
        	if(func_num_args() > 0)
        	{
        		trigger_error('Sorry, but '.__CLASS__.'::'.__FUNCTION__.' does not accept any parameters', E_USER_ERROR);
        		
        	}//if(func_num_args() > 0)
        	
        	if( (strlen(Zend_Registry::get('Instance/Application')) == 0) || (strlen(Zend_Registry::get('Instance/Module')) == 0) || (strlen(Zend_Registry::get('Instance/Function')) == 0)  )
        	{
        		
        		Zend_Registry::set('Instance/Application', 'Error');
    			Zend_Registry::set('Instance/Module', 'Error');
    			Zend_Registry::set('Instance/Function', '_404');
        	}
        	else 
        	{
        		if(!isset($_GET['wsdl']) && !isset($_GET['soap']) && !isset($_GET['rest']))
        		{
	        		Zend_Registry::set('Instance/Application', Zend_Registry::get('Instance/Application'));
	        		Zend_Registry::set('Instance/Module', Zend_Registry::get('Instance/Module'));
	        		Zend_Registry::set('Instance/Function', Zend_Registry::get('Instance/Function'));
	        		
	        		Zend_Registry::set('Instance/ControllerFile', 
	        			Zend_Registry::get('Application/TrueWorkingDirectory').
		        			DIRECTORY_SEPARATOR.'Applications'.
		        				DIRECTORY_SEPARATOR.Zend_Registry::get('Instance/Application').
		        					DIRECTORY_SEPARATOR.'Modules'.
		        						DIRECTORY_SEPARATOR.Zend_Registry::get('Instance/Module').
		        							DIRECTORY_SEPARATOR.'Classes'.
		        								DIRECTORY_SEPARATOR.Zend_Registry::get('Instance/Module').'.Controller.php');
	    			
		        	if(!file_exists(Zend_Registry::get('Instance/ControllerFile')))
					{
						Zend_Registry::set('Instance/Application', 'Error');
		    			Zend_Registry::set('Instance/Module', 'Error');
		    			Zend_Registry::set('Instance/Function', '_404');
		    			
		    			Zend_Registry::set('Instance/ControllerFile', 
			        		Zend_Registry::get('Application/TrueWorkingDirectory').
			        			DIRECTORY_SEPARATOR.'Applications'.
			        				DIRECTORY_SEPARATOR.Zend_Registry::get('Instance/Application').
			        					DIRECTORY_SEPARATOR.'Modules'.
			        						DIRECTORY_SEPARATOR.Zend_Registry::get('Instance/Module').
			        							DIRECTORY_SEPARATOR.'Classes'.
			        								DIRECTORY_SEPARATOR.Zend_Registry::get('Instance/Module').'.Controller.php');
					}  

        		}
        		else
        		{
        			Zend_Registry::set('Instance/Application', Zend_Registry::get('Instance/Application'));
	        		Zend_Registry::set('Instance/Module', Zend_Registry::get('Instance/Module'));
	        		Zend_Registry::set('Instance/Function', Zend_Registry::get('Instance/Function'));
	        		
	        		Zend_Registry::set('Instance/ControllerFile', 
	        			Zend_Registry::get('Application/TrueWorkingDirectory').
		        			DIRECTORY_SEPARATOR.'Applications'.
		        				DIRECTORY_SEPARATOR.Zend_Registry::get('Instance/Application').
		        					DIRECTORY_SEPARATOR.'Modules'.
		        						DIRECTORY_SEPARATOR.Zend_Registry::get('Instance/Module').
		        							DIRECTORY_SEPARATOR.'Classes'.
		        								DIRECTORY_SEPARATOR.Zend_Registry::get('Instance/Module').'.Webservice.php');
	    			
		        	if(!file_exists(Zend_Registry::get('Instance/ControllerFile')))
					{
						
						Zend_Registry::set('Instance/Application', 'Error');
		    			Zend_Registry::set('Instance/Module', 'Error');
		    			Zend_Registry::set('Instance/Function', '_404');
		    			
		    			Zend_Registry::set('Instance/ControllerFile', 
			        		Zend_Registry::get('Application/TrueWorkingDirectory').
			        			DIRECTORY_SEPARATOR.'Applications'.
			        				DIRECTORY_SEPARATOR.Zend_Registry::get('Instance/Application').
			        					DIRECTORY_SEPARATOR.'Modules'.
			        						DIRECTORY_SEPARATOR.Zend_Registry::get('Instance/Module').
			        							DIRECTORY_SEPARATOR.'Classes'.
			        								DIRECTORY_SEPARATOR.Zend_Registry::get('Instance/Module').'.Webservice.php');
					}  
        		}
        	}
        	
        }//Private Function checkApplicationModuleFuncitonForErrors()
        
        /**
         * setViewPath Function ( Private )
         *
         * 
         * @return void
         */
        Private Function setViewPath()
        {
        	if(func_num_args() > 0)
        	{
        		trigger_error('Sorry, but '.__CLASS__.'::'.__FUNCTION__.' does not accept any parameters', E_USER_ERROR);
        		
        	}//if(func_num_args() > 0)
        	
        	Zend_Registry::set('Instance/ViewPath', 
        		Zend_Registry::get('Application/TrueWorkingDirectory').
        			DIRECTORY_SEPARATOR.'Applications'.
        				DIRECTORY_SEPARATOR.Zend_Registry::get('Instance/Application').
        					DIRECTORY_SEPARATOR.'Modules'.
        						DIRECTORY_SEPARATOR.Zend_Registry::get('Instance/Module').
        							DIRECTORY_SEPARATOR.'Views'.
        								DIRECTORY_SEPARATOR);
        	
        }//Private Function setViewPath()
        
        /**
         * setLanguagePath Function ( Private )
         *
         * 
         * @return void
         */
        Private Function setLanguagePath()
        {
        	if(func_num_args() > 0)
        	{
        		trigger_error('Sorry, but '.__CLASS__.'::'.__FUNCTION__.' does not accept any parameters', E_USER_ERROR);
        		
        	}//if(func_num_args() > 0)
        	
        	Zend_Registry::set('Instance/LanguagePath', 
        		Zend_Registry::get('Application/TrueWorkingDirectory').
        			DIRECTORY_SEPARATOR.'Applications'.
        				DIRECTORY_SEPARATOR.Zend_Registry::get('Instance/Application').
        					DIRECTORY_SEPARATOR.'Language'.
        						DIRECTORY_SEPARATOR);
        	
        }//Private Function setLanguagePath()
        
        /**
         * setThemePath Function ( Private )
         *
         * 
         * @return void
         */
        Private Function setThemePath()
        {
        	if(func_num_args() > 0)
        	{
        		trigger_error('Sorry, but '.__CLASS__.'::'.__FUNCTION__.' does not accept any parameters', E_USER_ERROR);
        		
        	}//if(func_num_args() > 0)
        	
        	Zend_Registry::set('Instance/ThemePath', 
        		Zend_Registry::get('Application/TrueWorkingDirectory').
        				DIRECTORY_SEPARATOR.'Themes'.
	        				DIRECTORY_SEPARATOR.Zend_Registry::get('Application/Theme').
        						DIRECTORY_SEPARATOR);
        	
        }//Private Function setThemePath()
        
        /**
         * setEmailPath Function ( Private )
         *
         * 
         * @return void
         */
        Private Function setEmailPath()
        {
        	if(func_num_args() > 0)
        	{
        		trigger_error('Sorry, but '.__CLASS__.'::'.__FUNCTION__.' does not accept any parameters', E_USER_ERROR);
        		
        	}//if(func_num_args() > 0)
        	
        	Zend_Registry::set('Instance/EmailPath', 
        		Zend_Registry::get('Application/TrueWorkingDirectory').
        			DIRECTORY_SEPARATOR.'Applications'.
        				DIRECTORY_SEPARATOR.Zend_Registry::get('Instance/Application').
        					DIRECTORY_SEPARATOR.'Modules'.
        						DIRECTORY_SEPARATOR.Zend_Registry::get('Instance/Module').
        							DIRECTORY_SEPARATOR.'Emails'.
        								DIRECTORY_SEPARATOR);
        	
        }//Private Function setEmailPath()
        
        
        /**
         * executeController Function ( Private )
         *
         * 
         * @return void
         */
        Private Function executeController()
        {
        	if(func_num_args() > 0)
        	{
        		trigger_error('Sorry, but '.__CLASS__.'::'.__FUNCTION__.' does not accept any parameters', E_USER_ERROR);
        		
        	}//if(func_num_args() > 0)

        	require(Zend_Registry::get('Instance/ControllerFile'));
        	Zend_Registry::set('Instance/ControllerObjectName', Zend_Registry::get('Instance/Module').'Controller');
        	$ControllerObjectName = Zend_Registry::get('Instance/ControllerObjectName');
        	
        	${$ControllerObjectName} = new $ControllerObjectName();
        	
        	Zend_Registry::set('Instance/ControllerObject', ${$ControllerObjectName});
        	
        	/**
			 * Lets execute the function ( if it exists ) 
			 */
			if ( method_exists(${$ControllerObjectName}, Zend_Registry::get('Instance/Function') ) )
			{
				${$ControllerObjectName}->{Zend_Registry::get('Instance/Function')}();
				
			}//if ( method_exists(${$ControllerObjectName}, Zend_Registry::get('Instance/Function') ) )
			
        	
        	
        }//Private Function executeController()
        
        /**
         * executeView Function ( Private )
         *
         * 
         * @return void
         */
        Private Function executeView()
        {
        	if(func_num_args() > 0)
        	{
        		trigger_error('Sorry, but '.__CLASS__.'::'.__FUNCTION__.' does not accept any parameters', E_USER_ERROR);
        		
        	}//if(func_num_args() > 0)
        	
        	$ViewObj = new Zend_View();
        	$ViewObj->setScriptPath(Zend_Registry::get('Instance/ViewPath'));
        	
        	$ControllerObject = Zend_Registry::get('Instance/ControllerObject');
        	$Registry = Zend_Registry::getInstance();
        	$ViewObj->assign((array) $Registry );
        	
        	$ControllerObject = (array)$ControllerObject;
        	$validArray = array();
        	foreach ( $ControllerObject as $key=>$value) {
        	    if (!strstr($key, "\0")) {
                    $validArray[$key] = $value;
        	    } 
            }
            
            $ViewObj->assign($validArray);
        	//$ViewObj->assign((array) $ControllerObject);
        	
        	Zend_Registry::set('Instance/ViewContent', $ViewObj->render(Zend_Registry::get('Instance/Function').'.view.php'));
        	
        	/**
        	 * For AJAX calls
        	 */
        	if(isset($_GET['ajax']))
        	{
        		print '<span id="ajaxValue">'.Zend_Registry::get('Instance/ViewContent').'</span>';die;
        	}
        	
        }//Private Function executeView()
        
        /**
         * display Function ( Private )
         *
         * 
         * @return void
         */
        Private Function display()
        {
        	if(func_num_args() > 0)
        	{
        		trigger_error('Sorry, but '.__CLASS__.'::'.__FUNCTION__.' does not accept any parameters', E_USER_ERROR);
        		
        	}//if(func_num_args() > 0)
        	
        	$ViewObj = new Zend_View();
        	
        	$ViewObj->setScriptPath(Zend_Registry::get('Instance/ThemePath'));
        	$ControllerObject = Zend_Registry::get('Instance/ControllerObject');
        	
        	$validArray = array();
        	foreach ( $ControllerObject as $key=>$value) {
        	    if (!strstr($key, "\0")) {
                    $validArray[$key] = $value;
        	    } 
            }
        	
        	
        	$Registry = Zend_Registry::getInstance();
        	$ViewObj->assign((array) $Registry );
        	//$ViewObj->assign((array) $ControllerObject);
        	$ViewObj->assign($validArray);
        	
        	print $ViewObj->render(Zend_Registry::get('Instance/LayoutType').'.php');
        	
        	
        	
        }//Private Function display()
        
       /**
         * LoaderComponent Object Destructor
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
		
	}//Class LoaderComponent 

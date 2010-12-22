<?php

	/**
     * Cookies.Component.php
     *
     * @category   RCS Framework 
 	 * @package    Components
     * @subpackage Cookies
 	 * @copyright  Copyright (c) 2009 RCS
     * @author RCS
     * @version 0.1.2
     */
	Class CookiesComponent Extends ClassComponent 
	{
	
	    /**
		 * @var string $GUID
		 * From ClassComponent
		 */
		Public 	$GUID 					= NULL;
		
		/**
         * ACL Object Constructor
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
		
		
		/*
			Creates a cookie
			<code>
			   $rcsSite->createCookie('cookieName', 'cookieValue', 86400);
		    </code>
		*/
		Public Function createCookie($name, $value, $length=NULL)
		{
			setrawcookie($name, rawurlencode($value), $length, "/", ".".str_replace("http://", "", $_SERVER['SERVER_NAME']));                        
			return;	
		}
		
		/*
			Destroys a cookie
			<code>
			   $rcsSite->destroyCookie('cookieName');
		    </code>
		*/
		Public Function destroyCookie($cookie)
		{
			setcookie($cookie, false, time()-2592000);
			setcookie($cookie, false, time()-2592000, '/');
        	setcookie($cookie, false, time()-2592000, ".".str_replace("http://", "", $_SERVER['SERVER_NAME']));
		}
		
		/*
			Destroys all cookies
			<code>
			   $rcsSite->destroyCookie();
		    </code>
		*/
		Public Function destroyAllCookies()
		{
			foreach($_COOKIE as $key=>$cookie) {
    			if($key != 'PHPSESSID')
    			{
				    $key = rtrim(trim($key));
				    setcookie($key, false, time()-2592000);
				    setcookie($key, false, time()-2592000, '/');
        		    setcookie($key, false, time()-2592000, ".".str_replace("http://", "", $_SERVER['SERVER_NAME']));
    		    }
		    }
		    
		}
		
		
		/**
         * Cookies Object Destructor
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
		
	}//Class CookiesComponent
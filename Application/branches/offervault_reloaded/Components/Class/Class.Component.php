<?php

	/**
     * ClassComponent.Component.php
     *
     * @category   RCS Framework 
 	 * @package    Components
     * @subpackage ClassComponent
 	 * @copyright  Copyright (c) 2009 RCS
     * @author RCS
     * @version 0.1.2
     */
	Class ClassComponent 
	{
		
		Private 	$GUID 					= NULL;
		
		/**
         * ClassComponent Object Constructor
         * 
         * @return void
         */
		Public Function __construct()
        {
        	if(func_num_args() > 0)
        	{
        		trigger_error('Sorry, but '.__CLASS__.'::'.__FUNCTION__.' does not accept any parameters', E_USER_ERROR);
        		
        	}//if(func_num_args() > 0)    
            
        }//Public Function __construct()
        
	
        //BEGIN FUNCTIONS ADDED FROM INCLUDE RIP
        //BEGIN FUNCTIONS ADDED FROM INCLUDE RIP
        //BEGIN FUNCTIONS ADDED FROM INCLUDE RIP
        Public Static Function addDays($date, $increment=30)
		{
			if ( trim($date) == "" || $date == "0000-00-00" || $date == "0000-00-00 00:00:00" )
				return 0;


			// In Format YYYY-MM-DD
			$year	= substr($date,0,4);
			$month	= substr($date,5,2);
			$day	= substr($date,8,2);

			// Out Format YYYY-MM-DD
			return date("Y-m-d", mktime(0, 0, 0, $month, $day + ($increment), $year));
		}
        
		Public Static Function divideEx($op1, $op2)
		{
			if ( $op2 != 0 )
				return $op1 / $op2;
	
			return $op1;
		}
        //ENDOF FUNCTIONS ADDED FROM INCLUDE RIP
        //ENDOF FUNCTIONS ADDED FROM INCLUDE RIP
        //ENDOF FUNCTIONS ADDED FROM INCLUDE RIP
			
        /**
         * Encrypt Function ( Private )
         *
         * @param string $string
         * @param string $key 
         * @return string 
         */
        Protected Function Encrypt($string, $key) 
        {
			$result = '';
			for($i=0; $i<strlen($string); $i++) {
				$char = substr($string, $i, 1);
				$keychar = substr($key, ($i % strlen($key))-1, 1);
				$char = chr(ord($char)+ord($keychar));
				$result.=$char;
			}
			
			return base64_encode($result);
			
		}//Protected Function Encrypt($string, $key)

		/**
         * Decrypt Function ( Private )
         *
         * @param string $string
         * @param string $key 
         * @return string 
         */
        Protected Function Decrypt($string, $key) 
		{
			$result = '';
			$string = base64_decode($string);
			
			for($i=0; $i<strlen($string); $i++) {
				$char = substr($string, $i, 1);
				$keychar = substr($key, ($i % strlen($key))-1, 1);
				$char = chr(ord($char)-ord($keychar));
				$result.=$char;
			}
			
			return $result;
			
		}//Protected Function Decrypt($string, $key) 
        
        /**
         * GenerateGUID Function ( Private )
         *
         * @param void 
         * @return void
         */
        Protected Function GenerateGUID()
        {
        	$salt = "abcdefghijklmnpqrstuvwxyzABCDEFGHIJKLMNPQRSTUVWXYZ1234567890";
			srand((double)microtime()*1000000);
			for ($i=0;$i<25;$i++)
			{
				$this->GUID = $this->GUID . substr ($salt, rand() % strlen($salt), 1);
			}
			return $this->GUID;
        	
        }//Protected Function GenerateGUID()
        
        
       /**
         * ClassComponent Object Destructor
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
		
	}//Class ClassComponent 
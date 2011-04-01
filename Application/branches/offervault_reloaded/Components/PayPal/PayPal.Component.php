<?php

	/**
     * PayPal.Component.php
     *
     * @category   RCS Framework 
 	 * @package    Components
     * @subpackage PayPal
 	 * @copyright  Copyright (c) 2009 RCS
     * @author RCS
     * @version 0.1.2
     */
	Class PayPalComponent Extends ClassComponent 
	{
		
		/**
		 * @var string $GUID
		 * From ClassComponent
		 */
		Public 	$GUID 					= NULL;
		
		/**
		 * @var business
		 */
		Public $business;
		
		/**
		 * @var itemName
		 */
		Public $itemName;
		
		/**
		 * @var currencyCode
		 */
		Public $currencyCode = 'USD';
		
		/**
		 * @var itemNumber
		 */
		Public $itemNumber;
		
		/**
		 * @var amount
		 */
		Public $amount;
		
		/**
		 * @var tax
		 */
		Public $tax;
		
		/**
		 * @var invoice
		 */
		Public $invoice;
		
		/**
		 * @var returnURL
		 */
		Public $returnURL;
		
		/**
		 * @var content
		 */
		Public $content;
		
		
		
		/**
         * PayPal Object Constructor
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
         * processPayment Function ( Public )
         *
         * 
         * @return void
         */
        Public Function processPayment()
        {
        	
			if($this->content == '')
			{
			echo "
			<html>
			 <head>
			   <meta HTTP-EQUIV='Expires' CONTENT='Tue, 01 Jan 1980 1:00:00 GMT'>
			   <meta HTTP-EQUIV='Pragma' CONTENT='no-cache'>
			  <title></title>
			</head>
			<body >
			<div align='center'>
			<font face='arial' size='4'><B>Processing ...</B></font><BR><BR>
			<font face='arial' size='2'>
			<a href='javascript:document.paypal.submit();' style='text-decoration:none;'>
				Please make sure to follow the entire PayPal process. 
				<br />
				Your order will not processed until payment is completed and you follow the instructions on redirecting you back to this site.
				<br />
				Please click here to proceed
				<!-- (This may take a moment, please do not refresh or leave this page.  You will automatically be redirected.) -->
			</a>
			</font>
			</div>
			<form action='https://www.paypal.com/cgi-bin/webscr' method='post' name='paypal'>
			<input type='hidden' name='cmd' value='_xclick'>
			<input type='hidden' name='business' value='{$this->business}'>
			<input type='hidden' name='item_name' value='{$this->itemName}'>
			<input type='hidden' name='currency_code' value='{$this->currencyCode}'>
			<input type='hidden' name='item_number' value='{$this->itemNumber}'>
			<input type='hidden' name='amount' value='{$this->amount}'>
			<input type='hidden' name='tax' value='{$this->tax}'>
			<input type='hidden' name='invoice' value='{$this->invoice}'>
			<input type='hidden' name='rm' value='2'>
			<input type='hidden' name='return' value='{$this->returnURL}'>
			</form>
			</body>
			</html>
			"; 
			}
			else
			{
				print $this->content;
			}
			  
			die;
        	
        }//Public Function processPayment()
        
        
       /**
         * PayPal Object Destructor
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
		
	}//Class PayPalComponent -->
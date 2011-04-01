<?php 

require_once('LinkTrust.Abstract.php');

/**
 * Adfinity.php
 *
 * @category   RCS Framework 
 * @package    Modules
 * @subpackage Bevomedia
 * @copyright  Copyright (c) 2009 RCS
 * @author RCS
 * @version 0.1.2
 */
class Adfinity Extends LinkTrustAbstract {
	/**
	 * @access protected
	 * @var string $apiUrl
	 */
	protected $apiUrl = '';
	
	/**
	 * @access protected
	 * @var string $offersUrl
	 */
	protected $offersUrl = 'http://publishers.adfinity.com/RequestOffers.aspx';
	
	/**
	 * @access protected
	 * @var string $loginUrl
	 */
	protected $loginUrl = 'http://publishers.adfinity.com/Welcome/LogIn.aspx';
	
	/**
	 * @access protected
	 * @var string $offersUsername
	 */
	protected $offersUsername = '107715';
	
	/**
	 * @access protected
	 * @var string $offersPassword
	 */
	protected $offersPassword = 'yoyoyo1025';

	/**
	 * @access protected
	 * @var string $viewState
	 */			
	protected $viewState = '1MY1kFhzW/xKkJbiZ93dWRUvDKA+tCYiJnVR08kF5f7UvI3FCfPMIGeF+/BcD17T';

	/**
	 * @access protected
	 * @var string $eventValidation
	 */			
	protected $eventValidation = 'JZk54W0Bakbo5HuHMAOPqt5Y+CLt/Dq+PKv996ydCbpAWHMLhaF3tBr+Xf4CuxZ9/hBe9vILNbY=';

	/**
	 * @access protected
	 * @var string $userNameFieldName
	 */			
	protected $userNameFieldName = 'ctl00$ContentPlaceHolder1$lcLogin$txtUserName';

	/**
	 * @access protected
	 * @var string $userNameFieldId
	 */			
	protected $userNameFieldId = 'ctl00_ContentPlaceHolder1_lcLogin_txtUserName';

	/**
	 * @access protected
	 * @var string $passwordFieldName
	 */			
	protected $passwordFieldName = 'ctl00$ContentPlaceHolder1$lcLogin$txtPassword';

	/**
	 * @access protected
	 * @var string $passwordFieldId
	 */			
	protected $passwordFieldId = 'ctl00_ContentPlaceHolder1_lcLogin_txtPassword';

	/**
	 * @access protected
	 * @var string $submitButtonName
	 */			
	protected $submitButtonName = 'ctl00$ContentPlaceHolder1$lcLogin$btnSubmit';

	/**
	 * @access protected
	 * @var string $submitButtonId
	 */			
	protected $submitButtonId = 'ctl00_ContentPlaceHolder1_lcLogin_btnSubmit';
	
	/**
	 * @access protected
	 * @var string $csvUrl
	 */
	protected $csvUrl = 'http://publishers.adfinity.com/RptSubIdBreakout.aspx';
	
	/**
	 * @access protected
	 * @var string $viewState
	 */			
	protected $csvViewState = 'N/ZV/ju2B/C8VXquLklz61hKlnztN5vMXbKDej0VjCJqua+kdp2bxHvDx9oyz0QFPDH+e5Fv6Xgtpfuw3plnyQsx+FLKKtMHQxazpThqkfwar/rnoKBnHe+zZetFfNQrMpwJieuKAR6en1shWdJRasiDhWJr+PtnspSeGrLa8Trp7bFx1zmaGfRuTFOZa/nPUJRY/A8tb9d4sj3aYv8dfqy2KGSHK2zUwdUvEQ4SccXia+niacp3yjVZZnWpkiXI15QRTVcKICzcT53bhxb3NRGkM9zWRu9uQqHsNmkTinZNfUeeH70LkSeSMZbB7WiRKevTQuC8zfkv4rKsVaAk9Q09CiqAZp2up0ljL/2qSWQY0Yg4KUjB1PK+1lNzkljwakSJdleH3/jiex/qoNILFfIsaerKEzi3QbI22bs5dFxF+I5FQxKc9qNqNmTJwzIjPZjTIfdpeQ47He1Ogfv8cqczByoW67vhGCIPEitPfe4WPWmTUBkrgd80xCmwTX1CkYQhDjdONjeDvuPgNY+VPhybNjzMPn/wfQI86zZzLkvkoN/4f4gw7GvXb+O0ZeZtsMOOQqoyaVgUkNsAbec8kJ/pZ4sD9gats7gneENdRFL5Qjc3fXml6TXUywPO2aRJ3OicZZMmkcjAW9MngO2WtmeU1zeYSzM7cL2oJatFDH52SnHGWapBxPGu1NFMakCfmKNOVC43S7pZbdF5F87M15m6WoIBzqDqmdNYB7T0ZNy1IjCzVLsmW/APnietdGSrw0RBJv7bMifRgpVHePnAk/M9FM1wocCgTfP278foIWa8KoqnAHt57Fc8gIe1z811fiUK7o1Z2btFJzKcEyExFakEiFhowGmARb52TGlcS8pn+3ptsXFPPgLtKAwwQ1JVyhFNzwr6sGVK69uzFvEdTdFHnWxCZKJWMQ9NSAOxLDNT9lTodyr2d+AejnL4jHOrBkzCP2CuW/uDHSa5OpDlkmfnQPNjpUXCK0OY9clo1pudO4sLX9E/t5VM/P0HdZ7DD9OiByIF2CrRgv21jfAtxpbVdNAzG993ulyLivhXn0puTqmoogc+HJH7UtDn7jAH5MPHZhtbwmyxGKnk/Iy342H0GI+XE7O95+WHHLdvEZDGyFA+ozHyUUzc2F+xl8m6RFdZc8CKYUy5r9q5ULqjQroFliMK9MebjvlLCwXYPq0rj2FudJsYxp+DS4ByAtGBU6pESk5qwhHa8UHW5TDnyzi6q6EE0ByOejKR97u483XwB3A6Yp8fXmQmLnEayl564SV6ZBfob+gnMeM/AoiUBxLBfEiv//b7nistS+Hj3h2bk/gWFO/QdTqY2/IgJjbM3GQqTvXpTh8QxGvHA107PvPNFYalbBTSqHfQAhW5QUWvDQbHYGTIacGQZnlxL5MDzGxUNT4y+oBs75ZaWw+osm7Sq1b5OAwwre6KmzBt60V5+9JS/Nk8audcL2Yb3LESm48JhihC5NDe+K7QcpneVrjpQO2GCnpc';

	/**
	 * @access protected
	 * @var string $eventValidation
	 */			
	protected $csvEventValidation = 'dGhWvmQ5vhLNApmKsw9P6vwrM2zF++tFdMlw4xGygktB5fph496APHv1rnnZlWHi+i3xdxU1RYS8t4cDfc8vYG+GWh/txpiMS7IoaXn3xwd0YuOJtqg1xSbF5/GPsh3sCGNwJ1S8Z0NdWJBmElwHp0X245rhruetVZdYJRq2+LC91kqLELiMGg==';
	
	/**
	 * Adfinity class constructor.
	 *
	 */
	Public Function __construct()
	{

	}
}
<?php 

require_once('LinkTrust.Abstract.php');

/**
 * Epicenter.php
 *
 * @category   RCS Framework 
 * @package    Modules
 * @subpackage Bevomedia
 * @copyright  Copyright (c) 2009 RCS
 * @author RCS
 * @version 0.1.2
 */
class Epicenter Extends LinkTrustAbstract {
	/**
	 * @access protected
	 * @var string $apiUrl
	 */
	protected $apiUrl = '';
	
	/**
	 * @access protected
	 * @var string $offersUrl
	 */
	protected $offersUrl = 'http://affiliates.epicenternetwork.com/RequestOffers.aspx';
	
	/**
	 * @access protected
	 * @var string $loginUrl
	 */
	protected $loginUrl = 'http://affiliates.epicenternetwork.com/Welcome/LogInAndSignUp.aspx';
	
	/**
	 * @access protected
	 * @var string $offersUsername
	 */
	protected $offersUsername = '107704';
	
	/**
	 * @access protected
	 * @var string $offersPassword
	 */
	protected $offersPassword = 'yoyoyo1025';

	/**
	 * @access protected
	 * @var string $viewState
	 */
	protected $viewState = '';

	/**
	 * @access protected
	 * @var string $eventValidation
	 */
	protected $eventValidation = '';

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
	protected $csvUrl = 'http://affiliates.epicenternetwork.com/RptSubIdBreakout.aspx';
	
	/**
	 * @access protected
	 * @var string $viewState
	 */
	protected $csvViewState = 'N/ZV/ju2B/C8VXquLklz61hKlnztN5vMBsxv7jL0HfsRVDqHZ8rC/fi3tgK0UVeXlXq49A8ZQzM8XOqq9nXEe/OjPkuWoo1e/GtRbAbDCxZuXjN8X2TjTL4gHkCvYRjeVV20miiHgiftwRfQFJ3Hk3Mw7zxKV5DxG7VAsazxjzoY8nVSW5I0VIkY+xTZSC/3w6deW/CTnQDWxXjw5csGrzunTHg1dkstm5o7RKpbnna1zmpco6nENML6HgHvKiyNIHjaqYphPrSRz+NUV486a7gXFXdU7463WaV/EGmcwGGPizMoVaZumKlNjqpIO84IRno9ih3se/Fb5DMqiZBksdTasc9JyBEFZOE1X1iZBBycaGO8TSWE8tAGy1DkfK3y9cn1O/qYnRG0uFB7IfuAsdg31FVUgjN+JGTiDI4Fztrvwwivkb5Y1q5K/gQySHfIlz9HgxfrOBuNZ5+FFXbEF2q2/GLgaR4UY4obH0aoDOLaStya0KqYMkESneCQMbQafn739iSZTLGKDQIu630D6NjqdMZsIsvP38/Ck4LCCY4tNMXKPzYUzxz7x190TD+gb/3Yb74cGGbO9/uhOOIF/rK+KyMQkdpNSjLzuEt6UTKUGHHEc/Kfw1MwU5hyir1FInaGFajIjlF0pmlB4iJ9yLHW4OhIt/EdT+7GHqMmLJbwa0Jng6abFdaHwq6rYMSy2tNwuHtMAuW9kb6C1zJ4uDScl8Z2lHRXSWRAzJoyTDkE5AIlOsWcwWC0Ti8tHXAFNHplGwZ+midnML4FVTOsLKc+ySnQG+x76pK688nesRQj8+LgQrVIoM1T0sGPF1PsEXB4RTqgoxRt9GUTr6yHoh3bRhN2M9UhEpzpBZK7IENP3M4SibthgQS3xQR/YL6L1Waa2HCyDhiqwqeAdA46xcDYtIU+CZ3dbEHeFm0Y8qSjx3zxS0CG0T+kl1oMk+MPLGAVGi1hGwmr17YwADRP3+ozHvfjDEEb5QweboqImzRg57d9q7YudQKauRuqv2ADdv11uJ1yWRoeH9tJ1IwR6VKiVK/i/Yaoc3vflY0h7mceQCAJ3n1xIJNH1URzpyiBX/p5xhYlRWCpFtYW8WDpJbl5lMj7HslvSyjF9s8CSJeS7DiuqMjur9HauVr2H9g4c55XQynjEpWgm8E5m+LNnxlufcrr6f32KzcO/ZPTEePjY1Rmw/z/i8q3ZY9wmthOeDYmIUhDPoVFgEWj3Oy+vfqXZqOgwBM3lddH3lysF9rOoEej2is0TdKf2lTm1NnHk9y2Q/DdbN/UR3hLuE0K8pQybnaE6Kk9oizq9Rkkvzo8EWczt9n+Uj5z3uEYdcv1S/oP7++5MpTmvJQtuk7LJ/7DIWQKxVCVhs57d3IP1u/eHLD0BUSkURXr+gk/FVR0zZBwStM3nrXgRREOCIrdPEDZCBzwUmIupuIqmKWCEeu8SmRntsizOFHht2/hLX8Vl1r3ChAIJS2SUaNQGD9NcYytq+0GUrvJ';

	/**
	 * @access protected
	 * @var string $eventValidation
	 */
	protected $csvEventValidation = 'vV/j6abFl/ADPuzgTI4RSnnlE6YouJ4ooWZ1hwFuE0n2W/q3qBL5jKasjaPbI62YOMmW/S/oy8HO5J81rvYhIR/hRYav4nO+IJB5NkA+CnXJt139VSfNcbZYTmaqLvIykUd9pUDQsZ9rv/0umt4xLxL1ntjdwrAeE1/krYlBCAcfs2f8nbqcGQ==';
	
	/**
	 * Epicenter class constructor.
	 *
	 */
	Public Function __construct()
	{

	}
}
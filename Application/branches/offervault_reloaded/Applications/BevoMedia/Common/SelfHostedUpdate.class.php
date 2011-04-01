<?php
require_once(PATH.'AbsoluteIncludeHelper.include.php');

class SelfHostedUpdate {
	private $_tableName = 'bevomedia_selfhostedupdate';
	private $_updateHost = 'http://beta.bevomedia.com/';
	private $_updateFile = 'BevoMedia/API/SelfHostedUpdate.html';
	private $_updateCooldown = 3600;

	public $userId = 0;
	
	public function __construct($userId)
	{
		$this->userId = $userId;
	}
	
	public function updateReady()
	{
		$cooldown = $this->getCooldown();
		if($cooldown === false)
		{
			return true;
		}else{
			if($cooldown > $this->_updateCooldown)
			{
				return true;
			}
		}
		return false;
	}
	
	public function getCooldownRemaining($formatted = true)
	{
		if($this->getCooldown() == false)
		{
			return '0';
		}
		$cd = $this->_updateCooldown - $this->getCooldown();
		if($cd < 0)
		{
			return '0';
		}
		if($formatted)
		{
			$out = '';
			$seconds = $cd%60;
			if($seconds < 10)
				$seconds = '0' . $seconds;
				
			$out = ($cd-($cd%60))/60 . ':' . $seconds;
			return $out;
		}else{
			return $cd;
		}
	}
	
	public function getCooldownDate()
	{
		$sql = 'SELECT lastUpdate AS lastUpdate FROM ' . $this->_tableName . ' WHERE user__id = ' . $this->userId;
		$result = mysql_query($sql);
		if(!$result)
		{
			die(print mysql_error());
		}
		if(!mysql_num_rows($result))
		{
			return false;
		}
		$row = mysql_fetch_assoc($result);
		return $row['lastUpdate'];
	}
	
	
	public function getCooldown()
	{
		$sql = 'SELECT NOW() - lastUpdate AS cooldown FROM ' . $this->_tableName . ' WHERE user__id = ' . $this->userId;
		$result = mysql_query($sql);
		if(!$result)
		{
			die(print mysql_error());
		}
		if(!mysql_num_rows($result))
		{
			return false;
		}
		$row = mysql_fetch_assoc($result);
		return $row['cooldown'];
	}
	
	public function updateCooldown()
	{
		if($this->getCooldown() === false)
		{
			$sql = 'INSERT INTO ' . $this->_tableName . ' (user__id, lastUpdate) VALUES ("' . $this->userId . '", NOW())';
			mysql_query($sql);
		}else{
			$sql = 'UPDATE ' . $this->_tableName . ' SET lastUpdate = NOW() WHERE user__id = ' . $this->userId;
			mysql_query($sql);
		}
	}
	
}


?>
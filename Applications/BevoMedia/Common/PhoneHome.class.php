<?php
require_once(PATH.'AbsoluteIncludeHelper.include.php');
// Because of the information security concerns in the industry,
// and due to recent worries over data privacy in one of our
// main competitors, we've decided to be very explicit about
// EXACTLY what gets transmitted back to Bevo.
// I decided that there's no better way to do this than
// to aggregate the functionality in a single file,
// and name it something obvious, like "PhoneHome".
// If you're inspecting our source looking for nefarious activity,
// I hope this was the first thing you found.
// This class does all of the communication with Bevo servers.
// If you want to know exactly what data we're sending back,
// look no further. I mean, unless you WANT to look further.
//
// This class can be explicitely disabled by a settings value
// in your mysql database. The option is presented at install,
// but if you'd like to later disable it, put the following in
// your config.ini under the top section, "Application" --
// NoPhoneHome = true
class PhoneHome {
    private $_tableName = 'bevomedia_selfhostedupdate';
	private $_acctsTableName = 'bevomedia_dotcom_accounts';
	private $_host = 'http://beta.bevomedia.com/';
	private $_updateFile = 'BevoMedia/API/SelfHostedUpdate.html';
	private $_sqlTables = 'BevoMedia/API/SqlTables.html';
	private $_ppcQueueSubmit = 'BevoMedia/API/PPCQueueSubmit.html';
	private $_authUrl = 'BevoMedia/API/SelfHostedAuthentication.html';
	private $_updateCooldown = 3600;
	
	public $updates = array();
	public $disabled = false;
	public $bevo_user = false;
	public $user = false;
	public $thisVersion = false;
	public $latestFinal = false;
	public $latestVersion = false;
	public $latestBeta = false;
	public $bevoLastNetworkUpdate = false;
	public $bevoLastPPCUpdate = false;
	public $apiKey = false;
	
	public function __construct()
	{
	    //Development setting
	    if($_SERVER['HTTP_HOST'] == 'bsh')
	        $this->_host = 'http://bevolocal/';
	    /* If selfhost_nophonehome is true, QUIT! Bevo selfhost will never phone home */
        try {
            if(Zend_Registry::get('Application/NoPhoneHome') == '1')
            {
                $this->disabled = true;
                return;
            }
        } catch (Exception $e){
            //Disabled entry not found, not disabled!
            $this->disabled = false;
        }
	}
	
	public function bevo_auth($user)
	{
	    if($this->disabled)
	        return false;
	    $q = "select * from {$this->_acctsTableName} where user__id = {$user->id} and enabled=1";
	    $bevo_user = mysql_query($q);
	    if($bevo_user && mysql_num_rows($bevo_user) > 0)
	    {
	        $tmp_bevo_user = mysql_fetch_assoc($bevo_user);
	    	try {
				$url = "{$this->_host}{$this->_authUrl}?username=" . @$tmp_bevo_user['username'] . '&password=' . @$tmp_bevo_user['password'];
				$ch = curl_init();
				curl_setopt($ch, CURLOPT_URL, $url);
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
				curl_setopt($ch, CURLOPT_TIMEOUT, 30);
				curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 6.1; en-US; rv:1.9.1.2) Gecko/20090729 Firefox/3.5.2 GTB5');
				$html = curl_exec($ch);
				curl_close($ch);
				$vars = @json_decode($html);
				if(!$vars)
				    return false;
				$this->latestFinal = $this->latestVersion = intval($vars->latestFinal);
				if (isset($vars->verified) && ($vars->verified==false)) {
					echo '<script type="text/javascript">alert(\'You must verify your account. In order to do that log in to the main website and follow the instructions.\'); window.location = \'http://beta.bevomedia.com/\';</script>"';
				    return false;
				}
				if(isset($vars->error))
				{
					echo '<script type="text/javascript">alert("'.$vars->error.'");</script>"';
				    return false;
				}
				else if (!isset($vars->apiKey))
				    return false;
				$this->user = $user;
				$this->bevo_user = $bevo_user;
				$this->apiKey = $vars->apiKey;
				$this->user->setApiCalls(intval($vars->apiCalls));
				$this->user->UpdateMembershipType($vars->membershipType);
				$this->bevoLastNetworkUpdate = $vars->lastNetworkUpdate;
				$this->bevoLastPPCUpdate = $vars->lastPPCUpdate;
				$this->latestBeta = intval($vars->latestBeta);
				$this->totalApiCalls = intval(@$vars->totalApiCalls);
				$this->ppcSignedUp = intval(@$var->ppcSignedUp);
	    	} catch (Exception $e) {
	    	    return false;
	    	}
	    	
	    	return $this->bevo_user;
	    }
        return false;
	}
	
	public function thisVersion() {
	    if($this->thisVersion)
	        return $this->thisVersion;
	    $this->thisVersion = 0;
	    $r = mysql_query("select value from bevomedia_settings where name='selfhost_version'");
	    if($r && mysql_num_rows($r) && $row = mysql_fetch_assoc($r))
	        $this->thisVersion = $row['value'];
	    return $this->thisVersion;
	}
	
	public function newVersion() {
	    $thisV = $this->thisVersion();
	    if($thisV < $this->latestFinal)
	        return true;
	    return false;
	}
	
	public function submitPPCJob($json)
	{
	    $data = "dd=true&jsonObj=".urlencode($json);
	    $url = $this->_host . $this->_ppcQueueSubmit . '?apiKey=' . $this->apiKey;
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_TIMEOUT, 30);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
		curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 6.1; en-US; rv:1.9.1.2) Gecko/20090729 Firefox/3.5.2 GTB5');
		$out = curl_exec($ch);
		return $out;
	}
	
	public function upgradeTables()
	{
	    $url = $this->_host . $this->_sqlTables . '?apiKey=' . $this->apiKey . '&since=' . $this->thisVersion();

	    $this->updateCooldown();
	    $this->user->setLastNetworkUpdate($this->bevoLastNetworkUpdate);
		$this->user->setLastPPCUpdate($this->bevoLastPPCUpdate);
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_TIMEOUT, 30);
		curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 6.1; en-US; rv:1.9.1.2) Gecko/20090729 Firefox/3.5.2 GTB5');
		$f = curl_exec($ch);
		$arr_sql =  preg_split('/;[\n\r]+/',$f);
		reset($arr_sql);
		
		while (list($k,$v)=each($arr_sql))
		{
			if (trim($v)!="")
			{
				if (mysql_query($v) === false )
				{
					echo "<span style='color: red'>Error executing the following query: </span><br /> {$v} <br />";
					echo "Error: ".mysql_error()."<br /><br />";
					if(!preg_match('@^INSERT@', trim($v)))
					  die;
					else
					  echo "This appears to be non-fatal; continuing the upgrade process<br /><br />";

				}
			}
		}
	}
	// Updates remote values
	public function doUpdate()
	{
	    $url = $this->_host . $this->_updateFile . '?apiKey=' . $this->apiKey . '&startDate=' . date('Y-m-d', strtotime($this->getCooldownDate()) - 24*3600);

	    $this->updateCooldown();
	    $this->user->setLastNetworkUpdate($this->bevoLastNetworkUpdate);
		$this->user->setLastPPCUpdate($this->bevoLastPPCUpdate);
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_TIMEOUT, 30);
		curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 6.1; en-US; rv:1.9.1.2) Gecko/20090729 Firefox/3.5.2 GTB5');
		$f = curl_exec($ch);
		$this->updates = Zend_Json::decode($f);
		
    	$kw_table = 'bevomedia_keyword_tracker_keywords';
        $live_local_map = array();
        $json = $this->updates;
        if(empty($json))
        {
            echo 'Invalid JSON';
            return false;
        }
        if (isset($json->error))
        {
        	echo '<script type="text/javascript">';
        	echo 'alert("'.$json['error'].'");';
        	echo '</script>';
        	return false;
        }
        /* update keywords first */
        unset($json[$kw_table]['_unique']);
        foreach(@$json[$kw_table] as $item => $row)
        {
			if(substr($item, 0, 1) == '_')
			  continue;
            $kw = mysql_real_escape_string(@$row['update']['keyword']);
            $find = mysql_query("select id from $kw_table where keyword='$kw'");
            if(mysql_num_rows($find) == 0)
            {
                
                mysql_query("insert into $kw_table (keyword) values ('$kw')");
                $kwid = mysql_insert_id();
            } else {
                $found = mysql_fetch_assoc($find);
                $kwid = $found['id'];
            }
            $live_local_map['keywordId'][$row['_live_id']] = $kwid;
            $live_local_map['rawKeywordId'][$row['_live_id']] = $kwid;
            $live_local_map['bidKeywordId'][$row['_live_id']] = $kwid;
        }
        unset($json[$kw_table]);
        
        /* update everything else */
        foreach($json as $tn=>$t)
        {
			echo "\nUpdating $tn\n";
            $u = false;
            $s = false;
            if(isset($t['_unique']))
            {
                $u = $t['_unique'];
                unset($t['_unique']);
            }
            if(isset($t['_store']))
            {
                $s = $t['_store'];
                unset($t['_store']);
			}
			
            // Loop through update items
            foreach($t as $item => $row)
            {
				if(substr($item, 0, 1) == '_')
				  continue;
                $update = isset($row['update']) ? $row['update'] : false;
                if(!is_array($row) || empty($row) || empty($update))
                    continue;
                $updateId = false;
                // Replace user_id with local user_id
                if(isset($update['user__id']))
                    $update['user__id'] = $this->user->id;
                // Replace Store variables
                foreach($live_local_map as $key => $arr)
                {
                    if(isset($update[$key]))
                    {
                        if($tn == 'bevomedia_ppc_keywords_stats' && $key == 'keywordId')
                            continue;
                        $update[$key] = @$arr[$update[$key]];
                    }
                }
                if(isset($live_local_map['ppckeywordId']) && $tn == 'bevomedia_ppc_keywords_stats')
                    $update['keywordId'] = @$live_local_map['ppckeywordId'][$update['keywordId']];

                // If there are uniquely identifiable columns, first look for a match
                if($u)
                {
                    $unique_cols = array();
                    // Build the query
                    foreach($u as $c)
                    {
                        $unique_cols[$c] = $c . '="'.mysql_real_escape_string(@$update[$c]).'"';
                    }
                    // Find the columns
                    $q = "SELECT id FROM $tn WHERE ".implode($unique_cols, ' AND ');
                    $find = mysql_query($q);
                    if(mysql_num_rows($find))
                    {
                        $found = mysql_fetch_assoc($find);
                        $updateId = $found['id'];
                        $update_cols = array();
                        // Build the update
                        foreach($update as $col=>$val)
                            if(!isset($unique_cols[$col]))
                                $update_cols[] = $col . '="' . mysql_real_escape_string($val).'"';
                        $row_query = "UPDATE $tn SET ". implode($update_cols, ', ') . ' WHERE ' . implode($unique_cols, ' AND ');
                    }
                }
                // If we haven't updated, insert.
                if(!$updateId)
                {
                    $insert_cols = array();
                    foreach($update as $col=>$val)
                        $insert_cols[] = $col . '="' . mysql_real_escape_string($val).'"';
                    $row_query = "INSERT INTO $tn SET ".implode($insert_cols, ', ');
                }
                mysql_query($row_query);
                echo $row_query . "\n";
                if($s && isset($row['_live_id']))
                {
                    if(!$updateId)
                        $live_local_map[$s][$row['_live_id']] = mysql_insert_id();
                    else
                        $live_local_map[$s][$row['_live_id']] = $updateId;
                }
            }
        }
        return true;
	}
	
    public function getCooldownRemaining()
	{
		if($this->getCooldown() == false)
		{
			return 0;
		}
		$cd = $this->_updateCooldown - $this->getCooldown();
		if($cd < 0)
		{
			return 0;
		}
		return $cd;
	}
	
	public function getCooldownDate()
	{
		$sql = 'SELECT lastUpdate AS lastUpdate FROM ' . $this->_tableName . ' WHERE user__id = ' . $this->user->id;
		$result = mysql_query($sql);
		if(!$result)
		{
			die('Error getting cooldown date: ' . mysql_error());
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
		if(!$this->user || !$this->user->id)
		  return false;
		$sql = 'SELECT NOW() - lastUpdate AS cooldown FROM ' . $this->_tableName . ' WHERE user__id = ' . $this->user->id;
		$result = mysql_query($sql);
		if(!$result)
		{
			die('Error getting cooldown: ' . mysql_error());
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
			$sql = 'INSERT INTO ' . $this->_tableName . ' (user__id, lastUpdate) VALUES ("' . $this->user->id . '", NOW())';
			mysql_query($sql);
		}else{
			$sql = 'UPDATE ' . $this->_tableName . ' SET lastUpdate = NOW() WHERE user__id = ' . $this->user->id;
			mysql_query($sql);
		}
	}
	
	public function doUpgrade()
	{
        $ftps = mysql_query("select distinct name, value from bevomedia_settings where name like 'selfhost_ftp%'");
        $rows = array();
        while($row = mysql_fetch_assoc($ftps))
        {
            $rows[$row['name']] = $row['value'];
        }
        $ftp_settings = array(
            'fhost' => @$rows['selfhost_ftphost'],
        	'fport' => @$rows['selfhost_ftpport'],
        	'fuser' => @$rows['selfhost_ftpuser'],
        	'fpass' => @$rows['selfhost_ftppass'],
        	'fdir' => @$rows['selfhost_ftpdir'],
        );
        $use_ftp = true;
        foreach(array('fhost', 'fport', 'fdir', 'fuser', 'fpass') as $el)
            if(!isset($ftp_settings[$el]))
                $use_ftp = false;
        if (!$this->unpackBevoInstall(ABSPATH, !empty($rows), $ftp_settings))
        {
        	return false;	
        }
		$this->upgradeTables();
        return true;
	}
	
	public function updateReady()
	{
		if(stristr($_SERVER['REQUEST_URI'], 'BevoUpgrade'))
		  return false;
	    if(!$this->user)
	        return false;
        if($this->getCooldownRemaining() === 0)
		    return true;
	    if(strtotime($this->bevoLastNetworkUpdate) > strtotime($this->user->lastNetworkUpdate))
	        return true;
	    if(strtotime($this->bevoLastPPCUpdate) > strtotime($this->user->lastPPCUpdate))
	        return true;
		return false;
	}
	

    	
    private function unpackBevoInstall($Path, $useftp = false, $ftp_settings = array())
    {
    	$PackUrl = ($_SERVER['HTTP_HOST'] == 'selfhosted.bevomedia1' ? 'http://bevomedia/' : 'http://beta.bevomedia.com/' ) . 'getselfhost/getLatest.php?apiKey='.$this->apiKey;

    	$PackData = file_get_contents($PackUrl);

    	if (strlen($PackData)<1024)
    	{
   			$json = Zend_Json::decode($PackData);
    		if (isset($json->error))
	        {
	        	echo '<script type="text/javascript">';
	        	echo 'alert("'.$json['error'].'");';
	        	echo '</script>';
	        	return false;
	        }
    		return false;	
    	}
    	
    	$ftp = false;
    	if($useftp === true)
    	{
    	    $ftp = @ftp_connect($ftp_settings['fhost'], $ftp_settings['fport'], 10);
    	    @ftp_login($ftp, $ftp_settings['fuser'], $ftp_settings['fpass']);
    	    if(@ftp_chdir($ftp, $ftp_settings['fdir']) != true)
    	        die('FTP: Unable to chdir to ' . $ftp_settings['fdir']);
    	    else
    	        echo 'FTP: chdir to ' . $ftp_settings['fdir'] . ', current dir is ' . @ftp_pwd($ftp) . "<br />";
    	    echo 'FTP: Locking install during upgrade process';
    	    $lock = tmpfile();
    	    fwrite($lock, '123');
    	    rewind($lock);
    	    @ftp_fput($ftp, 'upgrade.lock', $lock);
    	    $f = tmpfile();
    	    fwrite($f, $PackData);
    	    rewind($f);
    	    @ftp_fput($ftp, 'SelfHosted.zip', $f, FTP_BINARY);
    	    $zipFile = $Path.'SelfHosted.zip';
    	} else {
    	    echo 'Locking install during upgrade process';
    	    file_put_contents($Path.'upgrade.lock', '123');
    	    file_put_contents($Path.'SelfHosted.zip', $PackData);
    	    $zipFile = $Path.'SelfHosted.zip';
    	}
    	if(!file_exists($zipFile))
    	    die('<span style="color: red">Error opening ' . $zipFile . '</span>');
    	else
    	    echo 'SelfHost.zip archive downloaded successfully!<br />';
    	echo 'Starting to unzip<br />';
    	$zip = zip_open($zipFile);
    	$files = 0;
    	if ($zip)
    	{
    	    echo 'Zip opened successfully!<br />';
    	    $extensions = array();
            while ($zip_entry = zip_read($zip))
            {
                $files++;
                $filename = zip_entry_name($zip_entry);
                $fileinfo = pathinfo($filename);
                $dirname = $fileinfo['dirname'].'/';
                if(stristr($filename, 'config.ini'))
                    continue;
                if(isset($fileinfo['extension']))
                {
                    $extensions[$fileinfo['extension']] = true;
                }
                if($ftp)
                {
                    if(substr($filename, -1) == '/')
                    {
                        if(strstr($filename,'Externals') === false)
                            echo 'Making directory ' . $filename . "<br />";
                        @ftp_rmdir($ftp, $filename);
                        @ftp_mkdir($ftp, $filename);
                    } else {
                        @ftp_rmdir($ftp, $dirname);
                        @ftp_mkdir($ftp, $dirname);
                        if(strstr($filename,'Externals') === false)
                            echo 'Unpacking ' . $filename . "<br />";
                	    if(zip_entry_open($zip, $zip_entry, "r"))
                	    {
                	        $buf = zip_entry_read($zip_entry, zip_entry_filesize($zip_entry));
                	        $tmp = tmpfile();
                	        fwrite($tmp, "$buf");
                	        rewind($tmp);
                	        zip_entry_close($zip_entry);
                	        $binary = isset($fileinfo['extension']) && in_array($fileinfo['extension'], array('swf', 'gif', 'png', 'jpg', 'ico', '0b', 'jar'));
                	        @ftp_fput($ftp, $filename, $tmp, $binary ? FTP_BINARY : FTP_ASCII);
                	    } else {
                	        die('<span style="color: red">Error reading zip archive</span>');
                	    }
                    }
                }
                else
                {
            		if(substr($filename, -1) == '/')
            		{
            		    
            		    if(strstr($filename,'Externals') === false)
                            echo 'Making directory ' . $filename . "<br />";
            		    @rmdir($filename);
            		    @mkdir($filename, 0777, true);
            		} else {
            		    if(strstr($filename,'Externals') === false)
                            echo 'Unpacking ' . $filename . "<br />";
                	    $fp = @fopen($filename, "w");
            		
                		if(!$fp)
                		    die('<span style="color: red">Error opening '. $filename . '</span><br />');
                		if (zip_entry_open($zip, $zip_entry, "r"))
                		{
                		  $buf = zip_entry_read($zip_entry, zip_entry_filesize($zip_entry));
                		  fwrite($fp,"$buf");
                		  zip_entry_close($zip_entry);
                		  @fclose($fp);
                		  @chmod($filename, 0755);
                        }
    
            		}
               }
          }
          echo 'Unpacked ' . $files . " files<br />";
    	  zip_close($zip);
    	}
    	echo "Done unpacking!<br />";
    	if($ftp)
    	{
    	    echo "FTP: Deleting archive...<br />";
    	    @ftp_chdir($ftp, $ftp_settings['fdir']);
    	    @ftp_delete($ftp, 'SelfHosted.zip');
    	    echo "FTP: Deleting lock file...<br />";
    	    @ftp_delete($ftp, 'upgrade.lock');
    	    return $ftp;
    	} else {
    	    echo "Deleting archive...<br />";
    	    @unlink($Path.'Archive.zip');
    	    echo "Deleting lock file...<br />";
    	    @unlink($Path.'upgrade.lock');
    	    return true;
    	}
    }
}


?>
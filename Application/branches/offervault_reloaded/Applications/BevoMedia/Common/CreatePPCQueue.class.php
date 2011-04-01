<?php 

Class CreatePPCQueue {
	
	Private $json;
	
	Private $AccountString = "\$Account";
	Private $TempJSONString = "\$TempJSON = new Services_JSON();\n";
	
	Private $DescriptionString = "//CreatePPCbevomedia_queue.Description";
	Private $DescriptionArray = array();
	
	Public $UserID;
	
	Private $_db;
	
	Public $envelopes = array();
	Public $JobIDs = array();
	Public Function __construct()
	{
		$this->json = new Services_JSON();
	}
	
	Public Function AddToenvelope($JobID, $Item, $provider, $description = '')
	{
		if(!isset($this->JobIDs[$JobID]))
		{
			$this->JobIDs[$JobID] = array();
		}
		if(!isset($this->envelopes[$JobID]))
		{
			$this->envelopes[$JobID] = $this->EchoIdentificationComment($this->UserID);
		}
		if(!$this->_db)
		  $this->InitDB();
		$description = mysql_real_escape_string($description);
		$this->_db->exec("INSERT INTO bevomedia_queue_log (queueId, provider, description) VALUES ((SELECT id FROM bevomedia_queue WHERE jobId = '{$JobID}'), '{$provider}', '{$description}')");
		$logId = $this->_db->lastInsertId();
		$Start = "\n";
		$Start .= "\n";
		$Start .= "\$Status = 'success';\n";
		$Start .= "\$EnvOutput = '';\n";
		$Start .= "\$logId = $logId;\n";
		$Start .= "mysql_query('UPDATE bevomedia_queue_log SET started=NOW(), status = \"in-progress\" WHERE id = {$logId}');\n";
		$Start .= "try { ";

		$End = "\n";
		$End .= "} catch (Exception \$e) { \$Status = 'error'; \$EnvOutput .= \"\\n";
		$End .= "Error: {\$e->getMessage()}\\n";
		$End .= "\"; }\n";
		$End .= "\$EnvOutput = mysql_real_escape_string(\$EnvOutput);\n";
		$End .= "mysql_query(\"UPDATE bevomedia_queue_log SET completed=NOW(), output='\$EnvOutput', status='\$Status' WHERE id = \$logId\");\n";
		$End .= "";

        $this->envelopes[$JobID] .= $Start.$Item.$End;
	}
	
	Public Function EchoIdentificationComment($UserID)
	{
		$output = "//CreatePPCbevomedia_queue.UserID." . $UserID . "\n";
		return $output;
	}
	
	Public Function EchoAccountConstruct($Account)
	{
		array_push($this->DescriptionArray, 'PPCProvider=>'.get_class($Account));
		
		$output = $this->AccountString . " = new " . get_class($Account) . "();" . "\n";
		$output .= $this->AccountString . "->GetInfo(" . $Account->id . ");" . "\n";
		$output .= "\$Verified = false;\n";
		$output .= "try { \$Verified = " . $this->AccountString . "->VerifyAccountAPI(); } catch (Exception \$e) { \$Verified = false; }\n";
		$output .= "if( !\$Verified)\n";
		$output .= "{\n";
		$output .= "\t\$Status = 'error';\n";
		$output .= "\tthrow new Exception('Unable to validate account with API; your username or password is incorrect.');\n";
		$output .= "}\n";
		return $output;
	}
	
	Public Function EchoAccountFunction()
	{
		$Args = func_get_args();
		array_push($this->DescriptionArray, $Args[0].'=>'.$Args[1]);
		foreach($Args as $K=>$V)
		{
			if(is_string($V))
				$Args[$K] = str_replace("'", "\'", $V);
		}
		$jsonArgs = $this->json->encode($Args);
		$output = "\$jsonArgs = '" . str_replace('\\\\\'', '\\\'', $jsonArgs) . "';" . "\n";
		$output .= $this->TempJSONString;
		$output .= "\$Args = \$TempJSON->decode(\$jsonArgs);" . "\n";
		$output .= "\$Func = \$Args[0];" . "\n";
		$output .= "array_shift(\$Args);" . "\n";
		$output .= "foreach(\$Args as \$Key=>\$Val)\n{\n\t";
		$output .= "if(!is_string(\$Val)) continue;\n\t";
		$output .= "if(preg_match('/^\\\$[\\w]+\$/', @(string)\$Val))\n\t\t";
		$output .= "eval('\$Args[\$Key] = ' . \$Val . ';');" . "\n}";
		$output .= "try {\n\t\$Tempoutput = call_user_func_array(array(" . $this->AccountString . ", \$Func), \$Args);\n"; 
		$output .= "\t\$EnvOutput .= (is_numeric(\$Tempoutput)?'{'.\$Func.'} Success!'.\"\n\":('{'.\$Func.'} Error:' . print_r(\$Tempoutput, true) .\"\n\"));" . "\n";
		$output .= "\tif(!is_numeric(\$Tempoutput))\n\t\$Status = 'error';\n} catch (Exception \$e) { \$Status = 'error'; throw \$e; }\n";;
		return $output;
	}
	Public Function EchoCopyVar($src, $dest)
	{
	    return "\$$dest = \$$src;\n";
	}	  
	Public Function EchoDescriptionString()
	{
		$output = $this->DescriptionString;
		for($i=0; $i<sizeOf($this->DescriptionArray); $i++)
		{
			$output .= '.' . $this->DescriptionArray[$i];
		}
		$this->DescriptionArray = array();
		return $output;
	}
	
	Public Function InitDB()
	{
		if(class_exists('Zend_Registry'))
		{
			$this->_db = Zend_Registry::get('Instance/DatabaseObj');
		}else{
			$IncludePaths = array(
			    realpath(ABSPATH .'Externals'),
			    '.',
			);
			set_include_path(implode(PATH_SEPARATOR, $IncludePaths));
			
			require_once ABSPATH . 'Externals/Zend/Db.php';
			$config = array(
			    'host'     => ABSDBHOST,
			    'username' => ABSDBUSER,
			    'password' => ABSDBPASS,
			    'dbname'   => ABSDBNAME,
			);
			
			$this->_db = Zend_Db::factory('PDO_MYSQL', $config);	
		}
	}
	
	Public Function GetAllQueueItemsForUser($UserID, $OptDate = '0000-00-00 00:00:00')
	{
		if(!$this->_db)
			$this->InitDB();
			
		$OptDate = date('Y-m-d', strtotime($OptDate));
		
		$Sql = "SELECT id, jobId, created, completed, envelope, output FROM bevomedia_queue WHERE user__id = $UserID AND created >= '{$OptDate}' AND hidden=0 AND type='PPC Editor' ORDER BY created desc";
		$output = $this->_db->fetchAll($Sql);
		
		return $output;
	}
	Public Function HideQueueItemForUser($UserID, $QueueID)
	{
	  if(!$this->_db)
		  $this->InitDB();
	  $Sql = "UPDATE bevomedia_queue SET hidden=1 WHERE id=$QueueID AND user__id=$UserID";
	  $this->_db->exec($Sql);
	}
	
	Public Function SubProcesses($ID)
	{
	  if(!$this->_db)
		$this->InitDB();
	  return $this->_db->fetchAll("SELECT * FROM bevomedia_queue_log WHERE queueId = $ID");
	}
	Public Function Parseoutput($output, $DescArr = false, $Format = 'String')
	{
		if($output == '')
		{
			return 'bevomedia_queued...';
		}
		$outputLines = explode("\n", $output);
		$outputArr = array();
		$output = '';
		
		$C = 0;
		foreach($outputLines as $Key=>$Line)
		{
			if(strpos($Line, 'API') !== false && strpos($Line, 'Add') !== false)
			{
				$Line = str_replace(array('API', '' , '{'), '', $Line);
				$Line = explode('}', $Line);
				$Temp = '<b style="line-height:16px;">' . $Line[0] . '</b><br/>';
				$Temp .= $Line[1] . '';
				$output .= $Temp;
				$outputArr[] = $Temp;
			
				$Temp = explode("\n", $DescArr[$C]);
				for($i=0; $i<sizeOf($Temp)-2; $i++)
				{
					$output .= '<br/>';
				}
				$C++;
			}
		}
		
		if($Format == 'Array')
		{
			return $outputArr;
		}
		
		return $output;
		
		while(strpos($output, 'Success.Success.') !== false)
		{
			$output = str_replace('Success.Success.', 'Success.', $output);
		}
		return str_replace('Success.Error:', 'Error:', $output);
	}
	
	Public Function ParseDescription($envelope, $Format = 'String')
	{
		$Env = explode("\n", $envelope);
		
		$output = array();
		$Desc = '';
		
		foreach($Env as $En)
		{
			if(!(strpos($En, '//CreatePPCbevomedia_queue.Description.') === false))
			{
				$Desc .= $this->ParseDescriptionItem($En) . '<br/>';
				$output[] = $this->ParseDescriptionItem($En);
			}
		}
		
		if($Format == 'Array')
			return $output;
			
		return $Desc;
	}
	
	Public Function ParseDescriptionItem($Desc)
	{
		$Desc = str_replace('//CreatePPCbevomedia_queue.Description.', '', $Desc);
		$Des = explode(".", $Desc);
		$output = '';
		$Favicons = array('Adwords'=>'/Themes/BevoMedia/img/googlefavicon.png', 'Yahoo'=>'/Themes/BevoMedia/img/yahoofavicon.png', 'MSNAdCenter'=>'/Themes/BevoMedia/img/msnfavicon.png');
		$Suffix = '';
		foreach($Des as $De)
		{
			$D = explode("=>", $De);
			if($D[0] == 'PPCProvider')
			{
				$output .= '<img align="left" src="'. $Favicons[str_replace('Accounts_', '', $D[1])] .'"/> &nbsp;';
				continue;
				$output .= str_replace('Accounts_', '', $D[1]);
			}else if(strpos($D[0], 'Get') !== false)
			{
				$Suffix .= 'Using ';
				$Suffix .= str_replace(array('ID', 'API', 'Get'), '', $D[0]);
				$Suffix .= ': ';
				$Suffix .= '<i> ' . $D[1] . ' </i>';
				$Suffix .= '<br/>' . "\n";
			}else if(strpos($D[0], 'API') !== false){
				$output .= '<span style="line-height: 16px;"><b>';
				$output .= str_replace('API', '', $D[0]);
				$output .= '</b>';
				$output .= ' (' . str_replace('//CreatePPCbevomedia_queue', '', $D[1]) . ')';
				$output .= '</span><br/>' . "\n";
			}	
		}
		$output .= $Suffix;
		
		$Temp = explode("\n", $output);
		$Last = $T = '';
		foreach($Temp as $K=>$V)
		{
			if($V != $Last)
				$T .= $V . "\n";
			$Last = $V;
		}
		return $T;
		return $output;
	}
	
	Public Function GetTotalQueueItemsForUser($UserID, $completed = false)
	{
		if(!$this->_db)
			$this->InitDB();
			
		$output = new stdClass();
		
		$Latest = $this->GetLatestQueueItem($UserID, $completed);
		if($Latest === false)
		{
			$Latest = new stdClass();
			$Latest->created = date('Y-m-d');
		}
		
		$Latest->created = date('Y-m-d', strtotime($Latest->created));
		$Total = $this->GetAllQueueItemsForUser($UserID, $Latest->created);
		$StringTotal = 0;
		foreach($Total as $Row)
		{  $this->InitDB();

			$StringTotal += substr_count($Row->envelope, "//CreatePPCbevomedia_queue.Description");
		}
		
		$completedSql = "SELECT id, created, envelope FROM bevomedia_queue WHERE user__id = $UserID AND completed != '0000-00-00 00:00:00' AND created >= '{$Latest->created}' AND hidden = 0";
		$completed = $this->_db->fetchAll($completedSql);
		$Stringcompleted = 0;
		foreach($completed as $Row)
		{
			$Stringcompleted += substr_count($Row->envelope, "//CreatePPCbevomedia_queue.Description");
		}
		
		//$output->Total = sizeOf($Total);
		$output->Total = $StringTotal;
		
		//$output->Complete = sizeOf($completed);
		$output->Complete = $Stringcompleted;
		
		if($output->Total > 0)
			$output->Percent = $output->Complete / $output->Total;
		else
			$output->Percent = 1;
		
		return $output;
	}
	
	Public Function GetLatestQueueItem($UserID, $completed = false)
	{
		if(!$this->_db)
			$this->InitDB();
			
		if($completed === false)
		{
			$CSql = "";
		}else{
			$CSql = " AND completed = '0000-00-00 00:00:00' ";
		}
			
		$Sql = "SELECT id, created FROM bevomedia_queue WHERE user__id = $UserID $CSql ORDER BY created ASC";
		$Out = $this->_db->fetchRow($Sql);
		if(!$Out)
			return false;

		return $Out;	
	}
}


?>
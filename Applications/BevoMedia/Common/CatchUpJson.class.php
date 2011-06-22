<?php
	require_once(realpath(dirname(__FILE__) ) . DIRECTORY_SEPARATOR . 'AbsoluteIncludeHelper.include.php');

	class CatchUpJson
	{
		private $userId = 0;
		
		private $kwIdMap = array();
		

		private $tableList = array(
		    array('tableName'=>'bevomedia_aff_network', 'unique' => array('id')),
		    array('tableName'=>'bevomedia_user_aff_network', 'where' => 'user__id = ##user__id##',
			      'unique' => array('user__id', 'network__id'), 'exclude' => array('password')),
			array('tableName'=>'bevomedia_user_aff_network_subid', 'dateCol'=>'statDate', 'where' => 'user__id = ##user__id##',
			      'unique' => array('statDate', 'user__id', 'subId', 'network__id', 'offer__id')),
			array('tableName'=>'bevomedia_user_aff_network_stats', 'dateCol'=>'statDate', 'where' => 'user__id = ##user__id##',
			      'unique' => array('statDate', 'user__id', 'network__id')),
			array('tableName'=>'bevomedia_accounts_analytics', 'where' => 'user__id = ##user__id##',
			       'unique' => array('id', 'user__id', 'username'), 'exclude' => array('password')),
			array('tableName'=>'bevomedia_accounts_adwords', 'where' => 'user__id = ##user__id##',
			       'unique' => array('id', 'user__id', 'username'), 'store' => 'accountId', 'exclude' => array('password')),
			array('tableName'=>'bevomedia_accounts_yahoo', 'where' => 'user__id = ##user__id##',
			       'unique' => array('id', 'user__id', 'username'), 'store' => 'accountId', 'exclude' => array('password')),
			array('tableName'=>'bevomedia_accounts_msnadcenter', 'where' => 'user__id = ##user__id##',
			       'unique' => array('id', 'user__id', 'username'), 'store' => 'accountId', 'exclude' => array('password')),
			array('tableName'=>'bevomedia_ppc_campaigns', 'where' => 'user__id=##user__id## and accountId in (   SELECT `bevomedia_accounts_adwords`.`id` AS `accountId` FROM `bevomedia_accounts_adwords` WHERE (bevomedia_accounts_adwords.user__id = ##user__id##) UNION SELECT  `bevomedia_accounts_yahoo`.`id` AS `accountId` FROM `bevomedia_accounts_yahoo` WHERE (bevomedia_accounts_yahoo.user__id = ##user__id##) UNION SELECT `bevomedia_accounts_msnadcenter`.`id` AS `accountId` FROM `bevomedia_accounts_msnadcenter` WHERE (bevomedia_accounts_msnadcenter.user__id = ##user__id##)     )',
			       'unique' => array('user__id', 'accountId', 'providerType', 'name'), 'store' => 'campaignId'),
			array('tableName'=>'bevomedia_ppc_adgroups', 'where' => 'campaignId in (##campaignId##)',
			       'unique' => array('campaignId', 'name'), 'store' => 'adGroupId'),
			array('tableName'=>'bevomedia_ppc_keywords', 'where' => 'adGroupId in (##adGroupId##)',
			       'unique' => array('adGroupId', 'keywordId'), 'store' => 'ppckeywordId'),
			array('tableName'=>'bevomedia_ppc_keywords_stats', 'where' => 'keywordId in (##ppckeywordId##)',
			       'unique' => array('keywordId', 'statDate'), 'dateCol'=>'statDate',),
			array('tableName'=>'bevomedia_ppc_advariations', 'where' => 'adGroupId in (##adGroupId##)',
			       'unique' => array('adGroupId', 'apiAdId'), 'store' => 'advariationsId'),
			array('tableName'=>'bevomedia_ppc_advariations_stats', 'where' => 'advariationsId in (##advariationsId##)',
			       'unique' => array('advariationsId', 'statDate'), 'dateCol'=>'statDate',),
			array('tableName'=>'bevomedia_keyword_tracker_keywords', 'where' => 'id in (select distinct keywordid from bevomedia_ppc_keywords where id in (##ppckeywordId##))',
			       'unique' => array('keywordId')),
			array('tableName'=>'bevomedia_analytics_domains', 'where' => 'user__id = ##user__id##',
			       'unique' => array('id'), 'store' => 'domainId'),
			array('tableName'=>'bevomedia_analytics_reports', 'where' => 'domainId in (##domainId##)',
			       'unique' => array('domainId', 'dateFrom'), 'store' => 'reportId', 'dateCol' => 'dateFrom'),
			array('tableName'=>'bevomedia_analytics_reports_contentoverview', 'where' => 'reportId in (##reportId##)',
			       'unique' => array('reportId')),
			array('tableName'=>'bevomedia_analytics_reports_countries', 'where' => 'reportId in (##reportId##)',
			       'unique' => array('reportId')),
			array('tableName'=>'bevomedia_analytics_reports_siteusage', 'where' => 'reportId in (##reportId##)',
			       'unique' => array('reportId')),
			array('tableName'=>'bevomedia_analytics_reports_trafficsources', 'where' => 'reportId in (##reportId##)',
			       'unique' => array('reportId')),
			array('tableName'=>'bevomedia_analytics_reports_visitors_overview', 'where' => 'reportId in (##reportId##)',
			       'unique' => array('reportId')),
			array('tableName'=>'bevomedia_offers', 'unique' => array('id')),
			
		);
		public function __construct($userId = NULL)
		{
			if($userId !== NULL)
			{
				$this->userId = $userId;
			}
		}
		
		public function structure()
		{
		  $output = array();
		}
		public function data($date)
		{
			$output = array();
			$queries = array();
			
			$sql = 'SELECT apiCalls FROM bevomedia_user WHERE id = ' . $this->userId;
			$queries[] = $sql;
			$query = mysql_query($sql);
			$row = mysql_fetch_assoc($query);
			
			$store = array('user__id' => $this->userId);
			foreach($this->tableList as $table)
			{
                $wheres = array();
                $where = '';
                $tn = strtolower($table['tableName']);
			    if(isset($table['dateCol']))
			        $wheres[] =  $table['dateCol'] . ' >= "' . $date .'"';
			    if(isset($table['where']))
			    {
			        $where = $table['where'];
			        foreach($store as $r => $v)
			        {
			            $where = str_replace('##'.$r.'##', is_array($v) ? implode($v, ', ') : $v, $where);
			        }
			        $wheres[] = $where;
			    }
			    $output[$tn] = array();
				$output[$tn]['_unique'] = @$table['unique'];
				$sql = 'SELECT * FROM ' . $table['tableName'];
				if(!empty($wheres))
					$sql .= ' WHERE '. implode($wheres, ' AND ');
			    if(!empty($table['order']))
			        $sql .= ' ORDER BY ' . implode($table['order'], ' ');
				if(!empty($table['limit']))
			        $sql .= ' LIMIT ' . implode($table['limit'], ', ');
			    $queries[] = $sql;
			    if(strstr($where, 'in ()'))
			    {
			        $store[@$table['store']] = array();
			        continue;
			    }
				$result = mysql_query($sql);
				if(!$result){
					die($sql . "\n" . mysql_error());
				}

				$ids = array();
				while(false !== ($row = mysql_fetch_assoc($result)))
				{
					$output[$tn][] = $this->jsonOutput($row, @$table['unique'], @$table['exclude']);
					$ids[] = @$row['id'];
					//break;
				}
				
				if(isset($table['store']))
				{
				    if(isset($store[$table['store']]) && is_array($store[$table['store']]))
				        $store[$table['store']] = array_merge($store[$table['store']], $ids);
				    else
				        $store[$table['store']] = $ids;
				    $output[$tn]['_store'] = $table['store'];
				}
			}
			
			return $output;
		}
		private function getStructure($tn)
		{
		  $sql = 'EXPLAIN ' . $tn;
		  $r = mysql_query($sql);
		  $cols = array();
		  while($row = mysql_fetch_assoc($r))
		  {
			if($row['Field'] == 'id')
			  continue;
			$cols[] = $row;
		  }
		  return $cols;
		}
		
		private function jsonOutput($row, $unique = array(), $exclude = array())
		{
			$temp = array('update' => array(), '_live_id' => @$row['id']);
			if(!is_array($unique))
			    $unique = array();
			if(!is_array($exclude))
			    $exclude = array();
			foreach($row as $key=>$value)
			{
			    if(in_array($key, $exclude))
			        continue;
			    if($key == 'user__id')
			        $value = '##user_id##';
			    if(($key != 'id' || in_array('id', $unique)))
			        $temp['update'][$key] = htmlentities($value);
			    
			}
			return $temp;
		}
	}
?>
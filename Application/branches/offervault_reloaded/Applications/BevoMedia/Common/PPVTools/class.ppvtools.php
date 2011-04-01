<?php
ini_set('display_errors', 1);

class PPVToolsModule {

//
// PPV Functions
//

 // Extractor Code
  function ppvtool_extractor($urls, $sdepth = '1'){

   //Scan all Keywords in Google for results.
   $urls = $this->clean_input($urls);
   unset($google_urls);
   $i = 0;

   reset($urls);
   foreach($urls as $key => $value){

    $page = 0;
    $sdepth = (int)($sdepth);
    while($page < $sdepth){

     $google_results = $this->google_search('site:'.trim($value), $page);
     usleep(100000); //Give google API some time between each fetch.

     $page_count = $google_results['responseData']['cursor']['estimatedResultCount'];
     if($page_count < $page*8) break;

     if($google_results['responseStatus'] == '200'){
      foreach($google_results['responseData']['results'] as $value2){
        $google_urls["$i"] = $value2;
        $i++;
      }
     }

     $page++;

    }
   }
   return($google_urls);
  }


 // Sniper Code
  function ppvtool_sniper($keywords, $sdepth = '1'){
   //Scan all Keywords in Google for results.
   $keywords = $this->clean_input($keywords);
   unset($google_urls);
   $i = 0;

   foreach($keywords as $key => $value){

    $page = 0;
    $sdepth = (int)($sdepth);
    while($page < $sdepth){

     $google_results = $this->google_search(trim($value), $page);
     usleep(50000); //Give google API some time between each fetch.

     if($google_results['responseStatus'] == '200'){
      foreach($google_results['responseData']['results'] as $value2){
        $google_urls["$i"] = $value2;
        $i++;
      }
     }

     $page++;

    }
   }
   return($google_urls);
  }


 // Site Keyword Code
  function ppvtool_sitekeywords($url){

   require_once('class.ppvtools.autokeyword.php');
   // Create a new curl resource
   $ch = curl_init();

   curl_setopt($ch, CURLOPT_URL, $url);
   curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
   curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
   $data = curl_exec($ch);

   //Clean up webpage and remove markup code.
     $data = $this->remove_HTML($data);
     $data = str_replace('&nbsp;', ' ', $data); 
     $data = str_replace('&amp;', '&', $data);
     $htmlchars = array('&idquo;', '&isquo;', '&rsquo;', '&rdquo;', '&copy;');
     $data = str_replace($htmlchars, '', $data);
     $data = ereg_replace("[^A-Za-z0-9]", " ", $data);

   //Set the length of Keywords you like
     $params['content'] = $data; //page content
     $params['min_word_length'] = 5; //minimum length of single words
     $params['min_word_occur'] = 3; //minimum occur of single words

     $params['min_2words_length'] = 3; //minimum length of words for 2 word phrases
     $params['min_2words_phrase_length'] = 10; //minimum length of 2 word phrases
     $params['min_2words_phrase_occur'] = 2; //minimum occur of 2 words phrase

     $params['min_3words_length'] = 3; //minimum length of words for 3 word phrases
     $params['min_3words_phrase_length'] = 10; //minimum length of 3 word phrases
     $params['min_3words_phrase_occur'] = 2; //minimum occur of 3 words phrase

     $keyword = new autokeyword($params, "iso-8859-1");

     unset($output);
     $output['one_word'] = $keyword->parse_words();
     $output['two_word'] = $keyword->parse_2words();
     $output['three_word'] = $keyword->parse_3words();
     $output['all'] = $keyword->get_keywords();

     return($output);

  }


 // Organic Relavent Keyword Code
  function ppvtool_relaventkeywords($keyword){

   require_once('class.ppvtools.autokeyword.php');
    $results = $this->ppvtool_sniper($keyword, 8);

    $keywords = '';
    foreach($results as $key => $value){
       $keywords .= $results["$key"]['titleNoFormatting'].' ';
       $keywords .= $results["$key"]['content'].' ';
    }

    //Clean up contant and remove markup code.
      $data = $this->remove_HTML($keywords);
      $data = $this->filter_stopwords($data);

   //Set the length of Keywords you like
      $params['content'] = $data; //page content
      $params['min_word_length'] = 5;  //minimum length of single words
      $params['min_word_occur'] = 6;  //minimum occur of single words

      $params['min_2words_length'] = 3;  //minimum length of words for 2 word phrases
      $params['min_2words_phrase_length'] = 10; //minimum length of 2 word phrases
      $params['min_2words_phrase_occur'] = 4; //minimum occur of 2 words phrase

      $params['min_3words_length'] = 3;  //minimum length of words for 3 word phrases
      $params['min_3words_phrase_length'] = 10; //minimum length of 3 word phrases
      $params['min_3words_phrase_occur'] = 4; //minimum occur of 3 words phrase

     $keyword_out = new autokeyword($params, "iso-8859-1");

     unset($output);
     $output['one_word'] = $keyword_out->parse_words();
     $output['two_word'] = $keyword_out->parse_2words();
     $output['three_word'] = $keyword_out->parse_3words();
     $output['all'] = $keyword_out->get_keywords();

     return($output);
  }


//
// Google API/JSON Interface Code
//

  function google_search($keyword, $page = '0'){

   static $firstrun = 0;
   static $chgoogle;

   if($firstrun == 0){
      // Create a new curl resource
      $chgoogle = curl_init();
      //require_once('class.ppvtools.json.php');
   }
   $firstrun++;

   $json = new Services_JSON();
   $page = $page*8;
   $url = "http://ajax.googleapis.com/ajax/services/search/web?v=1.0&rsz=large&q=".urlencode($keyword)."&start=".$page;

   // sendRequest
   curl_setopt($chgoogle, CURLOPT_URL, $url);
   curl_setopt($chgoogle, CURLOPT_RETURNTRANSFER, 1);
   curl_setopt($chgoogle, CURLOPT_TIMEOUT, 10);
   curl_setopt($chgoogle, CURLOPT_MAXCONNECTS, 1);
   $body = curl_exec($chgoogle);

   $google_dat = PPVobjToArray($json->decode($body));

   return($google_dat);
  }



//
// Alexa Interface Code
//

  function ppvtool_alexa_ranking($url){
   $chalexa = curl_init();

   $data_url = "http://data.alexa.com/data?cli=10&dat=s&url=".$url;

   // sendRequest
   curl_setopt($chalexa, CURLOPT_URL, $data_url);
   curl_setopt($chalexa, CURLOPT_RETURNTRANSFER, 1);
   curl_setopt($chalexa, CURLOPT_TIMEOUT, 10);
   curl_setopt($chalexa, CURLOPT_MAXCONNECTS, 1);
   $body = curl_exec($chalexa);
   curl_close($chalexa);

   $data = simplexml_load_string($body);
   $data = PPVobjToArray($data);

   return($data);
  }





//
// Alexa Search Ratio
//

  function ppvtool_alexa_ratio($keywordin){

  // Create a new curl resource
   $ch = curl_init();
   $url = 'http://www.alexa.com/search?q='.urlencode($keywordin);

  // sendRequest
  // note how referer is set manually

   curl_setopt($ch, CURLOPT_URL, $url);
   curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
   curl_setopt ($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.6) Gecko/20070725 Firefox/2.0.0.6");
   curl_setopt ($ch, CURLOPT_TIMEOUT, 60);
   $body = curl_exec($ch);


  //Fetch Summary
   $i = strpos($body, '<strong>QCI</strong>');
   $body_hd = substr($body, $i);
   $i = strpos($body_hd, '<span class="bottom"></span>');
   $body_hd = substr($body_hd, $i);
   $i = strpos($body_hd, '&nbsp;');
   $body_hd = substr($body_hd, $i+6);
   $i = strpos($body_hd, ' out of');
   $body_hd = substr($body_hd, 0, $i);

   //Search Index
      if(substr($body_hd, 0, 5) == 'class') $keyword['qci'] = '';
      else $keyword['qci'] = $body_hd;

  //Fetch Summary
   $i = strpos($body, '<strong>Query Popularity</strong>');
   $body_hd = substr($body, $i);
   $i = strpos($body_hd, '<span class="bottom"></span>');
   $body_hd = substr($body_hd, $i);
   $i = strpos($body_hd, '&nbsp;');
   $body_hd = substr($body_hd, $i+6);
   $i = strpos($body_hd, ' out of');
   $body_hd = substr($body_hd, 0, $i);

   //Search Ad Index
      $keyword['popularity'] = (int)($body_hd);

   return($keyword);

  }







//
// KeywordSpy Campaign Code
//

  //
  // Find Keywords Overview Stats
  //
  function ppvtool_kwspy_keywordstats($keywordin){

   // Create a new curl resource
   $ch = curl_init();


   //Distrubute Server Queries. (Helps to keep API from thinking this is an attack if more then one request is made at a time)
      //$url = 'http://www.keywordspy.com/research/search.aspx?q='.$keywordin.'&tab=keyword-overview';
   $url = 'http://www.andrewarsenault.com/bevo/cpc_relay.php?keyword='.urlencode($keywordin);
   require_once('class.ppvtools.table2array.php');

   // sendRequest
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt ($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.6) Gecko/20070725 Firefox/2.0.0.6");
    curl_setopt ($ch, CURLOPT_TIMEOUT, 60);
    $body = curl_exec($ch);

    $i = strpos($body, '<div class="panelTitle">Keyword Statistics</div>');
    $body_hd = substr($body, $i);
    $i = strpos($body_hd, '</table>');
    $body_hd = substr($body_hd, 0, $i+8);

   // Removal of CPC Values:
    $i = strpos($body_hd, '<td>CPC:</td>');			$body_hdtem = substr($body_hd, $i);
    $i = strpos($body_hdtem, '<td align="right">');		$body_hdtem = substr($body_hdtem, $i);
    $i = strpos($body_hdtem, '</td>');				$body_hdtem = substr($body_hdtem, 0, $i+5);
    $keyword['cpc'] = $this->remove_HTML($body_hdtem);

   // Removal of Monthly Values:
    $i = strpos($body_hd, '<td>Search Volume:</td>');		$body_hdtem = substr($body_hd, $i);
    $i = strpos($body_hdtem, '<td align="right">');		$body_hdtem = substr($body_hdtem, $i);
    $i = strpos($body_hdtem, '</td>');				$body_hdtem = substr($body_hdtem, 0, $i+5);
    $keyword['volume'] = $this->remove_HTML($body_hdtem);


   $i = strpos($body, '<td class="overview-header-left">Related');
   $body_hd = substr($body, $i);
   $i = strpos($body_hd, '</table>');
   $body_hd = '<table><tbody><tr>'.substr($body_hd, 0, $i+8);


   $tbl = new tableExtractor;
   $tbl->source = $body_hd;
   $tbl->anchor = '';
   $tpl->anchorWithin = true;
   $d = $tbl->extractTable();

   $i = 0;
   unset($key_data);
   foreach($d as $key => $value){
      $key_data["$i"]['keyword'] = strip_tags(current($value));
      $key_data["$i"]['volume'] = @$value['Search Volume'];
      $key_data["$i"]['cost'] = @$value['CPC'];
      $i++;
   }
   unset($key_data['10']);

   $keyword['related'] = $key_data;

   curl_close($ch);
   return($keyword);

  }

  //
  // Find Sites Main Advertising Campaigns
  //
  function ppvtool_kwspy_urlstats($urlin){

   $ch = curl_init();
   $url = 'http://www.keywordspy.com/research/search.aspx?q='.$urlin;
   require_once('class.ppvtools.table2array.php');

   // sendRequest
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt ($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.6) Gecko/20070725 Firefox/2.0.0.6");
    curl_setopt ($ch, CURLOPT_TIMEOUT, 60);
    $body = curl_exec($ch);


   //Fetch Summary
   $i = strpos($body, '<table id="sumData"');
   $body_hd = substr($body, $i);
   $i = strpos($body_hd, '</table>');
   $body_hd = substr($body_hd, 0, $i+8);

   // Removal of CPC Values:
    $i = strpos($body_hd, '<b>Daily Ad Budget:</b>');	$body_hdtem = substr($body_hd, $i+8);
    $i = strpos($body_hdtem, '<b>');			$body_hdtem = substr($body_hdtem, $i);
    $i = strpos($body_hdtem, '</b></td>');		$body_hdtem = substr($body_hdtem, 3, $i-3);
    $website['daily_budget'] = $body_hdtem;

   // Removal of Monthly Values:
    $i = strpos($body_hd, '<td>Total Clicks/Day:</td>');$body_hdtem = substr($body_hd, $i+8);
    $i = strpos($body_hdtem, '<td>');			$body_hdtem = substr($body_hdtem, $i);
    $i = strpos($body_hdtem, '</td>');			$body_hdtem = substr($body_hdtem, 4, $i-4);
    $website['clicks_day'] = $body_hdtem;

   // Removal of Monthly Values:
    $i = strpos($body_hd, '<td>Average Ad Position:</td>');$body_hdtem = substr($body_hd, $i+8);
    $i = strpos($body_hdtem, '<td>');			$body_hdtem = substr($body_hdtem, $i);
    $i = strpos($body_hdtem, '</td>');			$body_hdtem = substr($body_hdtem, 4, $i-4);
    $website['average_position'] = $body_hdtem;

   // Removal of Monthly Values:
    $i = strpos($body_hd, '<td>Average Cost/Click:</td>');$body_hdtem = substr($body_hd, $i+8);
    $i = strpos($body_hdtem, '<td>');			$body_hdtem = substr($body_hdtem, $i);
    $i = strpos($body_hdtem, '</td>');			$body_hdtem = substr($body_hdtem, 4, $i-4);
    $website['average_cost'] = $body_hdtem;

    if(substr($website['daily_budget'], 0, 1) != '$') return(-1);

   $i = strpos($body, '<td class="overview-header-left">Competitors');
   $body_hd = substr($body, $i);
   $i = strpos($body_hd, '</table>');
   $body_hd = '<table><tbody><tr>'.substr($body_hd, 0, $i+8);

   $tbl = new tableExtractor;
   $tbl->source = $body_hd;
   $tbl->anchor = '';
   $tpl->anchorWithin = true;
   $d = $tbl->extractTable();

   $i = 0;
   unset($key_data);
   foreach($d as $key => $value){
      $key_data["$i"]['Competitors'] = strip_tags(current($value));
      $key_data["$i"]['Keywords'] = $value['Keywords'];
      $i++;
   }
   unset($key_data['10']);

   $website['ads'] = $key_data;
   return($website);

  }


  //
  function ppvtool_kwspy_ads($keywordin, $source = 0){
   // Create a new curl resource
   $ch = curl_init();

   if($source == '1'){
      $url = 'http://www.andrewarsenault.com/bevo/ads_relay.php?keyword='.urlencode($keywordin);
   }else{
      $url = 'http://www.keywordspy.com/research/exportFiles.aspx?tab=keyword-details&exportType=text&h=undefined&q='.urlencode($keywordin).'&page=1&market=us&filter=&sort=ROI%20DESC';
   }

   curl_setopt($ch, CURLOPT_URL, $url);
   curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
   curl_setopt ($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.6) Gecko/20070725 Firefox/2.0.0.6");
   curl_setopt ($ch, CURLOPT_TIMEOUT, 60);

   $data = curl_exec($ch);
   $data = utf16_to_utf8($data);

   if(substr($data, 0, 6) == '<html>') return(-1);

      // Unpack and insert CSV data
        $handle = fopen("php://memory", 'r+');
        fputs($handle, $data);
        rewind($handle);

	$row = 0;
	while (($data = fgetcsv($handle, NULL, "\t")) !== FALSE) {
	    $num = count($data);

	    for ($c=0; $c < $num; $c++) {
	        if($row == 0) {
	           $csv_header[$c] = utf8_encode($data[$c]);
	        }else{
	           $header = $csv_header[$c];
	           $importcom[$row][$header] = $data[$c];
	        }
	    }
	    $row++;
	}
	fclose($handle);
   if($importcom['1']['Title'] == '') return(-1);
   $total_lines = count($importcom);

        //Clean up empty junk.
        unset($importcom[$total_lines-2]);
        unset($importcom[$total_lines-1]);
        unset($importcom[$total_lines]);



   return($importcom);
  }


//
// Useful Functions
//

 // Convert Post URLs
 function post_urlpass($input){
   if(substr($input, 0, 7) == 'http://') $input = substr($input, 7);
   return($input);
 }


 // Convert Strings into Single Arrays.
 function clean_input($input){
   //Convert String to Single Array
   if(!is_array($input)){
      $input = array(0 => $input);
   }
   return($input);
 }

 //Filter Tags and Stop Words
 function filter_stopwords($input){
    $data = str_replace('&nbsp;', ' ', $input); 
    $data = str_replace('&amp;', '&', $data);
    $htmlchars = array('&idquo;', '&isquo;', '&rsquo;', '&rdquo;', '&copy;', '&middot;', '&quote;', '&amp;');
    $data = str_replace($htmlchars, '', $data);
    $data = ereg_replace("[^A-Za-z0-9]", " ", $data);
    $htmlchars = array(' and ', ' more ', ' is ', ' a ', ' middot ', ' the ', ' for ');
    $data = str_replace($htmlchars, '', $data);

    return($data);
 }

 function remove_HTML($s , $keep = '' , $expand = 'script|style|noframes|select|option'){
        /**///prep the string
        $s = ' ' . strtolower($s);
       
        /**///initialize keep tag logic
        if(strlen($keep) > 0){
            $k = explode('|',$keep);
            for($i=0;$i<count($k);$i++){
                $s = str_replace('<' . $k[$i],'[{(' . $k[$i],$s);
                $s = str_replace('</' . $k[$i],'[{(/' . $k[$i],$s);
            }
        }
       
        //begin removal
        /**///remove comment blocks
        while(strpos($s,'<!--') > 0){
            $pos[1] = strpos($s,'<!--'); //stripos
            $pos[2] = strpos($s,'-->', $pos[1]); //stripos
            $len[1] = $pos[2] - $pos[1] + 3;
            $x = substr($s,$pos[1],$len[1]);
            $s = str_replace($x,'',$s);
        }
       
        /**///remove tags with content between them
        if(strlen($expand) > 0){
            $e = explode('|',$expand);
            for($i=0;$i<count($e);$i++){
                while(strpos($s,'<' . $e[$i]) > 0){
                    $len[1] = strlen('<' . $e[$i]);
                    $pos[1] = strpos($s,'<' . $e[$i]); //stripos
                    $pos[2] = strpos($s,$e[$i] . '>', $pos[1] + $len[1]);
                    $len[2] = $pos[2] - $pos[1] + $len[1];
                    $x = substr($s,$pos[1],$len[2]);
                    $s = str_replace($x,'',$s);
                }
            }
        }
       
        /**///remove remaining tags
        while(strpos($s,'<') > 0){
            $pos[1] = strpos($s,'<');
            $pos[2] = strpos($s,'>', $pos[1]);
            $len[1] = $pos[2] - $pos[1] + 1;
            $x = substr($s,$pos[1],$len[1]);
            $s = str_replace($x,'',$s);
        }
       
        /**///finalize keep tag
        for($i=0;$i<count(@$k);$i++){
            $s = str_replace('[{(' . $k[$i],'<' . $k[$i],$s);
            $s = str_replace('[{(/' . $k[$i],'</' . $k[$i],$s);
        }
       
        $s = trim(html_entity_decode($s));
        return $s;
 }

}


 function PPVobjToArray($data){
  if (is_object($data)) $data = get_object_vars($data);
  return (is_array($data)) ? array_map(__FUNCTION__,$data) : $data;
 }


 //Convert Encoding Type
 function utf16_to_utf8($str) {
    $c0 = ord($str[0]);
    $c1 = ord($str[1]);

    if ($c0 == 0xFE && $c1 == 0xFF) {
        $be = true;
    } else if ($c0 == 0xFF && $c1 == 0xFE) {
        $be = false;
    } else {
        return $str;
    }

    $str = substr($str, 2);
    $len = strlen($str);
    $dec = '';
    for ($i = 0; $i < $len; $i += 2) {
        $c = ($be) ? ord($str[$i]) << 8 | ord($str[$i + 1]) : 
                ord($str[$i + 1]) << 8 | ord($str[$i]);
        if ($c >= 0x0001 && $c <= 0x007F) {
            $dec .= chr($c);
        } else if ($c > 0x07FF) {
            $dec .= chr(0xE0 | (($c >> 12) & 0x0F));
            $dec .= chr(0x80 | (($c >>  6) & 0x3F));
            $dec .= chr(0x80 | (($c >>  0) & 0x3F));
        } else {
            $dec .= chr(0xC0 | (($c >>  6) & 0x1F));
            $dec .= chr(0x80 | (($c >>  0) & 0x3F));
        }
    }
    return $dec;
 }





// Useful Arrays

 function ppvarray_us_states(){
      $arr_us_states = array(
	'AL'	=>	'Alabama',
	'AK'	=>	'Alaska',
	'AS'	=>	'American Samoa',
	'AZ'	=>	'Arizona',
	'AR'	=>	'Arkansas',
	'AE'	=>	'Armed Forces - Europe',
	'AP'	=>	'Armed Forces - Pacific',
	'AA'	=>	'Armed Forces - USA/Canada',
	'CA'	=>	'California',
	'CO'	=>	'Colorado',
	'CT'	=>	'Connecticut',
	'DE'	=>	'Delaware',
	'DC'	=>	'District of Columbia',
	'FL'	=>	'Florida',
	'GA'	=>	'Georgia',
	'GU'	=>	'Guam',
	'HI'	=>	'Hawaii',
	'ID'	=>	'Idaho',
	'IL'	=>	'Illinois',
	'IN'	=>	'Indiana',
	'IA'	=>	'Iowa',
	'KS'	=>	'Kansas',
	'KY'	=>	'Kentucky',
	'LA'	=>	'Louisiana',
	'ME'	=>	'Maine',
	'MD'	=>	'Maryland',
	'MA'	=>	'Massachusetts',
	'MI'	=>	'Michigan',
	'MN'	=>	'Minnesota',
	'MS'	=>	'Mississippi',
	'MO'	=>	'Missouri',
	'MT'	=>	'Montana',
	'NE'	=>	'Nebraska',
	'NV'	=>	'Nevada',
	'NH'	=>	'New Hampshire',
	'NJ'	=>	'New Jersey',
	'NM'	=>	'New Mexico',
	'NY'	=>	'New York',
	'NC'	=>	'North Carolina',
	'ND'	=>	'North Dakota',
	'OH'	=>	'Ohio',
	'OK'	=>	'Oklahoma',
	'OR'	=>	'Oregon',
	'PA'	=>	'Pennsylvania',
	'PR'	=>	'Puerto Rico',
	'RI'	=>	'Rhode Island',
	'SC'	=>	'South Carolina',
	'SD'	=>	'South Dakota',
	'TN'	=>	'Tennessee',
	'TX'	=>	'Texas',
	'UT'	=>	'Utah',
	'VT'	=>	'Vermont',
	'VI'	=>	'Virgin Islands',
	'VA'	=>	'Virginia',
	'WA'	=>	'Washington',
	'WV'	=>	'West Virginia',
	'WI'	=>	'Wisconsin',
	'WY'	=>	'Wyoming'
      );
    return($arr_us_states);
 }

 function ppvarray_countries(){
return array(
'AF'=>'Afghanistan',
'AL'=>'Albania',
'DZ'=>'Algeria',
'AS'=>'American Samoa',
'AD'=>'Andorra',
'AO'=>'Angola',
'AI'=>'Anguilla',
'AQ'=>'Antarctica',
'AG'=>'Antigua And Barbuda',
'AR'=>'Argentina',
'AM'=>'Armenia',
'AW'=>'Aruba',
'AU'=>'Australia',
'AT'=>'Austria',
'AZ'=>'Azerbaijan',
'BS'=>'Bahamas',
'BH'=>'Bahrain',
'BD'=>'Bangladesh',
'BB'=>'Barbados',
'BY'=>'Belarus',
'BE'=>'Belgium',
'BZ'=>'Belize',
'BJ'=>'Benin',
'BM'=>'Bermuda',
'BT'=>'Bhutan',
'BO'=>'Bolivia',
'BA'=>'Bosnia And Herzegovina',
'BW'=>'Botswana',
'BV'=>'Bouvet Island',
'BR'=>'Brazil',
'IO'=>'British Indian Ocean Territory',
'BN'=>'Brunei',
'BG'=>'Bulgaria',
'BF'=>'Burkina Faso',
'BI'=>'Burundi',
'KH'=>'Cambodia',
'CM'=>'Cameroon',
'CA'=>'Canada',
'CV'=>'Cape Verde',
'KY'=>'Cayman Islands',
'CF'=>'Central African Republic',
'TD'=>'Chad',
'CL'=>'Chile',
'CN'=>'China',
'CX'=>'Christmas Island',
'CC'=>'Cocos (Keeling) Islands',
'CO'=>'Columbia',
'KM'=>'Comoros',
'CG'=>'Congo',
'CK'=>'Cook Islands',
'CR'=>'Costa Rica',
'CI'=>'Cote D\'Ivorie (Ivory Coast)',
'HR'=>'Croatia (Hrvatska)',
'CU'=>'Cuba',
'CY'=>'Cyprus',
'CZ'=>'Czech Republic',
'CD'=>'Democratic Republic Of Congo (Zaire)',
'DK'=>'Denmark',
'DJ'=>'Djibouti',
'DM'=>'Dominica',
'DO'=>'Dominican Republic',
'TP'=>'East Timor',
'EC'=>'Ecuador',
'EG'=>'Egypt',
'SV'=>'El Salvador',
'GQ'=>'Equatorial Guinea',
'ER'=>'Eritrea',
'EE'=>'Estonia',
'ET'=>'Ethiopia',
'FK'=>'Falkland Islands (Malvinas)',
'FO'=>'Faroe Islands',
'FJ'=>'Fiji',
'FI'=>'Finland',
'FR'=>'France',
'FX'=>'France, Metropolitan',
'GF'=>'French Guinea',
'PF'=>'French Polynesia',
'TF'=>'French Southern Territories',
'GA'=>'Gabon',
'GM'=>'Gambia',
'GE'=>'Georgia',
'DE'=>'Germany',
'GH'=>'Ghana',
'GI'=>'Gibraltar',
'GR'=>'Greece',
'GL'=>'Greenland',
'GD'=>'Grenada',
'GP'=>'Guadeloupe',
'GU'=>'Guam',
'GT'=>'Guatemala',
'GN'=>'Guinea',
'GW'=>'Guinea-Bissau',
'GY'=>'Guyana',
'HT'=>'Haiti',
'HM'=>'Heard And McDonald Islands',
'HN'=>'Honduras',
'HK'=>'Hong Kong',
'HU'=>'Hungary',
'IS'=>'Iceland',
'IN'=>'India',
'ID'=>'Indonesia',
'IR'=>'Iran',
'IQ'=>'Iraq',
'IE'=>'Ireland',
'IL'=>'Israel',
'IT'=>'Italy',
'JM'=>'Jamaica',
'JP'=>'Japan',
'JO'=>'Jordan',
'KZ'=>'Kazakhstan',
'KE'=>'Kenya',
'KI'=>'Kiribati',
'KW'=>'Kuwait',
'KG'=>'Kyrgyzstan',
'LA'=>'Laos',
'LV'=>'Latvia',
'LB'=>'Lebanon',
'LS'=>'Lesotho',
'LR'=>'Liberia',
'LY'=>'Libya',
'LI'=>'Liechtenstein',
'LT'=>'Lithuania',
'LU'=>'Luxembourg',
'MO'=>'Macau',
'MK'=>'Macedonia',
'MG'=>'Madagascar',
'MW'=>'Malawi',
'MY'=>'Malaysia',
'MV'=>'Maldives',
'ML'=>'Mali',
'MT'=>'Malta',
'MH'=>'Marshall Islands',
'MQ'=>'Martinique',
'MR'=>'Mauritania',
'MU'=>'Mauritius',
'YT'=>'Mayotte',
'MX'=>'Mexico',
'FM'=>'Micronesia',
'MD'=>'Moldova',
'MC'=>'Monaco',
'MN'=>'Mongolia',
'MS'=>'Montserrat',
'MA'=>'Morocco',
'MZ'=>'Mozambique',
'MM'=>'Myanmar (Burma)',
'NA'=>'Namibia',
'NR'=>'Nauru',
'NP'=>'Nepal',
'NL'=>'Netherlands',
'AN'=>'Netherlands Antilles',
'NC'=>'New Caledonia',
'NZ'=>'New Zealand',
'NI'=>'Nicaragua',
'NE'=>'Niger',
'NG'=>'Nigeria',
'NU'=>'Niue',
'NF'=>'Norfolk Island',
'KP'=>'North Korea',
'MP'=>'Northern Mariana Islands',
'NO'=>'Norway',
'OM'=>'Oman',
'PK'=>'Pakistan',
'PW'=>'Palau',
'PA'=>'Panama',
'PG'=>'Papua New Guinea',
'PY'=>'Paraguay',
'PE'=>'Peru',
'PH'=>'Philippines',
'PN'=>'Pitcairn',
'PL'=>'Poland',
'PT'=>'Portugal',
'PR'=>'Puerto Rico',
'QA'=>'Qatar',
'RE'=>'Reunion',
'RO'=>'Romania',
'RU'=>'Russia',
'RW'=>'Rwanda',
'SH'=>'Saint Helena',
'KN'=>'Saint Kitts And Nevis',
'LC'=>'Saint Lucia',
'PM'=>'Saint Pierre And Miquelon',
'VC'=>'Saint Vincent And The Grenadines',
'SM'=>'San Marino',
'ST'=>'Sao Tome And Principe',
'SA'=>'Saudi Arabia',
'SN'=>'Senegal',
'SC'=>'Seychelles',
'SL'=>'Sierra Leone',
'SG'=>'Singapore',
'SK'=>'Slovak Republic',
'SI'=>'Slovenia',
'SB'=>'Solomon Islands',
'SO'=>'Somalia',
'ZA'=>'South Africa',
'GS'=>'South Georgia And South Sandwich Islands',
'KR'=>'South Korea',
'ES'=>'Spain',
'LK'=>'Sri Lanka',
'SD'=>'Sudan',
'SR'=>'Suriname',
'SJ'=>'Svalbard And Jan Mayen',
'SZ'=>'Swaziland',
'SE'=>'Sweden',
'CH'=>'Switzerland',
'SY'=>'Syria',
'TW'=>'Taiwan',
'TJ'=>'Tajikistan',
'TZ'=>'Tanzania',
'TH'=>'Thailand',
'TG'=>'Togo',
'TK'=>'Tokelau',
'TO'=>'Tonga',
'TT'=>'Trinidad And Tobago',
'TN'=>'Tunisia',
'TR'=>'Turkey',
'TM'=>'Turkmenistan',
'TC'=>'Turks And Caicos Islands',
'TV'=>'Tuvalu',
'UG'=>'Uganda',
'UA'=>'Ukraine',
'AE'=>'United Arab Emirates',
'UK'=>'United Kingdom',
'US'=>'United States',
'UM'=>'United States Minor Outlying Islands',
'UY'=>'Uruguay',
'UZ'=>'Uzbekistan',
'VU'=>'Vanuatu',
'VA'=>'Vatican City (Holy See)',
'VE'=>'Venezuela',
'VN'=>'Vietnam',
'VG'=>'Virgin Islands (British)',
'VI'=>'Virgin Islands (US)',
'WF'=>'Wallis And Futuna Islands',
'EH'=>'Western Sahara',
'WS'=>'Western Samoa',
'YE'=>'Yemen',
'YU'=>'Yugoslavia',
'ZM'=>'Zambia',
'ZW'=>'Zimbabwe'
);
 }

 function ppvarray_namesmale(){
return array(
'JAMES',
'JOHN',
'ROBERT',
'MICHAEL',
'WILLIAM',
'DAVID',
'RICHARD',
'CHARLES',
'JOSEPH',
'THOMAS',
'CHRISTOPHER',
'DANIEL',
'PAUL',
'MARK',
'DONALD',
'GEORGE',
'KENNETH',
'STEVEN',
'EDWARD',
'BRIAN',
'RONALD',
'ANTHONY',
'KEVIN',
'JASON',
'MATTHEW',
'GARY',
'TIMOTHY',
'JOSE',
'LARRY',
'JEFFREY',
'FRANK',
'SCOTT',
'ERIC',
'STEPHEN',
'ANDREW',
'RAYMOND',
'GREGORY',
'JOSHUA',
'JERRY',
'DENNIS',
'WALTER',
'PATRICK',
'PETER',
'HAROLD',
'DOUGLAS',
'HENRY',
'CARL',
'ARTHUR',
'RYAN',
'ROGER'
);
 }


 function ppvarray_namesfemale(){
return array(
'MARY',  
'PATRICIA', 
'LINDA', 
'BARBARA', 
'ELIZABETH', 
'JENNIFER', 
'MARIA', 
'SUSAN', 
'MARGARET', 
'DOROTHY', 
'LISA', 
'NANCY', 
'KAREN', 
'BETTY', 
'HELEN', 
'SANDRA', 
'DONNA', 
'CAROL', 
'RUTH', 
'SHARON', 
'MICHELLE', 
'LAURA', 
'SARAH', 
'KIMBERLY', 
'DEBORAH', 
'JESSICA', 
'SHIRLEY', 
'CYNTHIA', 
'ANGELA', 
'MELISSA', 
'BRENDA', 
'AMY', 
'ANNA', 
'REBECCA', 
'VIRGINIA', 
'KATHLEEN', 
'PAMELA', 
'MARTHA', 
'DEBRA', 
'AMANDA', 
'STEPHANIE', 
'CAROLYN', 
'CHRISTINE', 
'MARIE', 
'JANET', 
'CATHERINE', 
'FRANCES', 
'ANN', 
'JOYCE', 
'DIANE'
);
 }

 function ppvarray_keywords(){
return array(
'accessories',
'advertising specialties',
'angels',
'anniversary',
'antique',
'apparel',
'apple',
'aromatherapy',
'art',
'arts',
'audio',
'awards',
'baby',
'baby shower',
'backpack',
'bag',
'balloon',
'basket',
'bass',
'bath',
'battery',
'bead',
'belt',
'binoculars',
'bird',
'birthday',
'boat',
'boats',
'book',
'boxes',
'boy',
'bra',
'brass',
'california',
'camping',
'canada',
'canadian',
'candles',
'canon',
'cap',
'car',
'card',
'case',
'cat',
'cd',
'cds',
'ceramics',
'child',
'childrens clothes',
'childrens clothing',
'chocolate',
'christian',
'christmas',
'cigar',
'classic',
'clocks',
'coffee',
'collectible',
'collecting',
'collector',
'computer',
'cooking',
'cosmetics',
'costume',
'cotton',
'craft',
'crystal',
'crystals',
'custom',
'decoration',
'diet',
'digital',
'dog',
'doll',
'drawing',
'dresses',
'drum',
'dvd',
'easter',
'ecommerce',
'education',
'educational',
'electronics',
'embroidery',
'equipment',
'essential oil',
'fabric',
'family',
'fantasy',
'fashion',
'figurines',
'fish',
'fishing',
'fitness',
'flower',
'food',
'football',
'footwear',
'frame',
'french',
'fun',
'furniture',
'gallery',
'game',
'garden',
'gift',
'girl',
'glass',
'gold',
'golf',
'gourmet',
'graduation',
'guitars',
'halloween',
'hard to find',
'hardware',
'health',
'herb',
'history',
'hobby',
'holiday',
'home',
'home decor',
'hp',
'hunting',
'information',
'jacket',
'jewelry',
'kit',
'kitchen',
'knives',
'labels',
'lace',
'lamps',
'leather',
'light',
'lingerie',
'literature',
'luggage',
'magazine',
'magnets',
'map',
'memorabilia',
'men',
'military',
'minerals',
'monitor',
'mothers day',
'mugs',
'music',
'newborn',
'nutrition',
'office',
'oils',
'old',
'organic',
'ornaments',
'outdoor',
'painting',
'panasonic',
'pant',
'patterns',
'pens',
'pet',
'pet supplies',
'photography',
'picture',
'pin',
'plant',
'plastic',
'porcelain',
'posters',
'pottery',
'present',
'print',
'printer',
'promotional product',
'quilt',
'quilting',
'recipes',
'refurbished',
'religion',
'rock',
'rose',
'safety',
'scales',
'science',
'science fiction',
'security',
'sewing',
'shirt',
'shop',
'silver',
'skin care',
'soap',
'socks',
'software',
'speakers',
'sports',
'stationery',
'stone',
'supplements',
'survival',
'swimwear',
'switch',
'table',
'tea',
'tool',
'toshiba',
'toy',
'travel',
'usa',
'used',
'video',
'vintage',
'vinyl',
'vitamins',
'watch',
'wedding',
'wholesale',
'wine',
'wireless',
'women',
'wood',
'yamaha'
 );
}

?>
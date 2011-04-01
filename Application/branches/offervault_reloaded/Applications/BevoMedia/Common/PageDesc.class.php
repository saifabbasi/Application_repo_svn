<?php
/** PageDesc class
  * created 100805 by Robert Augustin <robert@soapdesigned.com>
  * manages the page description box on any given page with cookiez
  * generates the HTML output for the description box
  */

class PageDesc {

	/** SoapPageDesc()
	  * generates page description with the right "closed" or open class, depending on which page we're on
	  * relies on the unique page name stored in the PageHelper object (added 100805)
	  * @param $pagehelper object (usually that's just $this->PageHelper)
	  * @param $can_toggle bool set to false and the page description box will always show and offer no toggle option
	  * @param $custom_title string custom title for this view. if set, it overrides the PageHelper preset
	  * @param $custom_image string url/filename of a custom image for the page description. if set, it overrides the image that is preset in PageHelper for this view
	  * @param $custom_class string CSS classname that will be added to div#pagedesc, for individual styling
	  */
	public function ShowDesc($pagehelper, $can_toggle=true, $custom_title=false, $custom_image=false, $custom_class=false) {
		$cookname = '__bevoPDESC';
		$class_closed = 'closed';
		$o = false;
		$divclasses = array();
		
		//if the page has a page heading+subheading
		if(is_object($pagehelper) && !empty($pagehelper) && $pagehelper->Heading != '' && $pagehelper->SubHeading != '') {
			
			if($pagehelper->UniquePageName != '' && isset($_COOKIE[$cookname]) && $can_toggle) {
				$cook = $_COOKIE[$cookname];
				$arr = explode('|', $cook);
				if(in_array($pagehelper->UniquePageName, $arr)) {
					$o = $class_closed;
					setcookie($cookname, $cook, time()+60*60*24*30*12, '/'); //1 year
				}
			}
			
			//get classes
			if($o && $can_toggle)
				$divclasses[] = $class_closed;
			if($custom_class)
				$divclasses[] = $custom_class;
			
			//output
			$out = '<div id="pagedesc"';
			$out .= !empty($divclasses) ? ' class="'.implode(' ',$divclasses).'"' : '';
			$out .= '><div class="inside">';
				if($custom_image)
					$out .= '<div class="img"><img src="'.SCRIPT_ROOT.'img/'.$custom_image.'" alt="" /></div>';
				else	$out .= $pagehelper->HeadingImage ? '<div class="img"><img src="'.SCRIPT_ROOT.'img/'.$pagehelper->HeadingImage.'" alt="" /></div>' : '';
				$out .= $custom_title ? '<h2>'.$custom_title.'</h2>' : '<h2>'.$pagehelper->Heading.'</h2>';
				$out .= '<p>'.$pagehelper->SubHeading.'</p>';
				$out .= '<div class="clear"></div></div>';
				$out .= $can_toggle ? '<a class="btn pagedesc_toggle" title="Toggle page description for this page" href="#'.$pagehelper->UniquePageName.'">Toggle page description for this page</a>' : '';
			$out .= '</div>';
			
			return $out;
		}
	}
}

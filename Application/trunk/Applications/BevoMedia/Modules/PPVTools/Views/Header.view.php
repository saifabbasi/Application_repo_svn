<?php /*
<div class="SkyBox"><div class="SkyBoxTopLeft"><div class="SkyBoxTopRight"><div class="SkyBoxBotLeft"><div class="SkyBoxBotRight">
        <table width="590" cellspacing="0" cellpadding="5" border="0">
            <tr valign="top">
                <td width="127" valign="middle"><img style="padding: 5px;" src="/Themes/BevoMedia/img/ppvicon.gif" border="0" alt=""></td>
                <td class="main">
                    <h4>Premium Research Tool</h4>

                    <br />
                    
					Whether you have a search, PPV, or media buy campaign, the Bevo Premium Research Tools features the most valuable, time saving internet marketing tools in the market. Take advantage of these tools today and get a leg up on your competition. For Premium Members only.
					
					<br /><br />
                </td>
            </tr>
        </table>
    </div></div></div></div></div> * / ?>


<style>	/* General * /
	#cssdropdown, #cssdropdown ul { position: absolute; z-index: 100; list-style: none; font-weight: bold;}
	#cssdropdown, #cssdropdown * { padding: 0; margin: 0; font-weight: bold; color: #ffffff;}
	
	/* Head links * /
	#cssdropdown li.headlink { line-height: 26px; width: 156px; float: left; margin-left: -1px; text-align: center; }
	#cssdropdown li.headlink a { display: block; text-decoration: none; font-size: 11px; }
	#cssdropdown li.headlink a:hover { background: url(/Themes/BevoMedia/img/bluegradientarrow.gif)}
	
	/* Child lists and links * /
	#cssdropdown li.headlink ul {width: 200px; display: none; border-top: 1px black solid; text-align: left; }
	#cssdropdown li.headlink:hover ul {margin-top: 2px; display: block; border: 2px solid #DFDFDF; padding: 2px;}
	#cssdropdown li.headlink ul li a {line-height: 26px; text-align: center; height: 26px; background: url(/Themes/BevoMedia/img/bluegradientarrowgray.gif) top; }
	#cssdropdown li.headlink ul li a:hover { text-decoration: none; color: #fff;  background: url(/Themes/BevoMedia/img/bluegradientarrow.gif)}
	
	/* Pretty styling * /
	#cssdropdown a { } #cssdropdown ul li a:hover { text-decoration: none; }
	#cssdropdown li.headlink { height: 28px; background-color: white; background-image: url(/Themes/BevoMedia/img/bluegradientarrowgray.gif); }
	#cssdropdown li.headlink ul {  }
//</style> 
*/ ?>
<script language="javascript">
	window.onload = function()
	{
		var lis = document.getElementsByTagName('li');
		for(i = 0; i < lis.length; i++)
		{
			var li = lis[i];
			if (li.className == 'headlink')
			{
				if(this.getElementsByTagName)
				{
					li.onmouseover = function() { this.getElementsByTagName('ul').item(0).style.display = 'block'; }
					li.onmouseout = function() { this.getElementsByTagName('ul').item(0).style.display = 'none'; }
				}
			}
		}
	}

</script>

<?php	echo $tmptmp = $soap_module ? SoapPageMenu('ppvtools',$soap_module[0],$soap_module[1]) : '<div id="pagemenu"></div>'; 
	echo $this->PageDesc->ShowDesc($this->PageHelper);
?>
<?php /*
<ul id="cssdropdown">
	<li class="headlink">
		<a href="#">Get URLs</a>
		<ul>
		  <li><a href="/BevoMedia/PPVTools/PageSniper.html">Get URLs from keywords</a></li>
		  <li><a href="/BevoMedia/PPVTools/Extractor.html">Extract links from a site</a></li>
		  <li><a href="/BevoMedia/PPVTools/Alexa.html">Alexa search ranking</a></li>
		</ul>
	</li>
	<li class="headlink">
		<a href="#">Get Keywords</a>
		 <ul>
		  <li><a href="/BevoMedia/PPVTools/PageSearchKeyword.html">Suggested Keywords</a></li>
		  <li><a href="/BevoMedia/PPVTools/SiteKeywords.html">Site Keywords</a></li>
		 </ul>
	</li>
	<li class="headlink">
		<a href="#">Keyword Spy</a>
		 <ul>
		  <li><a href="/BevoMedia/PPVTools/WebSiteSpy.html">Website Spy</a></li>
		  <li><a href="/BevoMedia/PPVTools/KeywordSpy.html">Keyword Spy</a></li>
		  <li><a href="/BevoMedia/PPVTools/KeywordComparator.html">Keyword Comparator</a></li>
		 </ul>
	</li>
	<li class="headlink">
		<a href="/BevoMedia/PPVTools/LinkBuilder.html">List Builder</a>
	</li>

</ul> */ ?>

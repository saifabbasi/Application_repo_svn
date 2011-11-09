<?php 

	if ($this->User->vaultID==0 && !$this->User->IsSubscribed(User::PRODUCT_FREE_SELF_HOSTED))
	{
		header('Location: /BevoMedia/User/AddCreditCard.html');
		die;
	}
?>

<div id="pagemenu">
	<ul>
		<li><a href="/BevoMedia/User/RackspaceWizard.html">&laquo; Back to the RackSpace Wizard<span></span></a></li>
	</ul>
</div>
<?php echo $this->PageDesc->ShowDesc($this->PageHelper); ?>

<div class="pagecontent inpa-wrapper">
	<div class="inpa-box">
		<div class="inpa-boxtop"></div>
		<ul class="inpa-numlist">
			<li class="inpa-num1">Right-Click and save this text file:
				<a class="button inpa-download-rc" href="http://beta.bevomedia.com/install.php.txt">Download the Auto-Installer</a>
			</li>
			<li class="inpa-num2">Rename the text file to <code>install.php</code></li>
			<li class="inpa-num3">Upload to your server via FTP</li>
			<li class="inpa-fin">Done! Go to <strong><em>yoursite.com/install.php</em></strong>, and our automated installer will do the rest!</li>
		</ul>		
		<div class="inpa-boxbutt"></div>
	</div>
	<div class="inpa-box inpa-manual">
		<div class="inpa-boxtop"></div>		
		<ul class="inpa-numlist">
			<li class="inpa-num1">Download <strong><em>SelfHosted.zip</em></strong>
				<a class="button inpa-download" onclick="alert('Manual Install is unavailable during the beta period. Please use the automated installer.');" oldhref="http://beta.bevomedia.com/SelfHosted.zip">Download SelfHosted.zip</a>
			</li>
			<li class="inpa-num2">Unzip the files to a folder on your desktop</li>
			<li class="inpa-num3">Before uploading, you must manually edit the <code>config.ini</code> file and enter your database information</li>
			<li class="inpa-num4">Insert the contents of <code>bevomedia-self-hosted.sql</code>. There are several ways to do this, but your server probably has a control panel with PHPMyAdmin installed. Log into PHPMyAdmin, select the target database to install to, click the "SQL" tab up top, then select the "Import" tab, and upload <code>bevomedia-self-hosted.sql</code>.</li>
			<li class="inpa-num5">Delete the file <code>bevomedia-self-hosted.sql</code>, it is no longer needed</li>
			<li class="inpa-num6">Upload the rest of the files in the folder using your FTP client</li>
			<li class="inpa-fin">Done! Visit <strong><em>yoursite.com</em></strong> and start using Bevo! You do not need to finish the installer process. Delete <code>install.php</code> from your server.</li>
		</ul>		
		<div class="inpa-boxbutt"></div>
	</div>
	<div class="clear"></div>
<br />

<h3>Selfhost Server Requirements</h3>
<p>Because of library requirements and database permissions, <b>BevoMedia won't run on most 'shared' hosts</b>. While some shared hosts may provide all the needed extensions, we recommend a VPS or a dedicated server for your BevoMedia Selfhost installation.</p>

<p><b>Trouble installing? Can't find a host?</b> BevoMedia recommends Rackspace Cloud for simple, secure, reliable hosting, starting as low as $10/mo. <a href="RackspaceWizard.html">Click here for our one-click wizard for launching, configuring and installing Bevo Selfhost on a Rackspace Cloud server.</a></p>
	
<ul>
	<li>BevoMedia was developed and tested on Ubuntu Linux. Most Linux servers should be supported, but we haven't tried them all. We haven't tested Windows servers at all, but theoretically it "should" work.</li>
	<li>BevoMedia requires Apache webserver, because we use a .htaccess file to rewrite URLs. Most hosts use Apache, but if you want to use an alternative webserver like nginx, it's definitely possible -- we run nginx on about half of our servers, with only slight modification. Ask us for help!</li>
	<li>BevoMedia requires at least PHP 5.2 and MySQL 5.1, and PHP short-open tags must be enabled</li>
	<li>Your MySQL user must have <b>CREATE VIEW</b> permissions for the target database. If you're unsure, just try giving the user "All" permissions to the database.</li>
	<li>BevoMedia requires the following PHP extensions to be installed
		<ul>
			<li>Zip</li>
			<li>cURL</li>
			<li>SOAP</li>
			<li>MySQL</li>
			<li>PDO</li>
			<li>PDO adapter for MySQL</li>
			<li>Zend Framework</li>
		</ul>
	To test if you have these extensions, open a text editor like Notepad and enter the following:
	<input class="formtxt wide_half" type="text" value="&lt;?php phpinfo(); ?&gt;" />
	Save it as '<b>info.php</b>' and upload it to your server. Open <b>http://mysite.com/info.php</b> and search through the file for the above extensions to verify that they are all installed.
	<br />Alternatively, our automated installer will check for the presence of dependencies and alert you to any that you are missing.
</li>
</ul>
</div>

<?php /* ##################################################### OUTPUT ############### */ ?>

	<?php echo SoapPageMenu('tut','selfhosted'); 
	echo $this->PageDesc->ShowDesc($this->PageHelper); ?>

<div class="pagecontent">
	<h3 id="createviewserror">I am getting a "CREATE VIEWS" error</h3>
	<p>This is usually caused by a bug in cPanel/WHM. When you create a database user in cPanel/WHM, even if you select "Grant all privileges", not all privileges are granted. You must have 'root' access to WHM, or ask your host to do the following: Log into WHM, and open PHPMyAdmin as root database user. Click the "Users" tab on the top of the screen, then click the edit icon next to your desired Bevo MySQL username. Check all the boxes for permissions, then click Save. To finish, follow the instructions below, under <em>How do I reinstall Bevo? Installer says "Bevo is Already Installed"?</em>.</p>
		
	<h3 id="internalerror">I am getting a 500 Internal error</h3>
	<p>This 500 error is a common server problem in your .htaccess file. To fix it, just delete the two lines that start with "php_value" from .htaccess, should be lines 2 and 3. If you delete these lines from .htaccess, you must make sure PHP is configured with "Magic quotes = Off" and "short open tags = on"</p>
	
	<h3 id="outofmemoryerror">I am getting a PHP out of memory error message</h3>
	<p>Ask your host to increase the PHP memory limit for your account. Bevo requires at least 16M RAM available to operate normally, and 64MB RAM to install or update the software.</p>
		
	<h3 id="liquidweberror">I'm on Liquid Web and my clicks are not tracking properly</h3>
	<p>LiquidWeb uses an Apache security module called mod_security that prevents Bevo's tracking pixel from firing correctly. To fix this, contact LiquidWeb support and ask them to add <code>http://yoursite.com/track/t.gif</code> to the mod_security whitelist.</p>
	
	<h3 id="reinstallbevo">How do I reinstall Bevo? Installer says "Bevo is Already Installed"?</h3>
	<p>Delete <code>config.ini</code> from your Bevo install directory and re-run <code>install.php</code>.</p>
</div><!--close pagecontent-->

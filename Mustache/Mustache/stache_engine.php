<?php
/*This script initializes the templating software known as Mustache.
I downloaded this software from Github.
This is how i plan on changing sections of content on my pages without actually
reloading the entire page, thus mitigating the traffic that ill be sending via this app.
*/
require 'Autoloader.php';
Mustache_Autoloader::register();
$stache = new Mustache_Engine(
	array('loader' => new Mustache_Loader_FilesystemLoader(dirname(__FILE__).'/views'),
		  'partials_loader' => new Mustache_Loader_FilesystemLoader(dirname(__FILE__) . '/views/partials')	
		 ));
?>
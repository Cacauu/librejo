<?php
require_once __DIR__.'/../vendor/autoload.php';

use Librejo\App\App;
use Librejo\Client;
use Librejo\Entity\Entity;

$client = new Client\Client;

if (isset($_GET['entity'])) {
	$entity = $_GET['entity'];
}
else {
	$entity = 'https://tent.tent.is';
}

$meta = $client->discover($entity);
echo "Meta: ";
var_export($meta);
echo "<hr />";
var_export($meta['post']['content']);
echo "<hr />";
var_export($meta['post']['content']['servers'][0]['urls']);//'content->servers);
echo "<hr />Oauth Endpoint: ";
var_export($client->oauth_endpoint());
echo "<hr />Post Feed Endpoint: ";
var_export($client->post_feed_endpoint());
echo "<hr />New Post Endpoint: ";
var_export($client->new_post_endpoint());
echo "<hr/> Profile: ";
var_export($client->profile());
echo "<hr />";
echo "<hr />";

$app = new App(
	'Librejo Client', 
	'http://cacauu.de/librejo/client', 
	array(
		'read' => '',
		'write' => 'https://tent.io/types/status/v0'),
	'http://cacauu.de/librejo/client/redirect/'
	);
var_export($app->register());
?>
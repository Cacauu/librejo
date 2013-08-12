<?php
session_name('Librejo');
session_start();
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
// Saving the entity as a session variable as it is needed later
$_SESSION['entity'] = $entity;

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

$app = new App;
$create_app = $app->new_app($entity, 
	'Librejo Client', 
	'http://cacauu.de/librejo/client', 
	array(
		'read' => '',
		'write' => 'https://tent.io/types/status/v0'),
	'http://localhost:8888/librejo/examples/redirect.php'
	);
var_export($app->register());
echo "<hr />";

// This is the data that is used for all the other requests after registering the app
// Save that somewhere
// The Librejo Demo App uses sessions to store the data
echo "<p>Client ID: ".$app->client_id()."</p>";
$_SESSION['client_id'] = $app->client_id();
echo "<p>Hawk ID: ".$app->hawk_id()."</p>";
$_SESSION['hawk_id'] = $app->hawk_id();
echo "<p>Hawk Key: ".$app->hawk_key()."</p>";
$_SESSION['hawk_key'] = $app->hawk_key();
echo "<hr />";
echo "<a href='".$client->oauth_endpoint()."?client_id=".$app->client_id()."'>Click here to authenticate ".$app->name()."</a>";
?>
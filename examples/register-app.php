<?php
require_once __DIR__.'/../vendor/autoload.php';

use Librejo\App;
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
echo $meta;
?>
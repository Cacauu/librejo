<?php
namespace Librejo\Client\GuzzleClient;

use Guzzle\Http\Client;

class Guzzle {
	public function __construct($entityUri) {
		$GuzzleClient = new Guzzle\Http\Client($entityUri);
		return $GuzzleClient;
	}
}
?>
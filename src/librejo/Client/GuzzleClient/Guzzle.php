<?php
namespace Librejo\Client\GuzzleClient;

use Guzzle\Http\Client;

class Guzzle {
	protected $Guzzle;

	// Creating a new Guzzle client
	// Called with new GuzzleClient\Guzzle($entityUri)
	public function __construct($entityUri) {
		$this->Guzzle = new \Guzzle\Http\Client($entityUri);
	}

	// Returns the meta post of an entity
	// Called with $Guzzle->discover();
	public function discover() {
		$client = $this->Guzzle;
		$header = $client->head()->send();
		$header = $header->getHeader('link')->raw();
		$link = $header[0];
		$link = str_replace('<', '', $link);
		$link = str_replace('>; rel="https://tent.io/rels/meta-post"', "", $link);
		$meta = $client->get($link)->send();
		$meta = $meta->json();
		return $meta;
	}
}
?>
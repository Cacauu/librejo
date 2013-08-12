<?php
namespace Librejo\Client\GuzzleClient;

use Guzzle\Http\Client;

class Guzzle {
	protected $Guzzle;
	protected $entityUri;

	// Creating a new Guzzle client
	// Called with new GuzzleClient\Guzzle($entityUri)
	public function __construct($entityUri) {
		$this->Guzzle = new \Guzzle\Http\Client($entityUri);
		$this->entityUri = $entityUri;
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

	public function post_app($app, $post_endpoint) {
		$client = $this->Guzzle;
		$request = $client->post($post_endpoint, array('Content-Type' => 'application/vnd.tent.post.v0+json; type="https://tent.io/types/app/v0#"'), $app);
		$response = $request->send();
		$link = $response->getHeader('link')->raw();
		$link = str_replace('<', '', $link[0]);
		$link = str_replace('>; rel="https://tent.io/rels/credentials"', "", $link);
		$get = $client->get($link)->send();
		$response = array('App' => $response->json(), 'Credentials' => $get->json());
		return $response;
	}
}
?>
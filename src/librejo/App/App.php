<?php
namespace Librejo\App;

use Librejo\Client;
use Librejo\Client\GuzzleClient;

class App {
	protected $entityUri;
	protected $name;
	protected $url;
	protected $types;
	protected $redirect_uri;

	public function __construct($entityUri, $name, $url, array $types, $redirect_uri) {
		$this->entityUri = $entityUri;
		$this->name = $name;
		$this->url = $url;
		$this->types = $types;
		$this->redirect_uri = $redirect_uri;
	}

	public function register() {
		// Creating the app information JSON
		$app_array = array(
			'type' => 'https://tent.io/types/app/v0#',
			'content' => array(
				'name' => $this->name,
				'url' => $this->url,
				'types' => array(
					'write' => array('https://tent.io/types/status/v0')
				),
				'redirect_uri' => $this->redirect_uri,
				),
			'permissions' => array(
				'public' => false
			),
		);
		$app = json_encode($app_array, JSON_UNESCAPED_SLASHES); // JSON-encoding the app post
		$client = new Client\Client;
		$meta = $client->discover($this->entityUri);
		$register = new GuzzleClient\Guzzle($this->entityUri);
		$post_app = $register->post_app($app, $client->new_post_endpoint());
		//$link = preg_replace('/.*\<(.*)\>.*/', "$1", $post_app);
		return $post_app;
	}
}
?>
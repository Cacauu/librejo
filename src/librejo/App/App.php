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
	protected $client_id;
	protected $hawk_id;
	protected $hawk_key;
	protected $meta;
	protected $Guzzle;
	protected $client;

	public function __construct($entityUri) {
		$this->Guzzle = new GuzzleClient\Guzzle($entityUri);
		$this->entityUri = $entityUri;
		$client = new Client\Client;
		$this->client = $client;
		$meta = $client->discover($entityUri);
		$this->meta = $meta;
	}

	public function new_app($entityUri, $name, $url, array $types, $redirect_uri) {
		$this->entityUri = $entityUri;
		$this->name = $name;
		$this->url = $url;
		$this->types = $types;
		$this->redirect_uri = $redirect_uri;
	}

	// Returns the name of the registered app
	// Called with $app->name()
	public function name() {
		return $this->name;
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
				'scopes' => array('permissions'),
				'redirect_uri' => $this->redirect_uri,
				),
			'permissions' => array(
				'public' => false
			),
		);
		$app = json_encode($app_array, JSON_UNESCAPED_SLASHES); // JSON-encoding the app post
		$meta = $this->meta;
		$client = $this->client;
		$register = new GuzzleClient\Guzzle($this->entityUri);
		$post_app = $register->register_app($app, $client->new_post_endpoint());
		$this->client_id = $post_app['App']['post']['id'];
		$this->hawk_id = $post_app['Credentials']['post']['id'];
		$this->hawk_key = $post_app['Credentials']['post']['content']['hawk_key'];
		return $post_app;
	}

	// Returns the Client ID of the registered app
	// Called with $app->client_id()
	public function client_id() {
		return $this->client_id;
	}

	public function hawk_id() {
		return $this->hawk_id;
	}

	public function hawk_key() {
		return $this->hawk_key;
	}

	public function oauth($code, $app_id, $hawk_id, $hawk_key, $entity) {
		$meta = $this->meta;
		$Guzzle = new GuzzleClient\Guzzle($this->entityUri);
		$oauth = $Guzzle->oauth($code, $app_id, $hawk_id, $hawk_key, $entity, $this->meta['post']['content']['servers'][0]['urls']['oauth_token']);
		return $oauth;
	}

	public function send_post($credentials, $post) {
		$post = $this->Guzzle->send_post($credentials, $post, $this->meta['post']['content']['servers'][0]['urls']['new_post']);
		return $post;	
	}

	public function get_posts($credentials, $type) {
		$posts = $this->Guzzle->get_posts($credentials, $type, $this->meta['post']['content']['servers'][0]['urls']['posts_feed']);
		return $posts;
	}

	public function get_single_post($credentials, $id, $entity) {
		$posts = $this->Guzzle->get_single_post($credentials, $id, $entity, $this->meta['post']['content']['servers'][0]['urls']['post']);
		return $posts;
	}
}
?>
<?php
namespace Librejo\App;

use Librejo\Client;
use Librejo\Client\GuzzleClient;

class App {
	protected $entityUri;
	protected $name;
	protected $url;
	protected $redirect_uri;
	protected $client_id;
	protected $hawk_id;
	protected $hawk_key;
	protected $meta;
	protected $Guzzle;
	protected $client;
	protected $app_post;

	public function __construct($entityUri, $credentials) {
		$this->Guzzle = new GuzzleClient\Guzzle($entityUri, $credentials);
		$this->entityUri = $entityUri;
		$client = new Client\Client;
		$this->client = $client;
		$meta = $client->discover($entityUri);
		$this->meta = $meta;
	}

	public function new_app($entityUri, $post) {
		$this->app_post = $post;
		$app_array = json_decode($post, true);
		$this->entityUri = $entityUri;
		$this->name = $app_array['content']['name'];
		$this->url = $app_array['content']['url'];
		$this->redirect_uri = $app_array['content']['redirect_uri'];
	}

	// Returns the name of the registered app
	// Called with $app->name()
	public function name() {
		return $this->name;
	}

	public function register() {
		// Creating the app information JSON
		$app_json = $this->app_post;
		$meta = $this->meta;
		$client = $this->client;
		$register = $this->Guzzle;
		$post_app = $register->register_app($app_json, $client->new_post_endpoint());
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

	public function oauth($code) {
		$oauth = $this->Guzzle->oauth($code);
		return $oauth;
	}

	public function send_post($post) {
		$post = $this->Guzzle->send_post($post);
		return $post;	
	}

	public function get_posts($type) {
		$posts = $this->Guzzle->get_posts($type);
		return $posts;
	}

	public function get_single_post($id, $entity) {
		$post = $this->Guzzle->get_single_post($id, $entity);
		return $post;
	}

	public function delete_post($id) {
		$delete = $this->Guzzle->delete_post($id, $_SESSION['entity']);
		return $delete;
	}

	public function get_profile($entity) {
		$profile = $this->Guzzle->get_profile($entity);
		return $profile;
	}

	public function update_post($id, $entity, $new_post) {
		$updated = $this->Guzzle->update_post($id, $entity, $new_post);
	}

	public function generate_state($string) {
		$state = uniqid($string, true);
		return $state;
	}
}
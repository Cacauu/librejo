<?php

namespace Librejo\Client;

use Librejo\Client\GuzzleClient;

class Client {

	protected $meta;
	protected $endpoints;
	protected $new_post_endpoint;
	protected $post_feed_endpoint;
	protected $oauth_endpoint;
	protected $profile;

	//Function returning the meta post of an entity
	public function discover($entityUri) {
		$Guzzle = new GuzzleClient\Guzzle($entityUri, array());
		$meta = $Guzzle->discover();
		$this->meta = $meta;
		$this->oauth_endpoint = $meta['post']['content']['servers'][0]['urls']['oauth_auth'];
		$this->new_post_endpoint = $meta['post']['content']['servers'][0]['urls']['new_post'];
		$this->profile = $meta['post']['content']['profile'];
		return $meta;
	}

	public function meta() {
		return $this->meta;
	}

	public function profile() {
		return $this->profile;
	}

	public function oauth_endpoint() {
		return $this->oauth_endpoint;
	}

	public function new_post_endpoint() {
		return $this->new_post_endpoint;
	}
}
<?php
namespace Librejo\App;

class App {
	protected $name;
	protected $url;
	protected $types;
	protected $redirect_uri;

	public function __construct($name, $url, array $types, $redirect_uri) {
		$this->name = $name;
		$this->url = $url;
		$this->types = $types;
		$this->redirect_uri = $redirect_uri;
	}

	public function register() {
		//Building the app post
		$app_array = array(
			'type' => 'https://tent.io/types/app/v0#',
			'content' => array(
				'name' => $this->name,
				'url' => $this->url,
				'types' => $this->types,
				'redirect_uri' => $this->redirect_uri,
				),
			'permissions' => array(
				'public' => false
			),
		);
		$app = json_encode($app_array);
		return $app;
	}
}
?>
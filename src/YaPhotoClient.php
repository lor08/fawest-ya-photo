<?php

namespace Lor08\YaPhoto;

use Lor08\YaPhoto\Api\Album;
use Lor08\YaPhoto\Api\Photo;
use Lor08\YaPhoto\Api\ServiceDocument;

class YaPhotoClient
{

	protected $login;
	protected $accessToken;
	protected $transport;
	protected $serviceDocument;

	public function __construct(array $config)
	{
		$this->login = $config['login'];
		$this->accessToken = $config['token'];
		$this->transport = new Transport();
		$this->serviceDocument = new ServiceDocument($this->transport, $this->login);
		$this->loadServiceDocument();
		try {
			$this->oauth($this->accessToken);
		} catch(\Lor08\YaPhoto\Exception\ServerError $ex) {
			// Яндекс ответил 502. Повторите попытку снова. Как правило, раза с 5 удается получить токен
		} catch(\Lor08\YaPhoto\Exception\Api\Auth $ex) {
			// Что-то с самой авторизацией (см. $ex->getMessage())
		}
	}

	public function loadServiceDocument()
	{
		$this->serviceDocument->load();
		return $this;
	}

	public function oauth($token)
	{
		$this->transport->setOAuthToken($token);
		return $this;
	}

	public function getServiceDocument()
	{
		return $this->serviceDocument;
	}

	public function getTransport()
	{
		return $this->transport;
	}

	public function getAlbum($id)
	{
		$apiUrl = sprintf("http://api-fotki.yandex.ru/api/users/%s/album/%s/?format=json", $this->login, trim($id));
		$album = new Album($this->transport, $apiUrl);
		return $album->load();
	}

	public function sendPhoto($data, $albumId)
	{
		if($albumId) {
			$apiUrl = sprintf("http://api-fotki.yandex.ru/api/users/%s/album/%s/photos/?format=json", $this->login, trim($albumId));
		} else {
			$apiUrl = sprintf("http://api-fotki.yandex.ru/api/users/%s/photos/?format=json", $this->login);
		}
		$photo = new Photo($this->transport, $apiUrl);
		return $photo->upload($data);
	}
}
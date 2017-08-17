<?php
namespace Fawest\YaPhoto;

use Fawest\YaPhoto\Api\Album;
use Fawest\YaPhoto\Api\AlbumsCollection;
use Fawest\YaPhoto\Api\Photo;
use Fawest\YaPhoto\Api\PhotosCollection;
use Fawest\YaPhoto\Api\ServiceDocument;
use Fawest\YaPhoto\Api\Tag;
use Fawest\YaPhoto\Api\TagsCollection;
use Fawest\YaPhoto\Transport;

class Api
{
    protected $_transport;
    protected $_serviceDocument;
    protected $_login;

    public function __construct($login, $token = false)
    {
        $this->_login = (string)$login;
        $this->_transport = new Transport();
        $this->_serviceDocument = new ServiceDocument($this->_transport, $this->_login);
        if($token)
        	$this->oauth($token);
    }

    /**
     * Авторизацию по oauth-токену
     * @param string $token OAuth токен
     * @return self
     */
    public function oauth($token)
    {
        $this->_transport->setOAuthToken($token);
        return $this;
    }

    /**
     * Загрузка сервисного документа
     * @return self
     */
    public function loadServiceDocument()
    {
        $this->_serviceDocument->load();
        return $this;
    }

    /**
     * @return ServiceDocument
	 */
    public function getServiceDocument()
    {
        return $this->_serviceDocument;
    }

    public function getTransport()
    {
        return $this->_transport;
    }

    /**
     * @return AlbumsCollection
	 */
    public function getAlbumsCollection()
    {
        $apiUrl = $this->_serviceDocument->getUrlAlbumsCollection();
        $albumsCollection = new AlbumsCollection($this->_transport, $apiUrl);
        return $albumsCollection;
    }

    /**
     * @return PhotosCollection
	 */
    public function getPhotosCollection()
    {
        $apiUrl = $this->_serviceDocument->getUrlPhotosCollection();
        $photosCollection = new PhotosCollection($this->_transport, $apiUrl);
        return $photosCollection;
    }

    /**
     * @return TagsCollection
	 */
    public function getTagsCollection()
    {
        $apiUrl = $this->_serviceDocument->getUrlTagsCollection();
        $tagsCollection = new TagsCollection($this->_transport, $apiUrl);
        return $tagsCollection;
    }

    /**
     * @param string $title
     * @return Tag
	 */
    public function getTag($title)
    {
        $apiUrl = sprintf("http://api-fotki.yandex.ru/api/users/%s/tag/%s/?format=json", $this->_login, trim($title));
        $tag = new Tag($this->_transport, $apiUrl);
        return $tag;
    }

    /**
     * @param string|int $id
     * @return Album
	 */
    public function getAlbum($id)
    {
        $apiUrl = sprintf("http://api-fotki.yandex.ru/api/users/%s/album/%s/?format=json", $this->_login, trim($id));
        $album = new Album($this->_transport, $apiUrl);
        return $album;
    }

    /**
     * @param $data
     * @param $albumId
     *
     * @return $this
     * @throws Photo
     */
    public function sendPhoto($data, $albumId)
    {
        if($albumId) {
            $apiUrl = sprintf("http://api-fotki.yandex.ru/api/users/%s/album/%s/photos/?format=json", $this->_login, trim($albumId));
        } else {
            $apiUrl = sprintf("http://api-fotki.yandex.ru/api/users/%s/photos/?format=json", $this->_login);
        }
        $photo = new Photo($this->_transport, $apiUrl);
		/** @var TYPE_NAME $photo */
		return $photo->upload($data);
    }
}
<?php
namespace Fawest\YaPhoto\Api;

use Fawest\YaPhoto\ApiAbstract;
use Fawest\YaPhoto\Transport;

class ServiceDocument extends ApiAbstract
{
    protected $_login;
    protected $_apiUrl;
    protected $_urlPhotosCollection;
    protected $_urlAlbumsCollection;
    protected $_urlTagsCollection;

    public function __construct(Transport $transport, $login)
    {
        $this->_login = $login;
        $this->_apiUrl = sprintf('http://api-fotki.yandex.ru/api/users/%s/', $login);
        $this->_transport = $transport;
    }

    public function load()
    {
        try {
            $data = $this->_getData($this->_transport, $this->_apiUrl);
        } catch (\Fawest\YaPhoto\Exception\Api $ex) {
            throw new \Fawest\YaPhoto\Exception\Api\ServiceDocument($ex->getMessage(), $ex->getCode(), $ex);
        }
        $this->_urlPhotosCollection = $data['collections']['photo-list']['href'];
        $this->_urlAlbumsCollection = $data['collections']['album-list']['href'];
        $this->_urlTagsCollection = $data['collections']['tag-list']['href'];
        return $this;
    }

    /**
     * @return string
     */
    public function getUrlAlbumsCollection()
    {
        $result = $this->_urlAlbumsCollection;
        if (empty($result)) {
            $result = sprintf("http://api-fotki.yandex.ru/api/users/%s/albums/", $this->_login);
        }
        return $result;
    }

    /**
     * @return string
     */
    public function getUrlPhotosCollection()
    {
        $result = $this->_urlPhotosCollection;
        if (empty($result)) {
            $result = sprintf("http://api-fotki.yandex.ru/api/users/%s/photos/", $this->_login);
        }
        return $result;
    }

    /**
     * @return string
     */
    public function getUrlTagsCollection()
    {
        $result = $this->_urlTagsCollection;
        if (empty($result)) {
            $result = sprintf("http://api-fotki.yandex.ru/api/users/%s/tags/", $this->_login);
        }
        return $result;
    }
}
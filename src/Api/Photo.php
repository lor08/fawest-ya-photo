<?php
namespace Fawest\YaPhoto\Api;

use Fawest\YaPhoto\ApiAbstract;
use Fawest\YaPhoto\Transport;

class Photo extends ApiAbstract
{
    const SIZE_XXXS = 'XXXS';
    const SIZE_XXS = 'XXS';
    const SIZE_XS = 'XS';
    const SIZE_S = 'S';
    const SIZE_M = 'M';
    const SIZE_L = 'L';
    const SIZE_XL = 'XL';
    const SIZE_XXL = 'XXL';
	const SIZE_XXXL = 'XXXL';
    const SIZE_ORIGINAL = 'orig';

    protected $_albumId;
    protected $_apiUrl;
    protected $_atomId;
    protected $_title;
    protected $_author;
    protected $_apiUrlEdit;
    protected $_url;
    protected $_apiUrlEditMedia;
    protected $_apiUrlAlbum;
    protected $_dateEdited;
    protected $_dateUpdated;
    protected $_datePublished;
    protected $_dateCreated;
    protected $_access;
    protected $_isAdult;
    protected $_isHideOriginal;
    protected $_isDisableComments;
    protected $_img = array();
    protected $_geo;
    protected $_address;
    protected $_content;
    protected $_tags;

    public function __construct(Transport $transport, $apiUrl = null)
    {
        $this->_transport = $transport;
        $this->_apiUrl = $apiUrl;
    }

    public function __destruct()
    {
        foreach ($this as &$property) {
            $property = null;
        }
    }

    public function getAccess()
    {
        return $this->_access;
    }

    public function setAccess($access)
    {
        $this->_access = (string)$access;
        return $this;
    }

    public function getAddress()
    {
        return $this->_address;
    }

    public function getApiUrlAlbum()
    {
        return $this->_apiUrlAlbum;
    }

    public function setApiUrlAlbum($apiUrlAlbum)
    {
        $this->_apiUrlAlbum = (string)$apiUrlAlbum;
    }

    public function getAuthor()
    {
        return $this->_author;
    }

    public function getTags()
    {
        return $this->_tags;
    }

    public function setTags($tags)
    {
        if(is_array($tags) || $tags instanceof \ArrayAccess){
            $this->_tags = implode(', ', $tags);
        }else{
            $this->_tags = (string)$tags;
        }
        return $this;
    }

    public function getContent()
    {
        return $this->_content;
    }

    public function getDateCreated($format = null)
    {
        $result = null;
        if (!empty($this->_dateCreated)) {
            $result = strtotime($this->_dateCreated);
            if (!empty($format)) {
                $result = date($format, $result);
            }
        }
        return $result;
    }

    public function getDateEdited($format = null)
    {
        $result = null;
        if (!empty($this->_dateEdited)) {
            $result = strtotime($this->_dateEdited);
            if (!empty($format)) {
                $result = date($format, $result);
            }
        }
        return $result;
    }

    public function getDatePublished($format = null)
    {
        $result = null;
        if (!empty($this->_datePublished)) {
            $result = strtotime($this->_datePublished);
            if (!empty($format)) {
                $result = date($format, $result);
            }
        }
        return $result;
    }

    public function getDateUpdated($format = null)
    {
        $result = null;
        if (!empty($this->_dateUpdated)) {
            $result = strtotime($this->_dateUpdated);
            if (!empty($format)) {
                $result = date($format, $result);
            }
        }
        return $result;
    }

    public function getGeo()
    {
        $result = null;
        if (!empty($this->_geo)) {
            $result = explode(' ', $this->_geo);
        }
        return $result;
    }

    public function getId()
    {
        $result = substr($this->_atomId, strrpos($this->_atomId, ':') + 1);
        return $result;
    }

    public function getAtomId()
    {
        return $this->_atomId;
    }

    public function isAdult()
    {
        return $this->_isAdult;
    }

    public function setIsAdult($isAdult)
    {
        $this->_isAdult = (bool)$isAdult;
        return $this;
    }

    public function isDisableComments()
    {
        return $this->_isDisableComments;
    }

    public function setIsDisableComments($isDisableComments)
    {
        $this->_isDisableComments = (bool)$isDisableComments;
        return $this;
    }

    public function isHideOriginal()
    {
        return $this->_isHideOriginal;
    }

    public function setIsHideOriginal($isHideOriginal)
    {
        $this->_isHideOriginal = (bool)$isHideOriginal;
        return $this;
    }

    public function getTitle()
    {
        return $this->_title;
    }

    public function setTitle($title)
    {
        $this->_title = (string)$title;
        return $this;
    }

    public function getUrl()
    {
        return $this->_url;
    }

    public function getAlbumId()
    {
        $result = $this->_albumId;
        $pos = strrpos($this->_albumId, ':');
        if ($pos !== false) {
            $result = substr($this->_albumId, strrpos($this->_albumId, ':') + 1);
        }
        return $result;
    }

    public function setAlbumId($albumId)
    {
        $this->_albumId = (string)$albumId;
        return $this;
    }

    public function getAlbumAtomId()
    {
        $result = null;
        $pos = strrpos($this->_albumId, ':');
        if ($pos !== false) {
            $result = $this->_albumId;
        }
        return $result;
    }

    public function getImg($nick = null)
    {
        $nick = is_null($nick) ? self::SIZE_ORIGINAL : $nick;
        $result = null;
        if (isset($this->_img[$nick])) {
            $result = $this->_img[$nick];
        }
        return $result;
    }

    public function getImgHref($nick = null)
    {
        $result = null;
        $data = $this->getImg($nick);
        if (!empty($data)) {
            $result = $data['href'];
        }
        return $result;
    }

    public function getImgWidth($nick = null)
    {
        $result = null;
        $data = $this->getImg($nick);
        if (!empty($data)) {
            $result = $data['width'];
        }
        return $result;
    }

    public function getImgHeight($nick = null)
    {
        $result = null;
        $data = $this->getImg($nick);
        if (!empty($data)) {
            $result = $data['height'];
        }
        return $result;
    }

    public function getImgSize($nick = null)
    {
        $result = null;
        $data = $this->getImg($nick);
        if (!empty($data)) {
            $result = $data['bytesize'];
        }
        return $result;
    }

    public function load()
    {
        try {
            $data = $this->_getData($this->_transport, $this->_apiUrl);
        } catch (\Fawest\YaPhoto\Exception\Api $ex) {
            throw new \Fawest\YaPhoto\Exception\Api\Photo($ex->getMessage(), $ex->getCode(), $ex);
        }
        $this->initWithData($data);
        return $this;
    }

    public function upload($data)
    {
        return $this->_postData($this->_transport, $this->_apiUrl, $data);
    }

    public function update()
    {
        // @todo:
    }

    public function delete()
    {
        try {
            $this->_deleteData($this->_transport, $this->_apiUrl);
        } catch (\Fawest\YaPhoto\Exception\Api $ex) {
            throw new \Fawest\YaPhoto\Exception\Api\Photo($ex->getMessage(), $ex->getCode(), $ex);
        }
        $this->__destruct();
        return true;
    }

    public function initWithData(array $entry)
    {
        if (isset($entry['id'])) {
            $this->_atomId = (string)$entry['id'];
        }
        if (isset($entry['links']['album'])) {
            if (preg_match('/\/(\d+)\//', $entry['links']['album'], $matches)) {
                $this->_albumId = $matches[1];
            }
        }
        if (isset($entry['authors'][0]['name'])) {
            $this->_author = (string)$entry['authors'][0]['name'];
        }
        if (isset($entry['author'])) {
            $this->_author = (string)$entry['author'];
        }
        if (isset($entry['title'])) {
            $this->setTitle($entry['title']);
        }
        if (isset($entry['published'])) {
            $this->_datePublished = (string)$entry['published'];
        }
        if (isset($entry['updated'])) {
            $this->_dateUpdated = (string)$entry['updated'];
        }
        if (isset($entry['edited'])) {
            $this->_dateEdited = (string)$entry['edited'];
        }
        if (isset($entry['links']['self'])) {
            $this->_apiUrl = (string)$entry['links']['self'];
        }
        if (isset($entry['links']['alternate'])) {
            $this->_url = (string)$entry['links']['alternate'];
        }
        if (isset($entry['links']['album'])) {
            $this->setApiUrlAlbum($entry['links']['album']);
        }
        if (isset($entry['links']['edit'])) {
            $this->_apiUrlEdit = (string)$entry['links']['edit'];
        }
        if (isset($entry['links']['editMedia'])) {
            $this->_apiUrlEditMedia = (string)$entry['links']['editMedia'];
        }
        if (isset($entry['access'])) {
            $this->setAccess($entry['access']);
        }
        if (isset($entry['xxx'])) {
            $this->setIsAdult($entry['xxx']);
        }
        if (isset($entry['disableComments'])) {
            $this->setIsDisableComments($entry['disableComments']);
        }
        if (isset($entry['hideOriginal'])) {
            $this->setIsHideOriginal($entry['hideOriginal']);
        }
        if (isset($entry['tags'])) {
            $this->setTags($entry['tags']);
        }
        if (isset($entry['img'])) {
            $this->_img = $entry['img'];
        }
        // TODO: определить как получить
//        if (isset($entry['addressBinding']['address'])) {
//            $this->setAddress($entry['addressBinding']['address']);
//        }
        if (isset($entry['geo']['coordinates'])) {
            $this->_geo = $entry['geo']['coordinates'];
        }

        return $this;
    }
}

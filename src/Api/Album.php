<?php
namespace Fawest\YaPhoto\Api;

use Fawest\YaPhoto\Api\CollectionAbstract;

class Album extends CollectionAbstract
{
    protected $_apiUrlEdit;
    protected $_apiUrlPhotos;
    protected $_apiUrlCover;
    protected $_apiUrlYmapsml;
    protected $_apiUrlParent;
    protected $_atomId;
    protected $_parentId;
    protected $_author;
    protected $_title;
    protected $_summary;
    protected $_datePublished;
    protected $_dateEdited;
    protected $_dateUpdated;
    protected $_url;
    protected $_isProtected;
    protected $_imageCount;

    public function getApiUrlParent()
    {
        return $this->_apiUrlParent;
    }

    public function setApiUrlParent($apiUrlParent)
    {
        $this->_apiUrlParent = (string)$apiUrlParent;
        return $this;
    }

    public function getApiUrl()
    {
        return $this->_apiUrl;
    }

    public function getApiUrlCover()
    {
        return $this->_apiUrlCover;
    }

    public function getApiUrlEdit()
    {
        return $this->_apiUrlEdit;
    }

    public function getApiUrlPhotos()
    {
        return $this->_apiUrlPhotos;
    }

    public function getApiUrlYmapsml()
    {
        return $this->_apiUrlYmapsml;
    }

    public function getAuthor()
    {
        return $this->_author;
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

    public function getId()
    {
        $result = substr($this->_atomId, strrpos($this->_atomId, ':') + 1);
        return $result;
    }

    public function getAtomId()
    {
        return $this->_atomId;
    }

    public function getImageCount()
    {
        return $this->_imageCount;
    }

    public function isProtected()
    {
        return $this->_isProtected;
    }

    public function getParentId()
    {
        return $this->_parentId;
    }

    public function getSummary()
    {
        return $this->_summary;
    }

    public function setSummary($summary)
    {
        $this->_summary = (string)$summary;
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

    public function load()
    {
        try {
            $data = $this->_getData($this->_transport, $this->_apiUrl);
        } catch (\Fawest\YaPhoto\Exception\Api $ex) {
            throw new \Fawest\YaPhoto\Exception\Api\Album($ex->getMessage(), $ex->getCode(), $ex);
        }
        $this->initWithData($data);
        $this->_loadPhotos();
        return $this;
    }

    public function initWithData(array $entry)
    {
        if (isset($entry['links']['self'])) {
            $this->_apiUrl = (string)$entry['links']['self'];
        }
        if (isset($entry['id'])) {
            $this->_atomId = (string)$entry['id'];
        }
        if (isset($entry['links']['album'])) {
            if (preg_match('/\/(\d+)\//', $entry['links']['album'], $matches)) {
                $this->_parentId = (string)$matches[1];
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
        if (isset($entry['summary'])) {
            $this->setSummary($entry['summary']);
        }
        if (isset($entry['imageCount'])) {
            $this->_imageCount = (int)$entry['imageCount'];
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
        if (isset($entry['links']['alternate'])) {
            $this->_url = (string)$entry['links']['alternate'];
        }
        if (isset($entry['links']['album'])) {
            $this->setApiUrlParent($entry['links']['album']);
        }
        if (isset($entry['links']['photos'])) {
            $this->_apiUrlPhotos = (string)$entry['links']['photos'];
        }
        if (isset($entry['links']['edit'])) {
            $this->_apiUrlEdit = (string)$entry['links']['edit'];
        }
        if (isset($entry['links']['ymapsml'])) {
            $this->_apiUrlYmapsml = (string)$entry['links']['ymapsml'];
        }
        if (isset($entry['links']['cover'])) {
            $this->_apiUrlCover = (string)$entry['links']['cover'];
        }
        return $this;
    }

    protected function _loadPhotos()
    {
        if (!empty($this->_apiUrlPhotos)) {
            try {
                $this->_loadCollectionData($this->_apiUrlPhotos);
                foreach ($this->_entries as $entry) {
                    $photo = new \Fawest\YaPhoto\Api\Photo($this->_transport);
                    $photo->initWithData($entry)
                        ->setApiUrlAlbum($this->_apiUrl);
                    $this->_data[$photo->getId()] = $photo;
                }
            } catch (\Fawest\YaPhoto\Exception\Api $ex) {
                throw new \Fawest\YaPhoto\Exception\Api\PhotosCollection($ex->getMessage(), $ex->getCode(), $ex);
            }
        }
        return $this;
    }
}

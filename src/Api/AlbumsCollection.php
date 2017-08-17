<?php
namespace Fawest\YaPhoto\Api;

use Fawest\YaPhoto\Api\Album;
use Fawest\YaPhoto\Api\CollectionAbstract;

class AlbumsCollection extends CollectionAbstract
{
    /**
     * @return self
     * @throws \Fawest\YaPhoto\Exception\Api\AlbumsCollection
     */
    public function load()
    {
        try {
            $this->_loadCollectionData($this->_apiUrl);
            foreach ($this->_entries as $entry) {
                $album = new Album($this->_transport, $entry['links']['self']);
                $album->initWithData($entry);
                $this->_data[$album->getId()] = $album;
            }
        } catch (\Fawest\YaPhoto\Exception\Api $ex) {
            throw new \Fawest\YaPhoto\Exception\Api\AlbumsCollection($ex->getMessage(), $ex->getCode(), $ex);
        }
        return $this;
    }
}
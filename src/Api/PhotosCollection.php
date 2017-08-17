<?php
namespace Fawest\YaPhoto\Api;

class PhotosCollection extends \Fawest\YaPhoto\Api\CollectionAbstract
{
    /**
     * @return self
     * @throws \Fawest\YaPhoto\Exception\Api\PhotosCollection
     */
    public function load()
    {
        try {
            $this->_loadCollectionData($this->_apiUrl);
            foreach ($this->_entries as $entry) {
                $photo = new \Fawest\YaPhoto\Api\Photo($this->_transport, $entry['links']['self']);
                $photo->initWithData($entry);
                $this->_data[$photo->getId()] = $photo;
            }
        } catch (\Fawest\YaPhoto\Exception\Api $ex) {
            throw new \Fawest\YaPhoto\Exception\Api\PhotosCollection($ex->getMessage(), $ex->getCode(), $ex);
        }
        return $this;
    }
}
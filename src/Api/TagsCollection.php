<?php
namespace Fawest\YaPhoto\Api;

use Fawest\YaPhoto\Api\CollectionAbstract;
use Fawest\YaPhoto\Api\Tag;

class TagsCollection extends CollectionAbstract
{
    /**
     * @return self
     * @throws \Fawest\YaPhoto\Exception\Api\TagsCollection
     */
    public function load()
    {
        try {
            $this->_loadCollectionData($this->_apiUrl);
            foreach ($this->_entries as $entry) {
                $tag = new Tag($this->_transport, $entry['links']['self']);
                $tag->initWithData($entry);
                $this->_data[$tag->getId()] = $tag;
            }
        } catch (\Fawest\YaPhoto\Exception\Api $ex) {
            throw new \Fawest\YaPhoto\Exception\Api\TagsCollection($ex->getMessage(), $ex->getCode(), $ex);
        }
        return $this;
    }
}
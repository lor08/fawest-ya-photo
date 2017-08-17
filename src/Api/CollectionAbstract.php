<?php
namespace Fawest\YaPhoto\Api;

use Fawest\YaPhoto\ApiAbstract;
use Fawest\YaPhoto\Transport;

abstract class CollectionAbstract extends ApiAbstract
{
    const BY_LAST_UPDATE_ASC = 'updated';
    const BY_LAST_UPDATE_DESC = 'rupdated';
    const BY_PUBLISH_DATE_ASC = 'published';
    const BY_PUBLISH_DATE_DESC = 'rpublished';
    const BY_PUBLISH_DATE_EXIF_ASC = 'created';
    const BY_PUBLISH_DATE_EXIF_DESC = 'rcreated';

    protected $_apiUrl;
    protected $_apiUrlNextPage;
    protected $_dateUpdated;
    protected $_order;
    protected $_offset;
    protected $_limit;
    protected $_data = array();
    protected $_entries = array();

    public function __construct(Transport $transport, $apiUrl)
    {
        $this->_apiUrl = $apiUrl;
        $this->_transport = $transport;
    }

    /**
     * Загрузка следующей страницы выдачи
     * @return $this
     * @throws \Fawest\YaPhoto\Exception\Api\StopIteration
     */
    public function loadNext()
    {
        $this->resetFilters();
        if (empty($this->_apiUrlNextPage)) {
            throw new \Fawest\YaPhoto\Exception\Api\StopIteration("Not found next page of collection");
        }
        $this->__construct($this->_transport, $this->_apiUrlNextPage);
        try {
            $this->load();
        } catch (\Fawest\YaPhoto\Exception\Api\AlbumsCollection $ex) {
            throw new \Fawest\YaPhoto\Exception\Api\StopIteration($ex->getMessage(), $ex->getCode(), $ex);
        }
        return $this;
    }

    /**
     * Загрузить всю коллекцию
     * @param null|int $limitQueries Ограничиваем кол-во запросов к api на получение коллекции
     * @return self
     */
    public function loadAll($limitQueries = null)
    {
        $limitQueries = is_null($limitQueries) ? 20 : $limitQueries;
        $albums[] = $this
            ->load();
        for ($i = 0; $i < $limitQueries; $i++) {
            try {
                $this->loadNext();
            } catch (\Fawest\YaPhoto\Exception\Api\StopIteration $ex) {
                break;
            }
        }
        return $this;
    }

    /**
     * @param string|null $format В каком формате возвращать время (null = timestamp)
     * @return int|string
     */
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

    /**
     * @return array
     */
    public function getList()
    {
        $result = $this->_data;
        $limit = (int)$this->_limit;
        if ($limit > 0) {
            $result = array_slice($result, 0, $limit);
        }
        return $result;
    }

    /**
     * @return int
     */
    public function getCount()
    {
        return count($this->_data);
    }

    /**
     * @param int $limit
     * @return self
     */
    public function setLimit($limit)
    {
        $this->_limit = is_null($limit) ? null : (int)$limit;
        return $this;
    }

    /**
     * @param string $order
     * @return self
     */
    public function setOrder($order)
    {
        $this->_order = $order;
        return $this;
    }

//    /**
//     * @param string $offset
//     * @return self
//     */
//    public function setOffset($offset)
//    {
//        $this->_offset = $offset;
//        return $this;
//    }

    /**
     * Сбрасываем фильтры
     * @return self
     */
    public function resetFilters()
    {
        $this->_order = null;
        $this->_limit = null;
        return $this;
    }

    /**
     * Загружаем информацию по коллекции для дальнейшей обработки
     * @param string $apiUrl
     * @throws \Exception
     * @throws \Fawest\YaPhoto\Exception
     */
    protected function _loadCollectionData($apiUrl)
    {
        $this->_apiUrlNextPage = null;
        $this->_dateUpdated = null;
        $this->_entries = array();
        try {
            $data = $this->_getData($this->_transport, $this->_getApiUrlWithParams($apiUrl));
            if (isset($data['links']['next'])) {
                $this->_apiUrlNextPage = (string)$data['links']['next'];
            }
            if (isset($data['updated'])) {
                $this->_dateUpdated = (string)$data['updated'];
            }
            if (isset($data['entries']) && is_array($data['entries'])) {
                $this->_entries = $data['entries'];
            }
        } catch (\Fawest\YaPhoto\Exception $ex) {
            throw $ex;
        }
        $this->resetFilters();
    }

    protected function _getApiUrlWithParams($url)
    {
        $parts = parse_url($url);
        if (!isset($parts['query'])) {
            $parts['query'] = '';
        }
        if (!empty($this->_order)) {
            $parts['path'] .= $this->_order;
            if (!empty($this->_offset)) {
                $parts['path'] .= (';' . $this->_offset);
            }
            $parts['path'] .= '/';
        }
        $limit = (int)$this->_limit;
        if ($limit > 0) {
            if (!empty($parts['query'])) {
                $parts['query'] .= '&';
            }
            $parts['query'] .= 'limit=' . $limit;
        }
        $result = sprintf("%s://%s%s?%s", $parts['scheme'], $parts['host'], $parts['path'], $parts['query']);
        return $result;
    }
}
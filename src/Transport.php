<?php
namespace Fawest\YaPhoto;

use \Fawest\YaPhoto\Exception;

class Transport implements \Serializable
{
    const METHOD_POST = 'POST';
    const METHOD_GET = 'GET';
    const METHOD_PUT = 'PUT';
    const METHOD_DELETE = 'DELETE';

    protected $_oauthToken;

    public function __construct()
    {
    }

    public function __call($method, $arguments)
    {
        $result = null;
        $requestMethod = strtoupper($method);
        if (!in_array($requestMethod, array(self::METHOD_POST, self::METHOD_GET))) {
            throw new Exception(sprintf("Method %s is not supported!", $method));
        } else {
            array_unshift($arguments, $requestMethod);
            $result = call_user_func_array(array($this, 'request'), $arguments);
        }
        return $result;
    }

    /**
     * (PHP 5 >= 5.1.0)<br/>
     * String representation of object
     * @link http://php.net/manual/en/serializable.serialize.php
     * @see \Serializable::serialize()
     * @return string the string representation of the object or null
     */
    public function serialize()
    {
        return serialize(array('token' => $this->_oauthToken));
    }

    /**
     * (PHP 5 >= 5.1.0)<br/>
     * Constructs the object
     * @link http://php.net/manual/en/serializable.unserialize.php
     * @see \Serializable::unserialize()
     * @param string $serialized The string representation of the object.
     * @return void
     */
    public function unserialize($serialized)
    {
        $serialized = unserialize($serialized);
        if (is_array($serialized) && isset($serialized['token'])) {
            $this->_oauthToken = $serialized['token'];
        }
    }

    /**
     * @param string $token OAuth токен
     * @return self
     */
    public function setOAuthToken($token)
    {
        $this->_oauthToken = (string)$token;
        return $this;
    }

    public function request($method, $url, array $params = null)
    {
        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $headers = array();
        $headers[] = 'Accept: application/json';
        switch ($method) {
            case self::METHOD_POST:
                curl_setopt($curl, CURLOPT_POST, 1);

                $imgstr = '';
                $imgmime = '';
                if (isset($params['image'])){
                    $imgstr = $params['image'];
                    $imgSize = getimagesizefromstring($params['image']);
                    $imgmime = $imgSize['mime'];

                    $headers[] = 'Content-Type: ' . $imgSize['mime'];
                    $headers[] = 'Content-Length: ' . strlen($imgstr);

                    curl_setopt($curl, CURLOPT_POSTFIELDS, $imgstr);
                }elseif(isset($params['atomEntry'])){
                    $headers[] = 'Content-Type: application/atom+xml; charset=utf-8; type=entry';
                    curl_setopt($curl, CURLOPT_POSTFIELDS, $params['atomEntry']);
                }else{
                    // $headers[] = 'Content-Type: application/json';
                    if (is_array($params))
                        curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($params));
                }
                break;
            default:
                curl_setopt($curl, CURLOPT_HTTPGET, 1);
                break;
        }
        if (!empty($this->_oauthToken)) {
            $headers[] = 'Authorization: OAuth '. $this->_oauthToken;
        } elseif(!empty($this->_fimpToken)) {
            $headers[] = 'Authorization: FimpToken realm="fotki.yandex.ru", token="' . $this->_fimpToken . '"';
        }
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        $data = curl_exec($curl);
        $code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        if (curl_errno($curl)) {
            curl_close($curl);
            throw new Exception\CurlError(curl_error($curl));
        }
        curl_close($curl);

        $result = array(
            'code' => $code,
            'data' => $data
        );
        return $result;
    }
}
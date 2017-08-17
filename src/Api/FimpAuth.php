<?php
namespace Fawest\YaPhoto\Api;

class FimpAuth
{
    /**
     * @var string
     */
    protected $_token;
    /**
     * @var string
     */
    protected $_login;
    /**
     * @var string
     */
    protected $_credentials;
    /**
     * @var string
     */
    protected $_rsaKey;
    /**
     * @var string
     */
    protected $_requestId;

    /**
     * @param \Fawest\YaPhoto\Transport $transport
     * @param string|null $login
     * @param string|null $password
     * @param null $token
     * @throws \Fawest\YaPhoto\Exception\Api\Auth
     * @return \Fawest\YaPhoto\Api\FimpAuth
     */
    public function __construct(\Fawest\YaPhoto\Transport $transport, $login, $password, $token = null)
    {
        $this->_transport = $transport;
        $this->_login = $login;
        if (!empty($token)) {
            $this->_token = trim($token);
        } elseif (!empty($login) && !empty($password)) {
            $this->_loadRsaKey($transport, 'http://auth.mobile.yandex.ru/yamrsa/key/');
            $credentials = sprintf("<credentials login='%s' password='%s'/>", $login, $password);
            $this->_credentials = \Fawest\YaPhoto\Encrypt::encrypt($this->_rsaKey, $credentials);
            $this->load();
        } else {
            throw new \Fawest\YaPhoto\Exception\Api\Auth("Not specified password or token!");
        }
    }

    /**
     * @return self
     */
    public function load()
    {
        $params = array('request_id' => $this->_requestId,
            'credentials' => $this->_credentials);
        $this->_loadToken($this->_transport, 'http://auth.mobile.yandex.ru/yamrsa/token/', $params);
        return $this;
    }

    /**
     * @return string
     */
    public function getToken()
    {
        return $this->_token;
    }

    /**
     * @return string
     */
    public function getLogin()
    {
        return $this->_login;
    }

    protected function _loadRsaKey(\Fawest\YaPhoto\Transport $transport, $apiUrl)
    {
        $error = true;
        $result = null;
        $tmp = $transport->get($apiUrl);
        if ($tmp['code'] == 200) {
            $result = $tmp['data'];
            $error = false;
        }
        if ($error) {
            $text = strip_tags($tmp['data']);
            $msg = sprintf("Command %s error (%s). %s", get_called_class(), $apiUrl, trim($text));
            if ($tmp['code'] == 502) {
                throw new \Fawest\YaPhoto\Exception\ServerError(sprintf("Error get RSA key! %s", $msg), $tmp['code']);
            } else {
                throw new \Fawest\YaPhoto\Exception\Api\Auth(sprintf("Error get RSA key! %s", $msg), $tmp['code']);
            }
        }
        $response = new \SimpleXMLElement($result);
        $this->_requestId = (string)$response->request_id;
        $this->_rsaKey = (string)$response->key;
        return $this;
    }

    protected function _loadToken(\Fawest\YaPhoto\Transport $transport, $apiUrl, array $data)
    {
        $error = true;
        $result = null;
        $tmp = $transport->post($apiUrl, $data);
        if ($tmp['code'] == 200) {
            $result = $tmp['data'];
            $error = false;
        }
        if ($error) {
            $text = strip_tags($tmp['data']);
            $msg = sprintf("Command %s error (%s). %s", get_called_class(), $apiUrl, trim($text));
            if ($tmp['code'] == 502) {
                throw new \Fawest\YaPhoto\Exception\ServerError(sprintf("Error get token! %s", $msg), $tmp['code']);
            } else {
                throw new \Fawest\YaPhoto\Exception\Api\Auth(sprintf("Error get token! %s", $msg), $tmp['code']);
            }
        }
        $response = new \SimpleXMLElement($result);
        $this->_token = (string)$response->token;
        return $this;
    }
}
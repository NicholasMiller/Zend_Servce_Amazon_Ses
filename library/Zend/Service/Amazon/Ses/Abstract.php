<?php
/**
 * Zend Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://framework.zend.com/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@zend.com so we can send you a copy immediately.
 *
 * @category   Zend
 * @package    Zend_Service
 * @subpackage Amazon
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id:$
 */

/**
 * Amazon SES Abstract Base Class
 *
 * @category   Zend
 * @package    Zend_Service
 * @subpackage Amazon
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
abstract class Zend_Service_Amazon_Ses_Abstract extends Zend_Service_Amazon_Abstract
{
    /**
     * Endpoint url for
     * @var string
     */
    protected $_endpoint = 'email.%s.amazonaws.com';
    
    /**
     * Region
     * @var string
     */
    protected $_region = 'us-east-1';

    /**
     * The action being performed.
     * Derrived from concrete instance
     * @var string
     */
    protected $_action;

    /**
     * Http Timout
     * @var integer
     */
    protected $_httpTimeout = 10;

    /**
     * Class Constructor
     * @param string $accessKey (Optional) AWS Access Key
     * @param string $secretKey (Optional) AWS Secret Key
     */
    public function  __construct($accessKey = null, $secretKey = null)
    {
        parent::__construct($accessKey, $secretKey);
        $this->_action = end(explode('_', get_class($this)));
    }

    /**
     * The method that puts everything in motion
     * @return Zend_Service_Amazon_Ses_Response_Abstract
     */
    abstract public function request();

    /**
     * Adds the required headers to the Http Client
     * @return void
     */
    protected function _addRequiredHeaders(Zend_Http_Client $httpClient)
    {
        $date = gmdate('r');
        $httpClient->setHeaders('Date', $date)
                   ->setHeaders('Content-Type', 'application/x-www-form-urlencoded')
                   ->setHeaders(
                       'X-Amzn-Authorization',
                       'AWS3-HTTPS AWSAccessKeyId=' . $this->_getAccessKey() .
                            ', Algorithm=HmacSHA256, Signature=' . $this->_calculateSignature($date)
                   );
        
    }

    /**
     * Creates a signature from the provided date
     * @param  string $data RFC2616-compliant date
     * @return string
     */
    protected function _calculateSignature($date)
    {
        $hmac = Zend_Crypt_Hmac::compute(
            $this->_getSecretKey(),
            'SHA256', $date,
            Zend_Crypt_Hmac::BINARY
        );
        
        return base64_encode($hmac);
    }

    /**
     * Sends the assembled request to the
     * @param  array $params
     * @throws Zend_Service_Amazon_Ses_Exception if there was a
     *                          miscommunication during the http request, or
     *                          if aws returned an error message
     * @return Zend_Service_Amazon_Ses_Result
     */
    protected function _sendRequest($params)
    {
        $url = sprintf('https://' . $this->_endpoint, $this->_region);
        $params = array_merge($params, array('Action' => $this->_action));

        try {
            /* @var $client Zend_Http_Client */
            $client = self::getHttpClient();
            $client->resetParameters();

            $this->_addRequiredHeaders($client);

            $client->setConfig(array(
                'timeout' => $this->_httpTimeout
            ));

            $client->setUri($url);
            $client->setMethod(Zend_Http_Client::POST);
            $client->setParameterPost($params);

            return Zend_Service_Amazon_Ses_Response::factory(
                $this->_action, $client->request()
            );
        } catch (Zend_Http_Client_Exception $e) {
            $message = 'Error in request to AWS service: ' . $e->getMessage();
            throw new Zend_Service_Amazon_Ses_Exception($message, $e->getCode(), $e);
        }
    }
}
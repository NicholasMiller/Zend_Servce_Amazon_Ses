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
 * @version    $Id: $
 */

/**
 * Amazon SES Send Email Response
 *
 * @category   Zend
 * @package    Zend_Service
 * @subpackage Amazon
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Service_Amazon_Ses_Response_SendEmail extends
        Zend_Service_Amazon_Ses_Response_Abstract
{
    /**
     * @var string
     */
    protected $_requestId;

    /**
     * @var string
     */
    protected $_messageId;


    /**
     * Gets the AWS Request Id
     * @return string
     */
    public function getRequestId()
    {
        return $this->_requestId;
    }

    /**
     * Sets the AWS Request Id
     * @param string $requestId
     * @return Zend_Service_Amazon_Ses_Response_SendEmail
     */
    public function setRequestId($requestId)
    {
        $this->_requestId = $requestId;
        return $this;
    }

    /**
     * Gets the AWS Message Id
     * @return string
     */
    public function getMessageId()
    {
        return $this->_messageId;
    }

    /**
     * Sets the AWS Message Id
     * @param  string $messageId
     * @return Zend_Service_Amazon_Ses_Response_SendEmail
     */
    public function setMessageId($messageId)
    {
        $this->_messageId = $messageId;
        return $this;
    }

    /**
     * Parses the AWS XML response
     * @param  SimpleXMLElement $xml
     * @return Zend_Service_Amazon_Ses_Response_SendEmail
     */
    public function buildFromXml(SimpleXMLElement $xml)
    {
        $this->setMessageId(
            $xml->SendEmailResult->MessageId
        )->setRequestId(
            $xml->ResponseMetadata->RequestId
        );

        return $this;
    }
}
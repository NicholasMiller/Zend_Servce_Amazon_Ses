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
 * @version    $Id: Ec2.php 20096 2010-01-06 02:05:09Z bkarwin $
 */

/**
 * Amazon SES SendRawEmail Operation Class
 *
 * @category   Zend
 * @package    Zend_Service
 * @subpackage Amazon
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Service_Amazon_Ses_SendRawEmail extends Zend_Service_Amazon_Ses_Abstract
{
    /**
     * Raw Email
     * @var string
     */
    protected $_rawEmail;

    /**
     * Gets the raw email message
     * @return string
     */
    public function getRawEmail()
    {
        return $this->_rawEmail;
    }

    /**
     * Sets the raw email.
     * @param  string $rawEmail Raw email string
     * @return Zend_Service_Amazon_Ses_SendRawEmail For a fluid interface
     */
    public function setRawEmail($rawEmail)
    {
        $this->_rawEmail = $rawEmail;
        return $this;
    }

    /**
     * Performs the request
     * @return Zend_Service_Amazon_Ses_Response_SendRawEmail
     */
    public function request()
    {
        $params = array(
            'RawMessage.Data' => base64_encode($this->getRawEmail() . "\n\n")
        );

        return $this->_sendRequest($params);
    }
}
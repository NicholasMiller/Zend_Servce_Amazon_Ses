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
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id: $
 */

/**
 * Amazon SES VerifyEmailAddress Operation Class
 *
 * @category   Zend
 * @package    Zend_Service
 * @subpackage Amazon
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Service_Amazon_Ses_VerifyEmailAddress extends Zend_Service_Amazon_Ses_Abstract
{
    /**
     * Email Address To Be Verified
     * @var string
     */
    protected $_emailAddress;

    /**
     * Gets the raw email message
     * @return string
     */
    public function getEmailAddress()
    {
        return $this->_emailAddress;
    }

    /**
     * Sets the raw email.
     * @param  string $emailAddress Email address to be verified
     * @return Zend_Service_Amazon_Ses_SendRawEmail For a fluid interface
     */
    public function setEmailAddress($emailAddress)
    {
        $this->_emailAddress = $emailAddress;
        return $this;
    }

    /**
     * Performs the request
     * @return Zend_Service_Amazon_Ses_Response_SendRawEmail
     */
    public function request()
    {
        $params = array(
            'EmailAddress' => $this->getEmailAddress()
        );

        return $this->_sendRequest($params);
    }
}
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
 * Amazon SES ListVerifiedEmailAddresses Result
 *
 * @category   Zend
 * @package    Zend_Service
 * @subpackage Amazon
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Service_Amazon_Ses_Response_ListVerifiedEmailAddresses extends
    Zend_Service_Amazon_Ses_Response_Abstract
{

    /**
     * @var array
     */
    protected $_emailAddresses;

    /**
     * Returns an array of email addresses which have been verified with SES
     * @return array
     */
    public function getEmailAddresses()
    {
        return $this->_emailAddresses;
    }

    /**
     * Parses the AWS XML response
     * @param  SimpleXMLElement $xml
     * @return Zend_Service_Amazon_Ses_Response_VerifyEmailAddress
     */
    public function buildFromXml(SimpleXMLElement $xml)
    {
        $xml->registerXPathNamespace('ses', 'http://ses.amazonaws.com/doc/2010-12-01/');
        foreach ($xml->xpath('//ses:member') as $member) {
            $this->_emailAddresses[] = (string)$member;
        }

        $this->setRequestId(
            (string)$xml->ResponseMetadata->RequestId
        );

        return $this;
    }
}
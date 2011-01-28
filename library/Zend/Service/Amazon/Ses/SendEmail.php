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
 * Amazon SES Service Class
 *
 * @category   Zend
 * @package    Zend_Service
 * @subpackage Amazon
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Service_Amazon_Ses_SendEmail extends Zend_Service_Amazon_Ses_Abstract
{
    /**
     * Source (From) email address
     * @var string
     */
    protected $_source;

    /**
     * To Recipients
     * @var array
     */
    protected $_toRecipients = array();

    /**
     * Carbon Copy Recipients
     * @var array
     */
    protected $_ccRecipients = array();

    /**
     * Blind Carbon Copy Recipients
     * @var array
     */
    protected $_bccRecipients = array();

    /**
     * Message Subject
     * @var string
     */
    protected $_subject;

    /**
     * Message Body
     * @var string
     */
    protected $_body;

    /**
     * Gets the Source (AWS Version of From Email Address)
     * @return string
     */
    public function getSource()
    {
        return $this->_source;
    }

    /**
     * Sets the Source (AWS Version of From Email Address)
     * @param string $source
     * @return Zend_Service_Amazon_Ses_SendEmail
     */
    public function setSource($source)
    {
        $this->_source = $source;
        return $this;
    }

    /**
     * Returns all the TO recipients
     * @return array
     */
    public function getTo()
    {
        return $this->_toRecipients;
    }

    /**
     * Add a TO address
     * @param  string $email
     * @param  string $name
     * @return Zend_Service_Amazon_Ses_SendEmail
     */
    public function addTo($email, $name = null)
    {
        $this->_addEmail($email, $name, 'to');
        return $this;
    }

    /**
     * Gets registered CC addresses
     * @return array
     */
    public function getCc()
    {
        return $this->_ccRecipients;
    }

    /**
     * Adds the CC address
     * @param  string $email
     * @param  string $name
     * @return Zend_Service_Amazon_Ses_SendEmail
     */
    public function addCc($email, $name = null)
    {
        $this->_addEmail($email, $name, 'cc');
        return $this;
    }

    /**
     * Gets the BCC email addresses
     * @return array
     */
    public function getBcc()
    {
        return $this->_bccRecipients;
    }

    /**
     * Adds a BCC address
     * 
     * @param  string $email
     * @param  string $name
     * @return Zend_Service_Amazon_Ses_SendEmail
     */
    public function addBcc($email, $name = null)
    {
        $this->_addEmail($email, $name, 'bcc');
        return $this;
    }

    /**
     *
     * @param  string $email
     * @param  string $name
     * @param  string $part
     * @throws Zend_Service_Amazon_Ses_Exception if email is invalid or part is not one of to, cc, bcc
     * @return void
     */
    protected function _addEmail($email, $name, $part)
    {
        $part = strtolower($part);
        if (!in_array($part, array('to', 'cc', 'bcc'))) {
            throw new Zend_Service_Amazon_Ses_Exception(
                '$part is not one of to, cc, bcc'
            );
        }

        $valid = new Zend_Validate_EmailAddress();
        if (!$valid->isValid($email)) {
            throw new Zend_Service_Amazon_Ses_Exception(
                "$email is not a valid email address"
            );
        }

        $property = "_{$part}Recipients";
        $this->{$property}[] = sprintf(
            '%s<%s>',
            !empty($name) ? $name . ' ' : '',
            $email
        );

    }

    /**
     * Clears all recipients
     * @return void
     */
    public function clearRecipients()
    {
        $this->_toRecipients = $this->_ccRecipients = $this->_bccRecipients = array();
    }

    /**
     * Gets the message subject
     * @return string
     */
    public function getSubject()
    {
        return $this->_subject;
    }

    /**
     * Sets the message subject
     * @param string $subject
     * @return Zend_Service_Amazon_Ses_SendEmail
     */
    public function setSubject($subject)
    {
        $this->_subject = $subject;
        return $this;
    }

    /**
     * Gets the email body
     * @return string
     */
    public function getBody()
    {
        return $this->_body;
    }

    /**
     * Sets the email body
     * @param string $body
     * @return Zend_Service_Amazon_Ses_SendEmail
     */
    public function setBody($body)
    {
        $this->_body = $body;
        return $this;
    }

    /**
     * Performs a request to AWS with all data supplied
     * @return Zend_Service_Amazon_Ses_Response_SendEmail
     */
    public function request()
    {
        $params = array(
            'Source' => $this->_source,
            'Message.Subject.Data' => $this->getSubject(),
            'Message.Body.Text.Data' => $this->getBody()
        );

        $params = array_merge(
            $params, $this->_parameterizeRecipients($this->_toRecipients, 'To')
        );

        return $this->_sendRequest($params);
    }
   
    /**
     * Converts array of recipients into format compatable for the SendEmail action
     * @param  array $recipients array of RFC822-compliant email address
     * @throws Zend_Service_Amazon_Ses_Exception if $type is not one of to, cc, bcc
     * @return array
     */
    protected function _parameterizeRecipients(array $recipients, $type)
    {
        $type = strtolower($type);

        if (!in_array($type, array('to', 'cc', 'bcc'))) {
            throw new Zend_Service_Amazon_Ses_Exception(
                '$type must be one of to, cc, or bcc'
            );
        }

        $params = array();
        foreach (array_values($recipients) as $k => $r) {
            $key = 'Destination.' . ucfirst(strtolower($type)) . 'Addresses.member.' . ($k + 1);
            $params[$key] = $r;
        }

        return $params;
    }
}
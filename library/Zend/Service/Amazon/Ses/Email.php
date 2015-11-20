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
 * @version    $Id:$
 */

/**
 * Encapsulates email data used for SES SendEmail action
 *
 * @category   Zend
 * @package    Zend_Service
 * @subpackage Amazon
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Service_Amazon_Ses_Email
{
    /**
     * Source (From) email address
     * @var string
     */
    protected $_from;

    /**
     * To Recipients
     * @var array
     */
    protected $_to = array();

    /**
     * Carbon Copy Recipients
     * @var array
     */
    protected $_cc = array();

    /**
     * Blind Carbon Copy Recipients
     * @var array
     */
    protected $_bcc = array();

    /**
     * The reply-to email address(es) for the message.
     * If the recipient replies to the message, each reply-to address will receive the reply.
     * 
     * @var array
     */
    protected $_replyTo = array();

    /**
     * Message Subject
     * @var string
     */
    protected $_subject;

    /**
     * Email Text Body
     * @var string
     */
    protected $_bodyText;

    /**
     * Sets the charset for the text part of the mail body
     * @var string
     */
    protected $_bodyTextCharset;

    /**
     * Email HTML Body
     * @var string
     */
    protected $_bodyHtml;

    /**
     * Sets the charset for the text part of the mail body
     * @var string
     */
    protected $_bodyHtmlCharset;

    /**
     * Return path email address
     * @var string
     */
    protected $_returnPath;

    /**
     * Gets the HTML part of the message body
     * @return string
     */
    public function getBodyHtml()
    {
        return $this->_bodyHtml;
    }

    /**
     * Sets the HTML part of the message body
     * @param  string $bodyHtml
     * @param  string $charset (Optional)
     * @return Zend_Service_Amazon_Ses_Email
     */
    public function setBodyHtml($bodyHtml, $charset = 'utf-8')
    {
        $this->_bodyHtml = $bodyHtml;
        $this->_bodyHtmlCharset = $charset;
        return $this;
    }

    /**
     * Gets the Return Path
     * @return string
     */
    public function getReturnPath()
    {
        return $this->_returnPath;
    }

    /**
     * Sets the Return Path
     * @param  string $returnPath Email Address
     * @return Zend_Service_Amazon_Ses_Email
     */
    public function setReturnPath($returnPath)
    {
        $this->_returnPath = $returnPath;
        return $this;
    }

    /**
     * Gets the Source (AWS Version of From Email Address)
     * @return string
     */
    public function getFrom()
    {
        return $this->_from;
    }

    /**
     * Sets the From Address
     * @param  string $from RFC-822 Compliant Email Address
     * @param  string $name
     * @return Zend_Service_Amazon_Ses_Email
     */
    public function setFrom($from, $name = null)
    {
        $this->_from = sprintf(
            '%s<%s>', !empty($name) ? $name . ' ' : '', $from
        );

        return $this;
    }

    /**
     * Returns all the TO recipients
     * @return array
     */
    public function getTo()
    {
        return $this->_to;
    }

    /**
     * Add a TO address
     * @param  string $email
     * @param  string $name
     * @return Zend_Service_Amazon_Ses_Email
     */
    public function addTo($email, $name = null)
    {
        $this->_addEmail($email, $name, 'to');
        return $this;
    }

    /**
     * Clears all TO addresses
     * @return void
     */
    public function clearTo()
    {
        $this->_to = array();
    }

    /**
     * Sets the reply-to email address(es) for the message.
     * If the recipient replies to the message, each reply-to address will
     * receive the reply.
     * 
     * @param  string $email Email Address
     * @param  string $name (Optional)
     * @return Zend_Service_Amazon_Ses_Email
     */
    public function addReplyTo($email, $name = null)
    {
        $this->_addEmail($email, $name, 'replyTo');
        return $this;
    }

    /**
     * Gets the reply-to email address(es) for the message.
     * 
     * @return array
     */
    public function getReplyTo()
    {
        return $this->_replyTo;
    }

    /**
     * Clears the reply to addresses.
     * @return void
     */
    public function clearReplyTo()
    {
        $this->_replyTo = array();
    }

    /**
     * Gets registered CC addresses
     * @return array
     */
    public function getCc()
    {
        return $this->_cc;
    }

    /**
     * Clears CC Addresses
     * @return void
     */
    public function clearCc()
    {
        $this->_cc = array();
    }

    /**
     * Adds the CC address
     * @param  string $email
     * @param  string $name
     * @return Zend_Service_Amazon_Ses
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
        return $this->_bcc;
    }

    /**
     * Adds a BCC address
     *
     * @param  string $email
     * @param  string $name
     * @return Zend_Service_Amazon_Ses
     */
    public function addBcc($email, $name = null)
    {
        $this->_addEmail($email, $name, 'bcc');
        return $this;
    }

    /**
     * Clears BCC Addresses
     * @return void
     */
    public function clearBcc()
    {
        $this->_bcc = array();
    }

    /**
     *
     * @param  string $email
     * @param  string $name
     * @param  string $part
     * @return void
     * @throws InvalidArgumentException if email is invalid or part is not one of to, cc, bcc
     */
    protected function _addEmail($email, $name, $part)
    {
        if (!in_array($part, array('to', 'cc', 'bcc', 'replyTo'))) {
            throw new InvalidArgumentException(
                '$part is not one of to, cc, bcc, replyTo'
            );
        }

        $property = "_{$part}";
        $this->{$property}[] = sprintf(
            '%s<%s>',
            !empty($name) ? $name . ' ' : '',
            $email
        );
    }

    /**
     * Clears all recipients
     *
     * @return void
     */
    public function clearRecipients()
    {
        $this->_to = $this->_cc = $this->_bcc = array();
    }

    /**
     * Gets the message subject
     *
     * @return string
     */
    public function getSubject()
    {
        return $this->_subject;
    }

    /**
     * Sets the message subject
     *
     * @param string $subject
     * @return Zend_Service_Amazon_Ses_Email
     */
    public function setSubject($subject)
    {
        $this->_subject = $subject;
        return $this;
    }

    /**
     * Gets the text part of the mail message
     * 
     * @return string
     */
    public function getBodyText()
    {
        return $this->_bodyText;
    }

    /**
     * Sets the text part of the mail message
     * 
     * @param  string $bodyText
     * @return Zend_Service_Amazon_Ses_Email
     */
    public function setBodyText($bodyText, $charset = 'utf-8')
    {
        $this->_bodyText = $bodyText;
        $this->_bodyTextCharset = $charset;
        return $this;
    }

    /**
     * Returns the parameters needed to make a SendEmail request to SES
     * 
     * @return Zend_Service_Amazon_Ses_Response_SendEmail
     */
    public function getParams()
    {
        $params = array(
            'Source' => $this->getFrom(),
            'Message.Subject.Data' => $this->getSubject(),
            'Message.Body.Text.Data' => $this->getBodyText(),
            'Message.Body.Html.Data' => $this->getBodyHtml()
        );

        $params = array_merge(
            $params, $this->_parameterizeRecipients()
        );

        if (!empty($this->_returnPath)) {
            $params['ReturnPath'] = $this->_returnPath;
        }

        return $params;
    }

    /**
     * Converts array of recipients into format compatable for the SendEmail action
     * @return array
     */
    protected function _parameterizeRecipients()
    {
        $params = array();
        foreach (array('To', 'Cc', 'Bcc') as $part) {
            $method = 'get' . $part;
            foreach ($this->$method() as $k => $r) {
                $key = 'Destination.' . $part . 'Addresses.member.' . ($k + 1);
                $params[$key] = $r;
            }
        }

        return $params;
    }
}

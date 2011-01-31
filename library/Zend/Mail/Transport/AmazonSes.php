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
 * @package    Zend_Mail
 * @subpackage Transport
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

/**
 * Amazon SES Transport
 *
 * Sends emails through Amaazons Simple Email Service API
 *
 * @category   Zend
 * @package    Zend_Mail
 * @subpackage Transport
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Mail_Transport_AmazonSes extends Zend_Mail_Transport_Abstract
{
    /**
     * @var Zend_Service_Amazon_Ses
     */
    protected $_ses;

    /**
     * The message id for the last successfully sent message
     * @var string
     */
    protected $_lastMessageId;

    /**
     * 
     * @var Zend_Service_Amazon_Ses
     */
    protected static $_defaultSes;


    /**
     * Constructor
     *
     * @param  Zend_Service_Amazon_Ses $ses (Optional) Amazon Simple Email Service Instance
     * @return void
     * @throws InvalidArgumentException When $ses is null and no default SES has been provided.
     */
    public function __construct(Zend_Service_Amazon_Ses $ses = null)
    {
        $this->_ses = self::$_defaultSes;

        if (!is_null($ses)) {
            $this->_ses = $ses;
        }

        if (is_null($this->_ses)) {
            throw new InvalidArgumentException(
                'constructor argument cannot be null when there is not a ' .
                'default Amazon SES object provided. Eiher provide a ' .
                'constructor $ses argument, or use ' .
                'Zend_Mail_Transport_AmazonSes::setDefaultSes()'
            );
        }
    }

    /**
     * Gets the associated Amazon Simple Email Service Instance
     * @return Zend_Service_Amazon_Ses
     */
    public function getSes()
    {
        return $this->_ses;
    }

    /**
     * Sets an Amazon Simple Email Service Instance
     * 
     * @param  Zend_Service_Amazon_Ses $ses
     * @return Zend_Mail_Transport_AmazonSes
     */
    public function setSes(Zend_Service_Amazon_Ses $ses)
    {
        $this->_ses = $ses;
        return $this;
    }

    /**
     * Passes an email to the Amazon Simple Email Service Object
     *
     * @return void
     */
    protected function _sendMail()
    {
        $email = $this->header . $this->EOL . $this->body;
        $this->_lastMessageId = $this->getSes()->sendRawEmail($email);
    }

    /**
     * Sets the default Amazon SES Object to be used for class instantiation.
     * 
     * @param  Zend_Service_Amazon_Ses $ses (Optional)
     * @return void
     */
    public static function setDefaultSes(Zend_Service_Amazon_Ses $ses)
    {
        self::$_defaultSes = $ses;
    }

    /**
     * Gets default Amazon SES Object
     *
     * @return Zend_Service_Amazon_Ses $ses (Optional)
     */
    public static function getDefaultSes()
    {
        return self::$_defaultSes;
    }
}
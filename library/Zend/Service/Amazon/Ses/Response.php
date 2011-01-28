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
 * Amazon SES Response (Factory Class)
 *
 * @category   Zend
 * @package    Zend_Service
 * @subpackage Amazon
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Service_Amazon_Ses_Response extends Zend_Service_Amazon_Abstract
{
    /**
     * 
     * @param  string             $type     Response Type to build (i.e. SendEmail)
     * @param  Zend_Http_Response $response Raw HTTP Response
     * @throw  Zend_Service_Amazon_Ses_Exception If amazon returns an exception message
     * @return Zend_Service_Amazon_Ses_Response
     */
    public static function factory($type, Zend_Http_Response $response)
    {
        $xml = new SimpleXMLElement($response->getBody());
        $xml->registerXPathNamespace('ses', 'http://ses.amazonaws.com/doc/2010-12-01/');
        
        $errorPath = $xml->xpath('//ses:Error');

        if (!empty($errorPath)) {
            $e = current($errorPath);
            /* @var $e SimpleXMLElement */
            throw new Zend_Service_Amazon_Ses_Exception(
                (string)$e->Message, (string)$e->Code
            );
        }

        $class = 'Zend_Service_Amazon_Ses_Response_' . $type;

        if (!class_exists($class)) {
            throw new Zend_Service_Amazon_Ses_Exception(sprint(
                'No response class found for supplied $type (%s)', $type
            ));
        }

        $obj = new $class();
        /* @var $obj Zend_Service_Amazon_Ses_Response_Abstract */

        $obj->buildFromXml($xml);
        return $obj;
    }
}
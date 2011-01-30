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
class Zend_Service_Amazon_Ses extends Zend_Service_Amazon_Abstract
{

    const XML_NAMESPACE = 'http://ses.amazonaws.com/doc/2010-12-01/';
    const ENDPOINT_URL  = 'https://email.us-east-1.amazonaws.com';
    const HTTP_TIMEOUT_SECONDS  = 10;
    
    /**
     * Verifies an email address.
     * This action causes a confirmation email message to be sent to the
     * specified address.
     * 
     * @param  RFC-822 Compliant Email Address $email
     * @return void
     */
    public function verifyEmailAddress($email)
    {
        $this->_sendRequest('VerifyEmailAddress', array(
            'EmailAddress' => $email
        ));
    }

    /**
     * Sends an email message, with header and content specified by the client.
     * 
     * The SendRawEmail action is useful for sending multipart MIME emails.
     * The raw text of the message must comply with Internet email standards;
     * otherwise, the message cannot be sent.
     *
     * Raw email must:
     * + Message must contain a header and a body, separated by a blank line.
     * + All required header fields must be present.
     * + Each part of a multipart MIME message must be formatted properly.
     * + MIME content types must be among those supported by Amazon SES.
     *   Refer to the Amazon SES Developer Guide for more details.
     * + Content must be base64-encoded, if MIME requires it.
     *
     * @param  string $message    The raw text of the message.
     * @param  string $from       (Optional) From email address, if not included in the raq email's headers.
     * @param  array  $recipients (Optional) Additional receipients to what's provided in the raw email's headers.
     * @return string AWS Message Id
     */
    public function sendRawEmail($message, $from = null, array $recipients = array())
    {
        $params = array(
            'RawMessage.Data' => base64_encode($message),
        );

        if (!is_null($from)) {
            $params['Source'] = $from;
        }

        foreach (array_values($recipients) as $k => $r) {
            $params['Destinations.member.' . ($k + 1)] = $r;
        }

        $xml = $this->_sendRequest('SendRawEmail', $params);

        if (!isset($xml->SendRawEmailResult->MessageId)) {
            throw new Zend_Service_Amazon_Ses_Exception(
                'There was an unexpected error processing your AWS SendRawEmail request'
            );
        }

        return (string)$xml->SendRawEmailResult->MessageId;
    }

    /**
     * Returns a list containing all of the email addresses that have been verified.
     *
     * @return array
     */
    public function listVerifiedEmailAddresses()
    {
        $xml = $this->_sendRequest('ListVerifiedEmailAddresses');

        $addresses = array();
        foreach ($xml->xpath('//ses:member') as $m) {
            $addresses[] = (string)$m;
        }

        return $addresses;
    }

    /**
     * Composes an email message based on input data and then immediately queues the message for sending.
     * 
     * @param  Zend_Service_Amazon_Ses_Email $email
     * @return string AWS Message Id
     * @throws Zend_Service_Amazon_Ses_Exception If the AWS request did not
     *                     return the properly formatted XML response.
     */
    public function sendEmail(Zend_Service_Amazon_Ses_Email $email)
    {
        $xml = $this->_sendRequest('SendEmail', $email->getParams());

        if (!isset($xml->SendEmailResult->MessageId)) {
            throw new Zend_Service_Amazon_Ses_Exception(
                'There was an unexpected error processing your AWS SendEmail request'
            );
        }

        return (string)$xml->SendEmailResult->MessageId;
    }

    /**
     * Returns the user's current activity limits.
     *
     * The following array keys are returned:
     * 
     * max24HourSend: The maximum number of emails the user is
     *                allowed to send in a 24-hour interval.
     *
     * maxSendRate: The maximum number of emails the
     *              user is allowed to send per second.
     *
     * sentLast24Hours: The number of emails sent during
     *                  the previous 24 hours.
     *
     * @return array
     */
    public function getSendQuota()
    {
        $xml = $this->_sendRequest('GetSendQuota');

        if (!isset($xml->GetSendQuotaResult->SentLast24Hours)) {
            throw new Zend_Service_Amazon_Ses_Exception(
                'There was an unexpected error processing your AWS GetSendQuota request'
            );
        }

        return array(
            'max24HourSend' => (string)$xml->GetSendQuotaResult->Max24HourSend,
            'maxSendRate' => (string)$xml->GetSendQuotaResult->MaxSendRate,
            'sentLast24Hours' => (string)$xml->GetSendQuotaResult->SentLast24Hours
        );
    }

    /**
     * Deletes the specified email address from the list of verified addresses.
     * 
     * @param string $email RFC-822 Compliant Email Address
     * @return void
     */
    public function deleteVerifiedEmailAddress($email)
    {
        $params = array(
            'EmailAddress' => $email
        );
        
        $this->_sendRequest('DeleteVerifiedEmailAddress', $params);
    }

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
     * 
     * @params string $action 
     * @param  array  $params (Optional)
     * @return SimpleXMLElement
     * @throws Zend_Service_Amazon_Ses_Exception if there was a
     *                          miscommunication during the http request, or
     *                          if aws returned an error message
     */
    protected function _sendRequest($action, array $params = array())
    {
        $url = self::ENDPOINT_URL;
        $params = array_merge($params, array('Action' => $action));

        try {
            /* @var $client Zend_Http_Client */
            $client = self::getHttpClient();
            $client->resetParameters();

            $this->_addRequiredHeaders($client);

            $client->setConfig(array(
                'timeout' => self::HTTP_TIMEOUT_SECONDS
            ));

            $client->setUri($url);
            $client->setMethod(Zend_Http_Client::POST);
            $client->setParameterPost($params);

            $response = $client->request();

        } catch (Zend_Http_Client_Exception $e) {
            $message = 'Error in request to AWS service: ' . $e->getMessage();
            throw new Zend_Service_Amazon_Ses_Exception($message, $e->getCode(), $e);
        }

        $xml = new SimpleXMLElement($response->getBody());
        $xml->registerXPathNamespace('ses', self::XML_NAMESPACE);

        if (isset($xml->Error)) {
            $e = $xml->Error;
            $code    = isset($e->Code) ? (string) $e->Code : null;
            $message = isset($e->Message) ? (string) $e->Message : null;
            
            /* @var $e SimpleXMLElement */
            throw new Zend_Service_Amazon_Ses_Exception(
                $message, $code
            );
        }

        return $xml;
    }
}
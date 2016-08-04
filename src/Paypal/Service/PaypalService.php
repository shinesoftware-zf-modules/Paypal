<?php
/**
* Copyright (c) 2014 Shine Software.
* All rights reserved.
*
* Redistribution and use in source and binary forms, with or without
* modification, are permitted provided that the following conditions
* are met:
*
* * Redistributions of source code must retain the above copyright
* notice, this list of conditions and the following disclaimer.
*
* * Redistributions in binary form must reproduce the above copyright
* notice, this list of conditions and the following disclaimer in
* the documentation and/or other materials provided with the
* distribution.
*
* * Neither the names of the copyright holders nor the names of the
* contributors may be used to endorse or promote products derived
* from this software without specific prior written permission.
*
* THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
* "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
* LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS
* FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE
* COPYRIGHT OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT,
* INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING,
* BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
* LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER
* CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT
* LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN
* ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
* POSSIBILITY OF SUCH DAMAGE.
*
* @package Events
* @subpackage Service
* @author Michelangelo Turillo <mturillo@shinesoftware.com>
* @copyright 2014 Michelangelo Turillo.
* @license http://www.opensource.org/licenses/bsd-license.php BSD License
* @link http://shinesoftware.com
* @version @@PACKAGE_VERSION@@
*/

namespace Paypal\Service;

use Zend\EventManager\EventManager;
use Zend\Db\TableGateway\TableGateway;
use Zend\Stdlib\Hydrator\ClassMethods;
use Zend\EventManager\EventManagerAwareInterface;
use Zend\EventManager\EventManagerInterface;

class PaypalService implements PaypalServiceInterface, EventManagerAwareInterface
{
    protected $eventManager;
    protected $settings;
    protected $translator;
    
    public function __construct(\Base\Service\SettingsService $settings, \Zend\Mvc\I18n\Translator $translator ){
        $this->settings = $settings;
        $this->translator = $translator;
    }
    
    /**
     * Create the Pay key code to redirect the user to the Paypal website
     * 
     * @param string email $receiver
     * @param float $amount
     * @param string $currency
     * @throws \Exception
     * @return string
     */
    public function getPayKey($receiver, $amount, $memo="", $currency="EUR")
    {
        $link = null;
        $username = $this->settings->getValueByParameter('paypal', 'username');
        $password = $this->settings->getValueByParameter('paypal', 'password');
        $signature = $this->settings->getValueByParameter('paypal', 'signature');
        $appid = $this->settings->getValueByParameter('paypal', 'appid');
        $mode = $this->settings->getValueByParameter('paypal', 'mode');
        $log = $this->settings->getValueByParameter('paypal', 'log');
        $currency_fallback = $this->settings->getValueByParameter('paypal', 'currency');
        $baseUrl = $this->settings->getValueByParameter('paypal', 'baseurl');
        $paypal_receiver = $this->settings->getValueByParameter('paypal', 'receiver');
        $fee = $this->settings->getValueByParameter('paypal', 'fee');
        $feelowlimit = $this->settings->getValueByParameter('paypal', 'feelowlimit');
        $feebase = $this->settings->getValueByParameter('paypal', 'feebase');
        $success = "$baseUrl/paypal/success";
        $cancel = "$baseUrl/paypal/failure";
        $ipn = "$baseUrl/paypal/ipn";
        
        if(empty($username) || empty($password) || empty($signature) || empty($appid) || empty($currency) || empty($baseUrl))
            throw new \Exception('Paypal is not set yet! Go to the administration page and then Settings > Paypal');
        
        $feevalue = round($amount * $fee / 100, 2);
        
        // The minimal transfer amount for Japanese Yen is 2 JPY according to this document: 
        // https://www.paypal.com/uk/cgi-bin/webscr?cmd=xpt/FinancialInstrument/popup/WireWithdrawMinimumAmount-outside.
        if($currency == "JPY" && $feevalue < 2) { 
            $feevalue = "2";
        }
        
        // if the fee value is too low set the minumum
        if($feevalue < $feelowlimit) {
            $feevalue = $feebase;
        }
        
        $config = array('acct1.UserName' => $username,
                'acct1.Password' => $password,
                'acct1.Signature' => $signature,
                'acct1.AppId' => $appid,
                'mode' => !empty($mode) ? $mode : "sandbox",
                'http.ConnectionTimeOut' => 30,
                'log.LogEnabled' => (bool)$log,
                'log.FileName' => LOG_PATH . '/paypal.log',
                'log.LogLevel' => 'FINE',
                'validation.level' => 'log');
        
        $service = new \PayPal\Service\AdaptivePaymentsService($config);
         
        $payer = new \PayPal\Api\Payer();
        $payer->setPaymentMethod("paypal");
        
        $requestEnvelope = new \PayPal\Types\Common\RequestEnvelope("en_US");
        $receivers = array();

        // seller 
        $receivers[0] = new \PayPal\Types\AP\Receiver();
        $receivers[0]->email = $receiver; // A receiver's email address
        $receivers[0]->amount = $amount;  // Amount to be credited to the receiver's account
        $receivers[0]->primary = true;
         
        // website owner 
        $receivers[1] = new \PayPal\Types\AP\Receiver();
        $receivers[1]->email = $paypal_receiver; // A receiver's email address
        $receivers[1]->amount = $feevalue;  // Amount to be credited to the receiver's account
        
        $receiverList = new \PayPal\Types\AP\ReceiverList($receivers);
        
        
        $payRequest = new \PayPal\Types\AP\PayRequest($requestEnvelope, "PAY", $cancel, $currency, $receiverList, $success);
        $payRequest->ipnNotificationUrl = $ipn;
        $payRequest->memo = $memo;

        try {
            $response = $service->Pay($payRequest);
        } catch(\Exception $ex) {
            var_dump($ex->getMessage());
            die;
        }
        
        if ($response->responseEnvelope->ack == "Success"){
            return $response->payKey;
        }else{
            throw new \Exception($response->error[0]->message);
        }
    }
    
    
    /* (non-PHPdoc)
     * @see \Zend\EventManager\EventManagerAwareInterface::setEventManager()
    */
    public function setEventManager (EventManagerInterface $eventManager){
        $eventManager->addIdentifiers(get_called_class());
        $this->eventManager = $eventManager;
    }
    
    /* (non-PHPdoc)
     * @see \Zend\EventManager\EventsCapableInterface::getEventManager()
    */
    public function getEventManager (){
        if (null === $this->eventManager) {
            $this->setEventManager(new EventManager());
        }
    
        return $this->eventManager;
    }
    
}
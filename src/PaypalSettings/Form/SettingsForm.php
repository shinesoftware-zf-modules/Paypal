<?php
namespace PaypalSettings\Form;
use Zend\Form\Form;
use Zend\Stdlib\Hydrator\ClassMethods;
use \Base\Hydrator\Strategy\DateTimeStrategy;

class SettingsForm extends Form
{

    public function init ()
    {

        $this->setAttribute('method', 'post');
        
        $this->add(array (
                'type' => 'Zend\Form\Element\Select',
                'name' => 'currency',
                'attributes' => array (
                        'class' => 'form-control'
                ),
                'options' => array (
                        'label' => _('Base Currency'),
                        'value_options' => array (
                        		'USD' => _('Dollar'),
                        		'GBP' => _('Pound'),
                        		'JPY' => _('Yen'),
                                'EUR' => _('Euro'),
                        )
                )
        ));
        
        
        $this->add(array (
                'type' => 'Zend\Form\Element\Select',
                'name' => 'mode',
                'attributes' => array (
                        'class' => 'form-control'
                ),
                'options' => array (
                        'label' => _('Mode'),
                        'value_options' => array (
                        		'sandbox' => _('TEST'),
                                'live' => _('LIVE'),
                        )
                )
        ));
        
        $this->add(array (
                'type' => 'Zend\Form\Element\Select',
                'name' => 'log',
                'attributes' => array (
                        'class' => 'form-control'
                ),
                'options' => array (
                        'label' => _('Log'),
                        'value_options' => array (
                        		'1' => _('Yes, write a log file'),
                        		'0' => _('No, disable the log file'),
                        )
                )
        ));
        
        $this->add(array (
                'name' => 'baseurl',
                'attributes' => array (
                        'class' => 'form-control',
                		'value' => 'http://www.mysite.com/'
                ),
                'options' => array (
                        'label' => _('Website URL'),
                )
        ));
        
        $this->add(array (
                'name' => 'receiver',
                'attributes' => array (
                        'class' => 'form-control',
                		'placeholder' => _('Your own paypal email account')
                ),
                'options' => array (
                        'label' => _('Main Paypal account receiver'),
                )
        ));
        
        
        $this->add(array (
                'name' => 'fee',
                'attributes' => array (
                        'class' => 'form-control',
                        'placeholder' => _('Type here the percentage of the fee'),
                        'value' => 4.5
                ),
                'options' => array (
                        'label' => _('Standard Fee Percentage'),
                )
        ));
        
        $this->add(array (
                'name' => 'feebase',
                'attributes' => array (
                        'class' => 'form-control',
                        'placeholder' => _('Type here the minimum value for the fee'),
                        'value' => 0.5
                ),
                'options' => array (
                        'label' => _('Minimum Fee Value'),
                )
        ));
        
        $this->add(array (
                'name' => 'feelowlimit',
                'attributes' => array (
                        'class' => 'form-control',
                        'placeholder' => _('Type here the minimum limit value for the fee'),
                        'value' => 0.5
                ),
                'options' => array (
                        'label' => _('Low Fee limit Condition'),
                )
        ));
        
        $this->add(array (
                'name' => 'username',
                'attributes' => array (
                        'class' => 'form-control',
                ),
                'options' => array (
                        'label' => _('Username'),
                )
        ));
        
        
        $this->add(array (
                'name' => 'signature',
                'attributes' => array (
                        'class' => 'form-control',
                ),
                'options' => array (
                        'label' => _('Signature'),
                )
        ));
        
        $this->add(array (
                'name' => 'appid',
                'attributes' => array (
                        'class' => 'form-control',
                ),
                'options' => array (
                        'label' => _('App ID'),
                )
        ));
        
        $this->add(array (
                'name' => 'password',
                'attributes' => array (
                        'class' => 'form-control',
                        'placeholder' => 'Test APPId: APP-80W284485P519543T',
                ),
                'options' => array (
                        'label' => _('Password'),
                )
        ));
        
        $this->add(array ( 
                'name' => 'submit', 
                'attributes' => array ( 
                        'type' => 'submit', 
                        'class' => 'btn btn-success', 
                        'value' => _('Save')
                )
        ));
     
    }
}
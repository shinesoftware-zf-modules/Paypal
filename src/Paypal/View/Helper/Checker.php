<?php
namespace Paypal\View\Helper;
use Zend\View\Helper\AbstractHelper;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class Checker extends AbstractHelper implements ServiceLocatorAwareInterface {
	
	protected $serviceLocator;
	 
	/**
	 * Set the service locator.
	 *
	 * @param $serviceLocator ServiceLocatorInterface       	
	 * @return CustomHelper
	 */
	public function setServiceLocator(ServiceLocatorInterface $serviceLocator) {
		$this->serviceLocator = $serviceLocator;
		return $this;
	}
	
	/**
	 * Get the service locator.
	 *
	 * @return \Zend\ServiceManager\ServiceLocatorInterface
	 */
	public function getServiceLocator() {
	    return $this->serviceLocator;
	}
	
	public function __invoke($content) {

            $re = "/(?P<entire_string>
                    ^.*? # Everything up until it sees the currency
                    (?P<currency_symbol>[\\£\\$\\€\\¥\\&euro;])
                    (?=[\\d.,]+)
                    (?P<whole_number>\\d+)
                    (?P<thousands>
                    (?:
                    (?P<thousands_separator>[,.])
                    (?P<thousands_trio>\\d{3})
                    )*
                    )
                    (?:
                    (?!\\5) # decimal_separator is not a thousands separator
                    (?P<decimal_separator>[,.])
                    (?P<decimal>\\d{2})
                    )? # Decimal is optional
                    # No more digits or separators afterward allowed
                    (?![\\d,.])
                    .*$ # Everything after the currency
                    )/xmu"; 
            
	    preg_match_all($re, $content, $matches);
	   
	     if(!empty($matches['entire_string'])){
	         return true;
	     }
	    
	    return false;
	}
}
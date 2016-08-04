<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Paypal\Controller;
use Base\Service\SettingsService;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Base\Service\SettingsServiceInterface;

class IndexController extends AbstractActionController
{
    protected $settings;
    protected $translator;
    
    /**
     * preDispatch event of the page
     *
     * (non-PHPdoc)
     * @see Zend\Mvc\Controller.AbstractActionController::onDispatch()
     */
    public function onDispatch(\Zend\Mvc\MvcEvent $e){
        $this->translator = $e->getApplication()->getServiceManager()->get('translator');
    
        return parent::onDispatch( $e );
    }
    
    public function __construct(SettingsServiceInterface $settings)
    {
        $this->settings = $settings;
    }
    
    public function ipnAction ()
    {
        $params = $this->params()->fromRoute('params');
        
        $logger = new \Zend\Log\Logger();
        $writer = new \Zend\Log\Writer\Stream(PUBLIC_PATH . '/../data/log/paypal.log');
        $logger->addWriter($writer);
        
        $logger->debug($_REQUEST);
        $logger->debug($params);
        die('IPN');
    }
    
    public function successAction(){
        $params = $this->params()->fromRoute('params');
        
    }
    
    public function failureAction(){
        $params = $this->params()->fromRoute('params');
    }
    
}

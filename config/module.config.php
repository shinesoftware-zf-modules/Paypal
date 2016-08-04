<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonpaypal for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

return array(
		'bjyauthorize' => array(
				'guards' => array(
					'BjyAuthorize\Guard\Route' => array(
							
		                // Generic route guards
		                array('route' => 'paypal', 'roles' => array('guest')),
		                array('route' => 'paypal/default', 'roles' => array('guest')),
		                array('route' => 'paypal/ipn', 'roles' => array('guest')),
		                array('route' => 'paypal/success', 'roles' => array('guest')),
		                array('route' => 'paypal/failure', 'roles' => array('guest')),
					        
				        // Custom Module
				        array('route' => 'zfcadmin/paypal/settings', 'roles' => array('admin')),
					),
			  ),
		),
		'navigation' => array(
		        'admin' => array(
		                'settings' => array(
		                        'label' => _('Settings'),
		                        'route' => 'zfcadmin',
		                        'pages' => array (
		                                array (
    				                        'label' => 'Paypal',
    				                        'route' => 'zfcadmin/paypal/settings',
    				                        'icon' => 'fa fa-paypal'
		                                ),
		                        ),
		                ),
		                
        		),
		),
    'router' => array(
        'routes' => array(
        		'zfcadmin' => array(
        				'child_routes' => array(
        						'paypal' => array(
        								'type' => 'literal',
        								'options' => array(
        										'route' => '/paypal',
        										'defaults' => array(
        												'controller' => 'PaypalAdmin\Controller\Index',
        												'action'     => 'index',
        										),
        								),
        								'may_terminate' => true,
        								'child_routes' => array (
        										'default' => array (
        												'type' => 'Segment',
        												'options' => array (
        														'route' => '/[:action[/:id]]',
        														'constraints' => array (
        																'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
        																'id' => '[0-9]*'
        														),
        														'defaults' => array ()
        												)
        										),
        										'settings' => array (
        												'type' => 'Segment',
        												'options' => array (
        														'route' => '/settings/[:action[/:id]]',
        														'constraints' => array (
        																'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
        																'id' => '[0-9]*'
        														),
        														'defaults' => array (
            														'controller' => 'PaypalSettings\Controller\Index',
            														'action'     => 'index',
        														)
        												)
        										)
        								),
        						),
        				),
        		),
        		
            'paypal' => array(
                'type'    => 'Literal',
                'options' => array(
                    'route'    => '/paypal',
                    'defaults' => array(
                        '__NAMESPACE__' => 'Paypal\Controller',
                        'controller'    => 'Index',
                        'action'        => 'index',
                        'page'			=> 1
                    ),
                ),
                'may_terminate' => true,
                'child_routes' => array(
                    'default' => array(
                        'type'    => 'Segment',
                        'options' => array(
                            'route'    => '/[:controller[/:action]]',
                            'constraints' => array(
                                'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'action'     => '[a-zA-Z][a-zA-Z0-9_-]*',
                            ),
                            'defaults' => array(
                            ),
                        ),
                    ),
                    'ipn' => array(
                            'type'    => 'Segment',
                            'options' => array(
                                    'route'    => '/ipn[/:params]',
                                    'constraints' => array(
                                            'params'     => '[a-zA-Z][a-zA-Z0-9_-]*',
                                    ),
                                    'defaults' => array(
                                            'controller'        => 'Index',
                                            'action'        => 'ipn',
                                    ),
                            ),
                    ),
                    'success' => array(
                            'type'    => 'Segment',
                            'options' => array(
                                    'route'    => '/success[/:params]',
                                    'constraints' => array(
                                            'params'     => '[a-zA-Z][a-zA-Z0-9_-]*',
                                    ),
                                    'defaults' => array(
                                            'controller'        => 'Index',
                                            'action'        => 'success',
                                    ),
                            ),
                    ),
                    'failure' => array(
                            'type'    => 'Segment',
                            'options' => array(
                                    'route'    => '/failure[/:params]',
                                    'constraints' => array(
                                            'params'     => '[a-zA-Z][a-zA-Z0-9_-]*',
                                    ),
                                    'defaults' => array(
                                            'controller'        => 'Index',
                                            'action'        => 'failure',
                                    ),
                            ),
                    ),
                ),
            ),
        ),
    ),
    'service_manager' => array(
        'abstract_factories' => array(
            'Zend\Cache\Service\StorageCacheAbstractServiceFactory',
            'Zend\Log\LoggerAbstractServiceFactory',
        ),
        'aliases' => array(
            'translator' => 'MvcTranslator',
        ),
    ),

		'controllers' => array(
        'invokables' => array(
        ),
        'factories' => array(
        		'Paypal\Controller\Index' => 'Paypal\Factory\IndexControllerFactory',
        		'PaypalSettings\Controller\Index' => 'PaypalSettings\Factory\IndexControllerFactory',
        )
    ),
    'view_helpers' => array (
    		'invokables' => array (
		    		'paypal' => 'Paypal\View\Helper\Link',
		    		'checkpaypal' => 'Paypal\View\Helper\Checker',
		    		'tickets' => 'Paypal\View\Helper\Data',
    		)
    ),
    'view_manager' => array(
        'template_path_stack' => array(
            __DIR__ . '/../view',
        ),
    ),
    // Placeholder for console routes
    'console' => array(
        'router' => array(
            'routes' => array(
            ),
        ),
    ),
);

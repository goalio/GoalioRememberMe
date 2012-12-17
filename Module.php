<?php
namespace GoalioRememberMe;

use Zend\Mvc\MvcEvent;
use Zend\Http\Request as HttpRequest;

class Module {

    public function init() {

    }

    public function getConfig() {
        return include __DIR__ . '/config/module.config.php';
    }

    public function getAutoloaderConfig() {
        return array(
                'Zend\Loader\StandardAutoloader' => array(
                        'namespaces' => array(
                                __NAMESPACE__      => __DIR__ . '/src/' . __NAMESPACE__,
                        ),
                ),
        );
    }

    public function getServiceConfig() {
        return array(
            'invokables' => array(
                'GoalioRememberMe\Authentication\Adapter\Cookie' => 'GoalioRememberMe\Authentication\Adapter\Cookie',
                'goaliorememberme_rememberme_service'            => 'GoalioRememberMe\Service\RememberMe',
            ),

            'factories' => array(

                'goaliorememberme_module_options' => function ($sm) {
                    $config = $sm->get('Config');
                    return new Options\ModuleOptions(isset($config['goaliorememberme']) ? $config['goaliorememberme'] : array());
                },

                'goaliorememberme_rememberme_mapper' => function ($sm) {
                    $options = $sm->get('zfcuser_module_options');
                    $rememberOptions = $sm->get('goaliorememberme_module_options');
                    $mapper = new Mapper\RememberMe;
                    $mapper->setDbAdapter($sm->get('zfcuser_zend_db_adapter'));
                    $entityClass = $rememberOptions->getRememberMeEntityClass();
                    $mapper->setEntityPrototype(new $entityClass);
                    $mapper->setHydrator(new Mapper\RememberMeHydrator());
                    return $mapper;
                },
            ),
        );
    }


    public function onBootstrap(MvcEvent $e) {

        $session = new \Zend\Session\Container('zfcuser');
        $cookieLogin = $session->offsetGet("cookieLogin");
        if(!$e->getRequest() instanceof HttpRequest)
        {
            return;
        }
        $cookie = $e->getRequest()->getCookie();
        // do autologin only if not done before and cookie is present
        if(isset($cookie['remember_me']) && $cookieLogin == false) {
            $adapter = $e->getApplication()->getServiceManager()->get('ZfcUser\Authentication\Adapter\AdapterChain');
            $adapter->prepareForAuthentication($e->getRequest());
            $authService = $e->getApplication()->getServiceManager()->get('zfcuser_auth_service');

            $auth = $authService->authenticate($adapter);
        }

    }
}


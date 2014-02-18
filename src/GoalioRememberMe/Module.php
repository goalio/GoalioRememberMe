<?php
namespace GoalioRememberMe;

use Zend\Mvc\MvcEvent;
use Zend\Http\Request as HttpRequest;
use Zend\Loader\StandardAutoloader;
use Zend\Loader\AutoloaderFactory;
use Zend\EventManager\EventInterface;

class Module {

    public function getAutoloaderConfig() {
        return array(
            AutoloaderFactory::STANDARD_AUTOLOADER => array(
                StandardAutoloader::LOAD_NS => array(
                    __NAMESPACE__ => __DIR__,
                ),
            ),
        );
    }

    public function getConfig() {
        return include __DIR__ . '/../../config/module.config.php';
    }

    public function getServiceConfig() {
        return array(
            'invokables' => array(
                'GoalioRememberMe\Authentication\Adapter\Cookie' => 'GoalioRememberMe\Authentication\Adapter\Cookie',
                'GoalioRememberMe\Form\Login'                    => 'GoalioRememberMe\Form\Login',
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

                'zfcuser_login_form' => function($sm) {
                    $options = $sm->get('zfcuser_module_options');
                    $form = new Form\Login(null, $options);
                    $form->setInputFilter(new \ZfcUser\Form\LoginFilter($options));
                    return $form;
                },
            ),
        );
    }


    public function onBootstrap(MvcEvent $e) 
    {

        if (!$e->getRequest() instanceof HttpRequest) {
            return;
        }

        $app = $e->getApplication();
        $serviceManager = $app->getServiceManager();

        $userIsLoggedIn = $serviceManager->get('zfcuser_auth_service')->hasIdentity();
        $cookie = $e->getRequest()->getCookie();

        // do autologin only if not done before and cookie is present
        if(!$userIsLoggedIn && isset($cookie['remember_me'])) {
            $adapter = $e->getApplication()->getServiceManager()->get('ZfcUser\Authentication\Adapter\AdapterChain');
            $adapter->prepareForAuthentication($e->getRequest());
            $authService = $e->getApplication()->getServiceManager()->get('zfcuser_auth_service');

            $auth = $authService->authenticate($adapter);
        }

        $app->getEventManager()->getSharedManager()->attach('ZfcUser\Service\User', 'changePassword.post', function(EventInterface $e) use ($serviceManager) {
            $userId = $serviceManager->get('zfcuser_auth_service')->getIdentity()->getId();
            $serviceManager->get('goaliorememberme_rememberme_mapper')->removeAll($userId);
        });
    }
}


<?php
return array(
    'service_manager' => array(
        'invokables' => array(
            'GoalioRememberMe\Authentication\Adapter\Cookie' => 'GoalioRememberMe\Authentication\Adapter\Cookie',
            'GoalioRememberMe\Form\Login'                    => 'GoalioRememberMe\Form\Login',
            'goaliorememberme_rememberme_service'            => 'GoalioRememberMe\Service\RememberMe',
        ),

        'factories' => array(

            'goaliorememberme_module_options' => function ($sm) {
                    $config = $sm->get('Config');
                    return new \GoalioRememberMe\Options\ModuleOptions(isset($config['goaliorememberme']) ? $config['goaliorememberme'] : array());
                },

            'goaliorememberme_rememberme_mapper' => function ($sm) {
                    $options = $sm->get('zfcuser_module_options');
                    $rememberOptions = $sm->get('goaliorememberme_module_options');
                    $mapper = new \GoalioRememberMe\Mapper\RememberMe;
                    $mapper->setDbAdapter($sm->get('zfcuser_zend_db_adapter'));
                    $entityClass = $rememberOptions->getRememberMeEntityClass();
                    $mapper->setEntityPrototype(new $entityClass);
                    $mapper->setHydrator(new \GoalioRememberMe\Mapper\RememberMeHydrator());
                    return $mapper;
                },

            'zfcuser_login_form' => function($sm) {
                    $options = $sm->get('zfcuser_module_options');
                    $form = new \GoalioRememberMe\Form\Login(null, $options);
                    $form->setInputFilter(new \ZfcUser\Form\LoginFilter($options));
                    return $form;
                },
        ),
    ),
);
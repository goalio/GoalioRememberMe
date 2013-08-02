<?php
/**
 * GoalioRememberMe Configuration
 *
 * If you have a ./config/autoload/ directory set up for your project, you can
 * drop this config file in it and change the values as you wish.
 */
$settings = array(

    /**
     * RememberMe Model Entity Class
     *
     * Name of Entity class to use. Useful for using your own entity class
     * instead of the default one provided. Default is ZfcUser\Entity\User.
     */
    //'remember_me_entity_class' => 'GoalioRememberMe\Entity\RememberMe',

    /**
     * Remember me cookie expire time
     *
     * How long will the user be remembered for, in seconds?
     *
     * Default value: 2592000 seconds = 30 days
     * Accepted values: the number of seconds the user should be remembered
     */
    //'cookie_expire' => 2592000,
    
    /**
     * Remember me cookie domain
     *
     * Default value: null (current domain)
     * Accepted values: a string containing the domain (example.com), subdomains (sub.example.com) or the all subdomains qualifier (.example.com)
     */
    //'cookie_domain' => null,

    /**
     * End of GoalioRememberMe configuration
     */
);

/**
 * ZfcUser Configuration
 *
 * If you have a ./config/autoload/ directory set up for your project, you can
 * drop this config file in it and change the values as you wish.
 */
$zfcSettings = array(

    /**
     * Authentication Adapters
     *
     * Specify the adapters that will be used to try and authenticate the user
     *
     * Default value: array containing 'ZfcUser\Authentication\Adapter\Db'
     * Accepted values: array containing services that implement 'ZfcUser\Authentication\Adapter\ChainableAdapter'
     */
    'auth_adapters' => array( 50 => 'GoalioRememberMe\Authentication\Adapter\Cookie' ),

    /**
     * End of ZfcUser configuration
     */
);

/**
 * You do not need to edit below this line
 */
return array(
    'goaliorememberme' => $settings,
    'zfcuser' => $zfcSettings,
);

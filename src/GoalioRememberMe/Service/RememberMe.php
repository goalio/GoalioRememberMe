<?php

namespace GoalioRememberMe\Service;

use Zend\ServiceManager\ServiceManagerAwareInterface;
use Zend\ServiceManager\ServiceManager;
use ZfcBase\EventManager\EventProvider;
use GoalioRememberMe\Options\RememberMeOptionsInterface;
use Zend\Math\Rand;

class RememberMe extends EventProvider implements ServiceManagerAwareInterface
{
    protected $mapper, $options;

    protected $serviceManager;

    public function createToken($length = 16)
    {
        $rand = Rand::getString($length, null, true);
        return $rand;
    }

    public function createSerieId($length = 16)
    {
        $rand = Rand::getString($length, null, true);
        return $rand;
    }

    public function updateSerie($entity)
    {
        $rememberMe = $this->getMapper()->findByIdSerie($entity->getUserId(), $entity->getSid());
        if($rememberMe){
            // Update serie with new token
            $token = $this->createToken();
            $rememberMe->setToken($token);
            $this->setCookie($rememberMe);
            $this->getMapper()->updateSerie($rememberMe);
            return $token;
        }
        return false;
    }

    public function createSerie($userId)
    {
        $token = $this->createToken();
        $serieId = $this->createSerieId();

        $class = $this->getOptions()->getRememberMeEntityClass();
        $rememberMe = new $class;
        $rememberMe->setUserId($userId);
        $rememberMe->setSid($serieId);
        $rememberMe->setToken($token);

        if($this->setCookie($rememberMe))
        {
            $rememberMe = $this->getMapper()->createSerie($rememberMe);
            return $rememberMe;
        }

        return false;
    }

    public function removeSerie($userId, $serieId)
    {
        $this->getMapper()->removeSerie($userId, $serieId);
    }

    public function removeCookie()
    {
        setcookie("remember_me", "", time() - 3600, '/');
    }

    public static function getCookie()
    {
        return isset($_COOKIE['remember_me']) ? $_COOKIE['remember_me'] : false;
    }

    public function setCookie($entity)
    {
        $cookieLength = $this->getOptions()->getCookieExpire();
        $cookieDomain = $this->getOptions()->getCookieDomain();
        $cookieValue = $entity->getUserId() . "\n" . $entity->getSid() . "\n" . $entity->getToken();
        return setcookie("remember_me", $cookieValue, time() + $cookieLength, '/', $cookieDomain, null, true);
    }

    /**
     * Check whether the current login is done via cookie
     *
     * Should be performed before allowing to change PW, access Financial Information etc.
     *
     * @return Boolean
     */
    public function isCookieLogin() {
        $session = new \Zend\Session\Container('zfcuser');
        return $session->offsetGet("cookieLogin");
    }

    public function setMapper($mapper)
    {
        $this->mapper = $mapper;
    }

    public function getMapper()
    {
        if (null === $this->mapper) {
            $this->mapper = $this->getServiceManager()->get('goaliorememberme_rememberme_mapper');
        }
        return $this->mapper;
    }

    /**
     * Retrieve service manager instance
     *
     * @return ServiceManager
     */
    public function getServiceManager()
    {
        return $this->serviceManager;
    }

    /**
     * Set service manager instance
     *
     * @param ServiceManager $locator
     * @return User
     */
    public function setServiceManager(ServiceManager $serviceManager)
    {
        $this->serviceManager = $serviceManager;
        return $this;
    }


    public function setOptions(RememberMeOptionsInterface $options)
    {
        $this->options = $options;
        return $this;
    }

    public function getOptions()
    {
        if (!$this->options instanceof RememberMeOptionsInterface) {
            $this->setOptions($this->getServiceManager()->get('goaliorememberme_module_options'));
        }
        return $this->options;
    }
}

<?php

namespace GoalioRememberMe\Options;

use Zend\Stdlib\AbstractOptions;

class ModuleOptions extends AbstractOptions implements
    RememberMeOptionsInterface
{
    /**
     * Turn off strict options mode
     */
    protected $__strictMode__ = false;

    /**
     * 30 days default
     * @var int
     */
    protected $cookieExpire = 2592000;

    /**
     * @var string
     */
    protected $cookieDomain = null;
    
    /**
     * @var string
     */
    protected $rememberMeEntityClass = 'GoalioRememberMe\Entity\RememberMe';

    public function setCookieExpire($seconds)
    {
        $this->cookieExpire = $seconds;
        return $this;
    }

    public function getCookieExpire()
    {
        return $this->cookieExpire;
    }

	public function setRememberMeEntityClass($rememberMeEntityClass) {
        $this->rememberMeEntityClass = $rememberMeEntityClass;
        return $this;
    }

	public function getRememberMeEntityClass() {
        return $this->rememberMeEntityClass;
    }
    
    public function getCookieDomain() {
        return $this->cookieDomain;
    }
    
    public function setCookieDomain($cookieDomain) {
        $this->cookieDomain = $cookieDomain;
        return $this;
    }
}

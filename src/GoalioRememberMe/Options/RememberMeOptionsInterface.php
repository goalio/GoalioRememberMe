<?php

namespace GoalioRememberMe\Options;

interface RememberMeOptionsInterface
{
    /**
     * @param int $seconds
     */
    public function setCookieExpire($seconds);

    /**
     * @return int
     */
    public function getCookieExpire();
    
    /**
     * @return string
     */
    public function getCookieDomain();
}

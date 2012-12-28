<?php

namespace GoalioRememberMe\Authentication\Adapter;

use ZfcUser\Authentication\Adapter\AbstractAdapter;
use Zend\Authentication\Result as AuthenticationResult;
use Zend\ServiceManager\ServiceManagerAwareInterface;
use Zend\ServiceManager\ServiceManager;
use Zend\Stdlib\RequestInterface as Request;
use Zend\Stdlib\ResponseInterface as Response;
use ZfcUser\Authentication\Adapter\AdapterChainEvent as AuthEvent;

class Cookie extends AbstractAdapter implements ServiceManagerAwareInterface
{
    protected $userMapper;

    protected $rememberMeMapper;

    protected $serviceManager;

    protected $rememberMeService;

    public function authenticate(AuthEvent $e)
    {
        // check if cookie needs to be set, only when prior auth has been successful
        if($e->getIdentity() !== null && $e->getRequest()->isPost() && $e->getRequest()->getPost()->get('remember_me') == 1) {
            $userObject = $this->getUserMapper()->findById($e->getIdentity());
            $this->getRememberMeService()->createSerie($userObject->getId());

            /**
             *  If the user has first logged in with a cookie,
             *  but afterwords login with identity/credential
             *  we remove the "cookieLogin" session.
             */
            $session = new \Zend\Session\Container('zfcuser');
            $session->offsetSet("cookieLogin", false);

            return;
        }

        if ($this->isSatisfied()) {
            $storage = $this->getStorage()->read();
            $e->setIdentity($storage['identity'])
                ->setCode(AuthenticationResult::SUCCESS)
                ->setMessages(array('Authentication successful.'));
            return;
        }

        $cookies = $e->getRequest()->getCookie();

        // no cookie present, skip authentication
        if(!isset($cookies['remember_me'])) {
            return false;
        }

        $cookie = explode("\n", $cookies['remember_me']);

        $rememberMe = $this->getRememberMeMapper()->findByIdSerie($cookie[0], $cookie[1]);

        if(!$rememberMe) {
            $this->getRememberMeService()->removeCookie();
            return false;
        }

        if($rememberMe->getToken() !== $cookie[2])
        {
            // H4x0r
            // @TODO: Inform user of theft, change password?
            $this->getRememberMeMapper()->removeAll($cookie[0]);
            $this->getRememberMeService()->removeCookie();
            $this->setSatisfied(false);

            $e->setCode(AuthenticationResult::FAILURE)
            ->setMessages(array('Possible identity theft detected.'));
            return false;
        }

        $userObject = $this->getUserMapper()->findById($cookie[0]);

        $this->getRememberMeService()->updateSerie($rememberMe);

        // Success!
        $e->setIdentity($userObject->getId());
        $this->setSatisfied(true);
        $storage = $this->getStorage()->read();
        $storage['identity'] = $e->getIdentity();
        $this->getStorage()->write($storage);
        $e->setCode(AuthenticationResult::SUCCESS)
          ->setMessages(array('Authentication successful.'));

        // Reference for weak login. Should not be allowed to change PW etc.
        $session = new \Zend\Session\Container('zfcuser');
        $session->offsetSet("cookieLogin", true);
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
     * @return void
     */
    public function setServiceManager(ServiceManager $serviceManager)
    {
        $this->serviceManager = $serviceManager;
    }

    public function setRememberMeMapper($rememberMeMapper)
    {
        $this->rememberMeMapper = $rememberMeMapper;
    }

    public function getRememberMeMapper()
    {
        if (null === $this->rememberMeMapper) {
            $this->rememberMeMapper = $this->getServiceManager()->get('goaliorememberme_rememberme_mapper');
        }
        return $this->rememberMeMapper;
    }

    public function setUserMapper($userMapper)
    {
        $this->userMapper = $userMapper;
    }

    public function getUserMapper()
    {
        if (null === $this->userMapper) {
            $this->userMapper = $this->getServiceManager()->get('zfcuser_user_mapper');
        }
        return $this->userMapper;
    }

    public function setRememberMeService($rememberMeService)
    {
        $this->rememberMeService = $rememberMeService;
    }

    public function getRememberMeService()
    {
        if (null === $this->rememberMeService) {
            $this->rememberMeService = $this->getServiceManager()->get('goaliorememberme_rememberme_service');
        }
        return $this->rememberMeService;
    }

    /**
     * Hack to use getStorage to clear cookie on logout
     *
     * @return Storage\StorageInterface
     */
    public function logout()
    {
        $authService = $this->getServiceManager()->get('zfcuser_auth_service');
        $user = $authService->getIdentity();

        $cookie = explode("\n", $this->getRememberMeService()->getCookie());

        if($cookie[0] !== '') {
            $this->getRememberMeService()->removeSerie($user->getId(), $cookie[1]);
            $this->getRememberMeService()->removeCookie();
        }
    }

}
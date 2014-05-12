GoalioRememberMe
================

Version 1.0.0 Created by the goalio UG (haftungsbeschränkt)

Introduction
------------

GoalioRememberMe is an extension module for ZfcUser that provides functionality to
stay logged in on subsequent visits to the site.

Information
-----------
I developed this module to use in our [goalio](http://www.goalio.de) application. There are currently no tests and support
can be a little slow because we are a small company with only two developers. I appreciate any feedback, pull requests are even better.


Requirements
------------

* [Zend Framework 2](https://github.com/zendframework/zf2) (requirement of ZfcUser).
* [ZfcBase](https://github.com/ZF-Commons/ZfcBase) (requirement of ZfcUser).
* [ZfcUser](https://github.com/ZF-Commons/ZfcUser) (1.*).

Features / Goals
----------------

* Add pluggable behaviour to stay logged in [COMPLETE]
* Provide updated login view [COMPLETE]
* Provide examples how to use the cookie information, i.e. differentiate between cookie and regular login [INCOMPLETE]

Installation
------------

### Main Setup

#### With composer

1. Add this project and the requirements in your composer.json:

    ```json
    "require": {
        "goalio/goalio-rememberme": "1.*"
    }
    ```

2. Now tell composer to download ZfcUser by running the command:

    ```bash
    $ php composer.phar update
    ```

#### Post installation

1. Enabling it in your `application.config.php`file.

    ```php
    <?php
    return array(
        'modules' => array(
            // ...
            'ZfcBase',
            'ZfcUser',
            'GoalioRememberMe'
        ),
        // ...
    );
    ```

2. Then Import the SQL schema located in `./vendor/goalio/goalio-rememberme/data/schema.sql`.

### Post-Install: Zend\Db

1. If you do not already have a valid Zend\Db\Adapter\Adapter in your service
   manager configuration, put the following in `./config/autoload/database.local.php`:

        <?php

        $dbParams = array(
            'database'  => 'changeme',
            'username'  => 'changeme',
            'password'  => 'changeme',
            'hostname'  => 'changeme',
        );

        return array(
            'service_manager' => array(
                'factories' => array(
                    'Zend\Db\Adapter\Adapter' => function ($sm) use ($dbParams) {
                        return new Zend\Db\Adapter\Adapter(array(
                            'driver'    => 'pdo',
                            'dsn'       => 'mysql:dbname='.$dbParams['database'].';host='.$dbParams['hostname'],
                            'database'  => $dbParams['database'],
                            'username'  => $dbParams['username'],
                            'password'  => $dbParams['password'],
                            'hostname'  => $dbParams['hostname'],
                        ));
                    },
                ),
            ),
        );

### Post-Install: Doctrine2 ORM
There is an additional module for Doctrine integration [GoalioRememberMeDoctrineORM](https://github.com/goalio/GoalioRememberMeDoctrineORM)

### Usage

Navigate to http://yourproject/user and you should land on a login page.

Options
-------

The RememberMe module has some options to allow you to quickly customize the basic
functionality. After installing, copy
`./vendor/goalio/goalio-rememberme/config/goaliorememberme.global.php.dist` to
`./config/autoload/goaliorememberme.global.php` and change the values as desired.

The following options are available:

- **remember_me_entity_class** - Name of Entity class to use. Useful for using your own
  entity class instead of the default one provided. Default is
  `GoalioRememberMe\Entity\RememberMe`.
- **cookie_expire** - Integer value in seconds when the login cookie should expire.
  Default is `2592000` (30 days).
- **cookie_domain** - String value for the domain this cookie should be set for.
  Default is null.

Security
--------

Having such a cookie for login purposes weakens your application security, as it is possible to
guess those values and they offer a second entry point besides the identity/credential combination
used by default.

In order to reduce this risk precautions have been taken. For example the solutions mentioned in
http://jaspan.com/improved_persistent_login_cookie_best_practice allow to identify if a remember me
token has been used by another person and give the necessary hints to the user (change password etc.).

Customization
-------------

Please comment on any problems with this module or give feedback if anything does not work
Out-of-the-Box. There should not really be any requirement to modify the behaviour, unless
security problems arise, but as I am creative with the use of modules myself, I would be very
interested in hearing what can be done to extend the functionality.

How does it work
----------------

This module adds an additional AuthenticationAdapter to the Process in ZfcUser. If any prior
authentication is successful (i.e. the default) and the user requests to set a cookie, the
adapter will do so and create the necessary updates in the DB to identify the cookie.

On a later visit the presence of the cookie is checked during the bootstrap process of the
module to provide an early entry point to authenticate the user. It is stored in the session
that the login was done via cookie, so certain actions should be prohibited without additional
login (i.e. change password, access payment information etc.).

Acknowledgements
----------------
Daniel Strøm (https://github.com/Danielss89)
for most of the basic work in the cookie adapter etc.

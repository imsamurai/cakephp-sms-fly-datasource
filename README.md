SMSFlySource Plugin
===================
[![Build Status](https://travis-ci.org/imsamurai/cakephp-sms-fly-datasource.png)](https://travis-ci.org/imsamurai/cakephp-sms-fly-datasource) [![Coverage Status](https://coveralls.io/repos/imsamurai/cakephp-sms-fly-datasource/badge.png?branch=master)](https://coveralls.io/r/imsamurai/cakephp-sms-fly-datasource?branch=master) [![Latest Stable Version](https://poser.pugx.org/imsamurai/cakephp-sms-fly-datasource/v/stable.png)](https://packagist.org/packages/imsamurai/cakephp-sms-fly-datasource) [![Total Downloads](https://poser.pugx.org/imsamurai/cakephp-sms-fly-datasource/downloads.png)](https://packagist.org/packages/imsamurai/cakephp-sms-fly-datasource) [![Latest Unstable Version](https://poser.pugx.org/imsamurai/cakephp-sms-fly-datasource/v/unstable.png)](https://packagist.org/packages/imsamurai/cakephp-sms-fly-datasource) [![License](https://poser.pugx.org/imsamurai/cakephp-sms-fly-datasource/license.png)](https://packagist.org/packages/imsamurai/cakephp-sms-fly-datasource)

CakePHP SMSFlySource Plugin with DataSource for [SMS Fly service](http://sms-fly.com/)

## Installation

### Step 1: Clone or download [HttpSource](https://github.com/imsamurai/cakephp-httpsource-datasource)

### Step 2: Clone or download to `Plugin/SMSFlySource`

  cd my_cake_app/app git@github.com:imsamurai/cakephp-sms-fly-datasource.git Plugin/SMSFlySource

or if you use git add as submodule:

	cd my_cake_app
	git submodule add "git@github.com:imsamurai/cakephp-sms-fly-datasource.git" "app/Plugin/SMSFlySource"

then update submodules:

	git submodule init
	git submodule update

### Step 3: Add your configuration to `database.php` and set it to the model
```
:: database.php ::
```
```php
public $smsFly = array(
  'datasource' => 'SMSFlySource.Http/SMSFlySource',
        'host' => 'sms-fly.com/api/api.php',
        'port' => 80
);
```

Then make use model SMSFly

### Step 4: Load plugin
```
:: bootstrap.php ::
```
```php
CakePlugin::load('HttpSource', array('bootstrap' => true, 'routes' => false));
CakePlugin::load('SMSFlySource');

```

#Documentation

Please read [HttpSource Plugin README](https://github.com/imsamurai/cakephp-httpsource-datasource/blob/master/README.md)

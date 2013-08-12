#Librejo

Librejo is a work-in-progress PHP-Tent-Library using [Composer](http://getcomposer.org).

Librejo is compatible with Tent 0.3 only, older versions of the Tent protocol are not compatible.

##Installation

**If you already have Composer:**

```composer install --dev```

**If you need Composer:**

```curl -sS https://getcomposer.org/installer | php
php composer.phar install --dev```

**Require the autoloader in your php file**

```require_once __DIR__.'/vendor/autoload.php'```

As Librejo is still work in progress, there is no documentation or API reference, that is to be done once all the basic functions work as one would expect. For the meantime you can have a look at the [example directory](https://github.com/Cacauu/librejo/tree/master/examples) which contains example for most of the implemented features.

##To Do

* OAuth

* Check Registration

* Get Posts

* Get Post

* Post Post

Librejo is released under BSD 3.0 license, see [LICENSE.txt](https://github.com/cacauu/librejo/blob/master/LICENSE.txt) for more information.
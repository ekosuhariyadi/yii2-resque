# yii2-resque
yii2-resque is yii2 component that wrap process of mjphaynes's [php-resque](https://github.com/sprytechies/yii2-resque).
php-resque is a Redis-backed PHP library for creating background jobs, placing them on multiple queues, and processing them later.

---

#### Contents ####

* [Background](#background)
* [Requirements](#requirements)
* [Installation](#installations)
* [Jobs](#jobs)

---

## Background ##

Inspired by sprytechies's [yii2-resque](https://github.com/sprytechies/yii2-resque) but we are using mjphaynes's [php-resque](https://github.com/sprytechies/yii2-resque)
instead of chrisboulton's [php-resque](https://github.com/chrisboulton/php-resque).

The reason behind using mjphaynes/php-resque can be found at [here](https://github.com/mjphaynes/php-resque/blob/master/README.md#background)


## Requirements ##

You must have the following installed in order to run:

* [Redis](http://redis.io/)
* [PHP 5.4+](http://php.net/)
* [PCNTL PHP extension](http://php.net/manual/en/book.pcntl.php)


## Installations ##

1.  The preferred way to install this extension is through [composer](http://getcomposer.org/download/).

    Add

    ```
    "repositories":[
            {
                "type": "git",
                "url": "https://github.com:ekosuhariyadi/yii2-resque.git"
            }
        ],
    ```

    Then

    Either run

    ```
    php composer.phar require --prefer-dist codeimpact/yii2-resque "dev-master"
    ```

    or add

    ```
    "codeimpact/yii2-resque": "dev-master"
    ```

    to the require section of your `composer.json` file.
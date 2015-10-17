# yii2-resque
yii2-resque is yii2 component that wrap process of mjphaynes's [php-resque](https://github.com/sprytechies/yii2-resque).
php-resque is a Redis-backed PHP library for creating background jobs, placing them on multiple queues, and processing them later.

---

#### Contents ####

* [Background](#background)
* [Requirements](#requirements)
* [Installation](#installation)
* [Jobs](#jobs)
    * [Defining Jobs](#defining-jobs)
    * [Queueing Jobs](#queueing-jobs)
    * [Delaying Jobs](#delaying-jobs)
    * [Job Statuses](#job-statuses)

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


## Installation ##

1.  The preferred way to install this extension is through [composer](http://getcomposer.org/download/).

    Add

    ```
    "repositories":[
        {
            "type": "git",
            "url": "https://github.com/ekosuhariyadi/yii2-resque.git"
        }
    ]
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

2. Copy `vendor/codeimpact/yii2-resque/config.yml` to folder where your project's composer.json exists.

3. And last add the following code to `common/config/main-local.php`

```php
return [
    //....
    'components' => [
        //....
        'resque' => [
            'class' => 'codeimpact\yii2resque\Yii2Resque',
        ],
    ],
];
```

## Jobs ##

### Defining Jobs ###

Each job should be in it's own class, and implement interface `codeimpact\yii2resque\BaseJob`
and at least override `perform` method.
```php
namespace common\jobs;

class EchoJob implements \codeimpact\yii2resque\BaseJob
{

    public function setUp()
    {
        // Set up environment for this job
    }

    public function perform($args)
    {
        echo "Hello job\n";
    }

    public function tearDown()
    {
        // Remove environment for this job
    }
}
```

When the job is run, the class will be instantiated and any arguments will be sent as
arguments to the perform method.

Any exception thrown by a job will result in the job failing - be careful here and make
sure you handle the exceptions that shouldn't result in a job failing. If you want to
cancel a job (instead of having it fail) then you can throw a `Resque\Exception\Cancel`
exception and the job will be marked as cancelled.

Jobs can also have `setUp` and `tearDown` methods. If a `setUp` method is defined, it will
be called before the perform method is run. The `tearDown` method if defined, will be
called after the job finishes. If an exception is thrown int the `setUp` method the perform
method will not be run. This is useful for cases where you have different jobs that require
the same bootstrap, for instance a database connection.

To enable autoload job classes, you have to create all job class in either
    common/jobs or {backend,frontend}/jobs directory
and passing the fullname of job class to the queue

### Queueing Jobs ###

To add a new job to the queue use the `runJob` method.

```php
$job = Yii::$app->resque->runJob('common\jobs\EchoJob', array('arg1', 'arg2'));
```

The first argument is the fully resolved classname for your job class.
The second argument is an array of any arguments you want to pass through to the job class.

It is also possible to run a Closure onto the queue. This is very convenient for quick,
simple tasks that need to be queued. When running Closures onto the queue, the `__DIR__`
and `__FILE__` constants should not be used.

```php
$job = Yii::$app->resque->runJob(function($job) {
    echo 'This is a inline job #'.$job->getId().'!';
});
```

It is possible to run a job onto another queue (default queue is called `default`) by passing
through a third parameter to the `runJob` method which contains the queue name.

```php
$job = Yii::$app->resque->runJob('common\jobs\EchoJob', array(), 'myqueue');
```


### Delaying Jobs ###

It is possible to schedule a job to run at a specified time in the future using the `Resque::later`
method. You can do this by either passing through an `int` or a `DateTime` object.

```php
$job = Yii::$app->resque->enqueueJob(60, 'common\jobs\EchoJob', array());
$job = Yii::$app->resque->enqueueJob(1398643990, 'common\jobs\EchoJob', array());
$job = Yii::$app->resque->enqueueJob(new \DateTime('+2 mins'), 'common\jobs\EchoJob', array());
$job = Yii::$app->resque->enqueueJob(new \DateTime('2014-07-08 11:14:15'), 'common\jobs\EchoJob', array());
```

If you pass through an integer and it is smaller than `94608000` seconds (3 years) php-resque will
assume you want a time relative to the current time (I mean, who delays jobs for more than 3 years
anyway??). Note that you must have a worker running at the specified time in order for the job to run.


### Job Statuses ###

php-resque tracks the status of a job. The status information will allow you to check if a job is in the queue, currently being run, failed, etc.
To track the status of a job you must capture the job id of a ran job.

```php
$job = Yii::$app->resque->runJob('common\jobs\EchoJob');
$jobId = $job->getId();
```

To fetch the status of a job:

```php
$status = Yii::$app->resque->getJobStatus($jobId);
```

Job statuses are defined as constants in the Resque\Job class. Valid statuses are:

* `Resque\Job::STATUS_WAITING`   - Job is still queued
* `Resque\Job::STATUS_DELAYED`   - Job is delayed
* `Resque\Job::STATUS_RUNNING`   - Job is currently running
* `Resque\Job::STATUS_COMPLETE`  - Job is complete
* `Resque\Job::STATUS_CANCELLED` - Job has been cancelled
* `Resque\Job::STATUS_FAILED`    - Job has failed
* `false` - Failed to fetch the status - is the id valid?

Statuses are available for up to 7 days after a job has completed or failed, and are then automatically expired.
This timeout can be changed in the configuration file.

## Workers ##

To start a worker navigate to your project root and run:

```
$ ./vendor/bin/yii2resque worker:start
```

Note that once this worker has started, it will continue to run until it is manually stopped.
You may use a process monitor such as [Supervisor](http://supervisord.org/) to run the worker
as a background process and to ensure that the worker does not stop running.

If the worker is a background task you can stop, pause & restart the worker with the following commands:

```
$ ./vendor/bin/yii2resque worker:stop
$ ./vendor/bin/yii2resque worker:pause
$ ./vendor/bin/yii2resque worker:resume
```

The commands take inline configuration options as well as reading from a [configuration file](https://github.com/mjphaynes/php-resque/blob/master/docs/configuration.md#file).

For instance, to specify that the worker only processes jobs on the queues named `high` and `low`, as well as allowing
a maximum of `30MB` of memory for the jobs, you can run the following:

```
$ ./vendor/bin/yii2resque worker:start --queue=high,low --memory=30 -vvv
```

Note that this will check the `high` queue first and then the `low` queue, so it is possible to facilitate job queue
priorities using this. To run all queues use `*` - this is the default value. The `-vvv` enables very verbose
logging. To silence any logging the `-q` flag is used.

For more commands and full list of options please see
the [commands](https://github.com/mjphaynes/php-resque/blob/master/docs/commands.md) documentation.

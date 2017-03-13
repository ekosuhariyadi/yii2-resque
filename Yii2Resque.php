<?php
/**
 * User: echo
 * Date: 10/17/15
 * Time: 4:47 PM
 */

namespace lerence\yii2resque;

use Resque\Job;
use yii\base\Component;

class Yii2Resque extends Component
{

    public function runJob($job, array $data = null, $queue = null)
    {
        return \Resque::push($job, $data, $queue);
    }

    public function enqueueJob($delay, $job, array $data = array(), $queue = null)
    {
        return \Resque::later($delay, $job, $data, $queue);
    }

    public function getJobStatus($jobId)
    {
        $job = Job::load($jobId);
        return $job->getStatus();
    }
}
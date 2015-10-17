<?php

/**
 * Created by PhpStorm.
 * User: echo
 * Date: 10/17/15
 * Time: 5:29 PM
 */
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
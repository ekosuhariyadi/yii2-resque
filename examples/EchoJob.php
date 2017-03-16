<?php

class EchoJob implements \lerence\yii2resque\BaseJob
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
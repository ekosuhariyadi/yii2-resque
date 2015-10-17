<?php
/**
 * Created by PhpStorm.
 * User: echo
 * Date: 10/17/15
 * Time: 5:22 PM
 */

namespace codeimpact\yii2resque;


interface BaseJob
{

    public function setUp();

    public function perform($args);

    public function tearDown();
}
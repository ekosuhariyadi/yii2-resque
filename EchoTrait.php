<?php
/**
 * Created by PhpStorm.
 * User: echo
 * Date: 10/17/15
 * Time: 8:10 PM
 */

namespace codeimpact\yii2resque;


use Monolog\Handler\StreamHandler;
use Monolog\Logger;

trait EchoTrait
{

    private $logger;

    /**
     * EchoTrait constructor.
     */
    public function __construct()
    {
        $this->logger = new Logger("job");
        $this->logger->pushHandler(new StreamHandler("php://output"));
    }

    public function echoes($message)
    {
        $this->logger->info($message);
    }

}
<?php


namespace Shake\Container\Execption;

use Exception;
use Psr\Container\NotFoundExceptionInterface;

/**
 * 找不到服务抛出的异常
 */
class ServiceNotFoundExecption extends Exception implements NotFoundExceptionInterface {

}
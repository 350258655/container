<?php


namespace Shake\Container\Reference;

/**
 * 服务实例和参数实例的抽象类
 */
abstract class AbstractReference {

    private $name;

    public function __construct($name) {
        $this->name = $name;
    }

    /**
     * 获取服务名称
     */
    public function getName(){
        return $this->name;
    }
}
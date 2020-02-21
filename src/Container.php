<?php


use Psr\Container\ContainerInterface;
use ZhengXiaokai\Container\Execption\ContainerExecption;
use ZhengXiaokai\Container\Execption\ParameterNotFoundException;
use ZhengXiaokai\Container\Execption\ServiceNotFoundExecption;
use ZhengXiaokai\Container\Reference\ParameterReference;
use ZhengXiaokai\Container\Reference\ServiceReference;

class Container implements ContainerInterface {

    /**
     * @var 服务实例
     */
    private $services;

    /**
     * @var 参数
     */
    private $parameters;

    /**
     * @var 服务存储
     */
    private $servicesStore;

    public function __construct(array $services = [], array $parameters = []) {
        $this->services = $services;
        $this->parameters = $parameters;
        $this->servicesStore = [];
    }

    /**
     * 从容器中获取服务并返回
     */
    public function get($name) {
        // 找不到服务
        if(!$this->has($name)){
            throw new ServiceNotFoundExecption('Service not found : '.$name);
        }

        if(!isset($this->servicesStore[$name])){
            $this->servicesStore[$name] = $this->createService($name);
        }
        return $this->servicesStore[$name];
    }

    /**
     * 从容器中获取参数并返回
     */
    public function getParameter($name){
        // 由于参数是一个多维数组，规定多个key由 '.' 进行拼接
        $tokens = explode('.', $name);
        $context = $this->parameters;
        // 获取数组中的第一个元素
        while (null !== ($token = array_shift($tokens))){
            if(!isset($context[$token])){
                throw new ParameterNotFoundException('Parameter not found: '.$name);
            }
            $context = $context[$token];
        }
        return $context;
    }


    /**
     * 判断服务是否定义
     */
    public function has($name) {
        return isset($this->services[$name]);
    }

    /**
     * 创建服务,即通过反射来实例化类
     */
    private function createService($name) {
        // &表示引用，即不同的名字访问同一个变量内容
        // $entry中至少需要包含class字段，可选包含arguments和calls
        $entry = &$this->services[$name];
        if(!is_array($entry) || !isset($entry['class'])){
            throw new ContainerExecption($name. ' service entry must be an array containing a \'class\' key');
        }elseif (!class_exists($entry['class'])){
            throw new ContainerExecption($name . ' services class does not exist: ' .$entry['class']);
        }elseif (isset($entry['lock'])){
            // 循环引用了
            throw new ContainerExecption($name. ' services contains a circular reference');
        }

        $entry['lock'] = true;

        $arguments = isset($entry['arguments']) ? $this->resolveArguments( $entry['arguments']) : [];
        
        $reflector = new ReflectionClass($entry['class']);
        // 根据类名和构造参数，实例化类
        $service = $reflector->newInstanceArgs($arguments);
        
        if(isset($entry['calls'])){
            $this->initializeService($service, $name, $entry['calls']);
        }
        return $service;
    }

    /**
     * 根据配置的参数名，获取到参数的值
     */
    private function resolveArguments( $argumentDefinitions) {
        // 存储参数
        $arguments = [];
        // 遍历参数列表
        foreach ($argumentDefinitions as $argumentDefinition){
            // 判断该参数是否属于类
            if($argumentDefinition instanceof ServiceReference){
                // 如果该参数是属于类，那么就获取类名，然后实例化该类，放入到数组中
                $argumentServiceName = $argumentDefinition->getName();
                $arguments[] = $this->get($argumentServiceName);
            }elseif ($argumentDefinition instanceof ParameterReference){
                // 如果该参数是属于普通参数，那么就从参数存储器($parameters)中获取该参数的值，并存储到数组中
                $argumentServiceName = $argumentDefinition->getName();
                $arguments[] = $this->getParameter($argumentServiceName);
            }else{
                $arguments[] = $argumentDefinition;
            }
        }
        return $arguments;
    }

    /**
     * 执行对象的方法
     */
    private function initializeService($service, $name, array $callDefinitions) {
        foreach ($callDefinitions as $callDefinition){
            // $callDefinition 必定是一个数组，包含方法名，和参数。method即方法名必定存在
            if(!is_array($callDefinition) || !isset($callDefinition['method'])){
                throw new ContainerExecption($name. ' service calls must be arrays containing a \'method\' key' );
            }elseif (!is_callable([$service, $callDefinition['method']])){
                // 判断该函数能不能被调用
                throw new ContainerExecption($name. ' service asks for call to uncallable method: '.$callDefinition['method']);
            }

            // 判断该函数是否有参数，有的话则获取该参数
            $arguments = isset($callDefinition['argument']) ? $this->resolveArguments($callDefinition['argument']) : [];

            // 把第一个参数作为回调函数（callback）调用，把参数数组作（param_arr）为回调函数的的参数传入。
            call_user_func_array([$service, $callDefinition['method']], $arguments);
        }
    }
}
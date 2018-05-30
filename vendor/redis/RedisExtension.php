<?php

/**
 * Redis扩展
 * Created by PhpStorm.
 * User: qzc
 * Date: 2017/12/15
 * Time: 下午2:47
 */
namespace Vendor\Redis;

class RedisExtension
{
    // 主机
    protected $_host        = '127.0.0.1';

    // 密码
    protected $_password    = 'fksKDKSDd8@#$SD7SDg';

    // 端口
    protected $_port        = '6379';

    protected $redis;

    public function __construct(){
        $this->redis = new \Redis();
        $this->redis->connect($this->_host,$this->_port);

        if($this->_password){
            $this->redis->auth($this->_password);
        }

    }

    /**
     * 设置redis
     * @param $key
     * @param $value
     */
    public function set($key,$value){
        $this->redis->set($key,$value);
    }

    /**
     * 获取redis
     * @param $key
     * @return bool|string
     */
    public function get($key){
        return $this->redis->get($key);
    }

    /**
     * 判断redis的key是否存在
     * @param $key
     * @return bool
     */
    public function exists($key){
        return $this->redis->exists($key);
    }

    /**
     * 设置过期时间
     * @param $key
     * @param $time 过期时间(秒)
     */
    public function expire($key,$time){
        $this->redis->expire($key,$time);
    }

    /**
     * 关闭连接
     */
    public function close(){
        $this->redis->close();
    }
}
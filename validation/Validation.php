<?php

/**
 * 表单验证类
 * Created by PhpStorm.
 * User: qzc
 * Date: 2017/12/11
 * Time: 下午2:30
 */
namespace Validation;

class Validation
{
    protected $_error;

    protected $_reg = [
        'require'   => '/.+/',
        'email'     => '/^\w+([-+.]\w+)*@\w+([-.]\w+)*\.\w+([-.]\w+)*$/',
        'mobile'    => '/^1[3,4,5,7,8]\d{9}$/',
        'url'       => '/^http(s?):\/\/(?:[A-za-z0-9-]+\.)+[A-za-z]{2,4}(?:[\/\?#][\/=\?%\-&~`@[\]\':+!\.#\w]*)?$/',
        'currency'  => '/^\d+(\.\d+)?$/',
        'number'    => '/^\d+$/',
        'zip'       => '/^\d{6}$/',
        'integer'   => '/^[-\+]?\d+$/',
        'double'    => '/^[-\+]?\d+(\.\d+)?$/',
        'english'   => '/^[A-Za-z]+$/',
        'positive_integer'  => '/^[1-9]{1}\d{0,9}$/', // 正整数
    ];
    //定义验证规则
    protected $_validate = [];

    protected $_data;

    public function __construct($data){
        if(empty($data)){
            $data = $_POST;
        }elseif(is_object($data)){
            $data = get_object_vars($data);
        }
        if(!is_array($data)) {
            $this->_error = 'DATA_TYPE_INVALID';
        }
        $this->_data = $data;
    }

    /**
     * 获得错误信息
     * @return string
     */
    public function getError(){
        return $this->_error;
    }

    /**
     * 检测属性的规则，没有错误，返会true。
     * @param $_validate
     * @return boolean
     */
    public function validate($_validate='_validate') {
        if(empty($_validate) || trim($_validate) == ''){
            $_validate = '_validate';
        }
        $_validate = $this->$_validate;
        foreach ($_validate as $key => $val) {
            if (array_key_exists($val[0], $this->_data) || empty($this->_data)) {
                $r = $this->_validate($val);
                if(!$r)return false;
            }
        }
        return true;
    }

    /**
     * 验证对应属性值
     * @param array $rule
     * @return bool
     */
    private function _validate($rule){
        if(!empty($rule[1])){
            if(array_key_exists($rule[1], $this->_reg)){
                if(!$this->regex($this->_data[$rule[0]], $rule[1])){
                    $this->_error = $rule[2];
                    return false;
                }
                return true;
            }
            switch ($rule[1]){
                case 'length_between':
                    $r = $this->_length($this->_data[$rule[0]], $rule[3], $rule[4]);
                    if($r !== -1){
                        $this->_error = $rule[2];
                        return false;
                    }
                    break;
                case 'in':
                    if(!$this->_in($this->_data[$rule[0]], $rule[3])){
                        $this->_error = $rule[2];
                        return false;
                    }
                    break;
                case 'reg':
                    if(!$this->regex($this->_data[$rule[0]], $rule[3])){
                        $this->_error = $rule[2];
                        return false;
                    }
                    break;
            }
        }
        return true;
    }

    /**
     * 0表示小于$min,1表示大于$max,-1表示在$min和$max 之间
     * @param string $value
     * @param integer $min
     * @param integer $max
     * @return number
     */
    private function _length($value,$min,$max)
    {
        if($value == null || trim($value) == '')
            return -1;
        if(function_exists('mb_strlen'))
            $length = mb_strlen($value);
        else
            $length = strlen($value);
        if(!empty($min) && $length < $min){
            return 0;
        }
        if(!empty($max) && $length > $max){
            return 1;
        }
        return -1;
    }

    /**
     * 检测值是否在指定的集合中
     * @param $value
     * @param array $inArray
     * @return bool
     */
    private function _in($value,$inArray = []){
        if(trim($value) == '') return false;
        if(!is_array($inArray)) return false;
        if(in_array($value,$inArray)){
            return true;
        }else{
            return false;
        }
    }

    /**
     * 使用正则验证数据
     * @access public
     * @param string $value  要验证的数据
     * @param string $rule 验证规则
     * @return boolean
     */
    private function regex($value, $rule) {
        if(trim($value) == ''){
            return false;
        }
        $validate = $this->_reg;
        // 检查是否有内置的正则表达式
        if (isset($validate[strtolower($rule)]))
            $rule = $validate[strtolower($rule)];
        return preg_match($rule, $value) === 1;
    }
}
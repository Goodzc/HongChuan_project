<?php

/**
 * 商户表单验证
 * Created by PhpStorm.
 * User: qzc
 * Date: 2017/12/11
 * Time: 下午4:41
 */
namespace App\Home\Validation;
use Validation\Validation;

class ValidationMerchant extends Validation
{
    /**
     * 新建商户
     */
    protected $createMerchant = [
        ['manager_id','require','管理员ID不能为空'],
        ['merchant_name','require','商户名称不能为空']
    ];

    /**
     * 获取商户列表
     * @var array
     */
    protected $getMerchantList = [
        ['manager_id','require','管理员ID不能为空'],
        ['page','require','页码不能为空'],
        ['page','integer','页码必须为整数'],
    ];

    /**
     * 获取商户信息
     * @var array
     */
    protected $getMerchantInfo = [
        ['manager_id','require','管理员ID不能为空'],
        ['merchant_id','require','商户ID不能为空'],
    ];
}
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

class ValidationUploadServer extends Validation
{
    /**
     * 上传服务器
     */
    protected $uploadStream = [
        ['manager_id','require','管理员ID不能为空'],
    ];

}
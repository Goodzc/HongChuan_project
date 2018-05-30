<?php

/**
 * 表单验证
 * Created by PhpStorm.
 * User: qzc
 * Date: 2017/12/11
 * Time: 下午4:41
 */
namespace App\Home\Validation;
use Validation\Validation;

class ValidationIndex extends Validation
{
    /**
     * 获取出库码列表
     */
    protected $getCpkOutCodeList = [
        ['manager_id','require','管理员ID不能为空'],
        ['out_sheet_id','require','出库编码不能为空'],
        ['out_sheet_id','length_between','出库编码长度为14位',14,14],
    ];

    /**
     * 获取出库箱码商品信息
     */
    protected $getCpkOutCodeGoodsInfo = [
        ['manager_id','require','管理员ID不能为空'],
        ['out_code','require','箱码不能为空'],
        ['out_code','length_between','箱码长度为24位',24,24],
    ];

    /**
     * 获取出库箱码商品信息
     */
    protected $outSheet = [
        ['manager_id','require','管理员ID不能为空'],
        ['out_code','require','箱码不能为空'],
    ];

}
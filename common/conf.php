<?php
/**
 * Created by PhpStorm.
 * User: qzc
 * Date: 2017/12/5
 * Time: 下午5:25
 */
$conf = [
    // 阿里大于短信配置
    'APP_KEY'               => '23446749',
    'SECRET_KEY'            => '5f9446899d3a67031470eb151bc5d5b4',
    'SIGN_NAME'             => '酒商圈',

    // 默认加载
    'DEFAULT_MODULE'        => 'home', // 默认模块
    'DEFAULT_CONTROLLER'    => 'Index', // 默认控制器
    'DEFAULT_ACTION'        => 'index', // 默认方法

    // 检测权限接口,及关键字
    'CHECK_AUTH_API'        => [
        // 公司
        'Company/updateCompany'                 => 'company_edit',                      // 编辑企业信息
        'Company/editCompanyName'               => 'company_edit',                      // 修改企业名称
        'Company/replaceCompanyLogo'            => 'company_edit',                      // 修改企业LOGO
        'Company/dissolveCompany'               => 'company_dissolve',                  // 解散企业
        'Company/removedMember'                 => 'address_member_del',                // 踢除成员

        // 考勤管理
        'Company/createSignInRule'              => 'attendance',                   // 设置考勤规则
        'Company/delSignInRule'                 => 'attendance',                   // 删除考勤规则

        // 库房管理
        'Company/createWareHouse'               => 'storehouse',                    // 创建库房
        'Company/updateWareHouse'               => 'storehouse',                    // 编辑库房
        'Company/delWareHouse'                  => 'storehouse',                    // 删除库房

        // 审批流程
        'Examine/setExamineProcess'             => 'examine_process',               // 设置审批流程

        // 商品管理
        'Goods/addGoods'                        => 'goods',                         // 添加商品
        'Goods/updateGoods'                     => 'goods',                         // 更新商品
        'Goods/deleteGoods'                     => 'goods',                         // 删除商品
        'Goods/addGoodsUnit'                    => 'goods',                         // 新增单位
        'Goods/deleteGoodsUnit'                 => 'goods',                         // 删除商品单位

        // 商户管理
        'Merchant/areaCreate'                   => 'merchant_area',                 // 创建商户区域接口
        'Merchant/areaUpdate'                   => 'merchant_area',                 // 更新商户区域接口
        'Merchant/areaDel'                      => 'merchant_area',                 // 删除商户区域接口
        'Merchant/rankCreate'                   => 'merchant_rank',                 // 创建商户等级接口
        'Merchant/rankUpdate'                   => 'merchant_rank',                 // 更新商户等级接口
        'Merchant/rankDel'                      => 'merchant_rank',                 // 删除商户等级接口
        'Merchant/editMerchant'                 => 'merchant_update',               // 编辑商户
        'Merchant/delMerchant'                  => 'merchant_delete',               // 删除商户
        'Merchant/merchantCreate'               => 'merchant_create',               // 新建商户

        // 公告管理
        'Notice/createNotice'                   => 'notice',                        // 发布公告接口
        'Notice/updateNotice'                   => 'notice',                        // 更新公告

        // 宴席记录
        'Banquet/delHotel'                      => 'address_hotel_del',             // 删除酒店

        // 订单
//        'Order/orderExamine'                    => 'order_examine',                 // 订单审批
        'Order/create'                          => 'order_create',                    // 下单接口
        'Order/leaveStorehouse'                 => 'order_leave_storehouse',          // 出库接口
        'Order/orderCancel'                     => 'order_cancel',                    // 订单取消
        'Order/orderConfirmPay'                 => 'order_update',                    // 订单确认付款接口

        // 角色
        'Roles/createRoles'                     => 'role_manage',                   // 创建角色
        'Roles/updateRoles'                     => 'role_manage',                   // 更新角色
        'Roles/delRoles'                        => 'role_manage',                   // 删除角色
        'Roles/setRolesMembers'                 => 'role_manage',                   // 设置角色成员
        'Roles/setRolesAuth'                    => 'auth_setting',                  // 设置角色权限
    ],
];

return $conf;
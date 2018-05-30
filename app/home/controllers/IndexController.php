<?php
/**
 * Created by PhpStorm.
 * User: qzc
 * Date: 2018/5/22
 * Time: 下午3:22
 */

namespace app\home\controllers;


use app\home\models\Index;
use App\Home\Validation\ValidationIndex;

class IndexController extends BaseController
{
    private static $indexModel;
    public function __construct()
    {
        parent::__construct();
        self::$indexModel = new Index();
    }

    /**
     * 查询出库记录
     */
    public function getCpkOutList(){
        $where = [];
        // 是否选择时间
        $startTime  = getHttpParam('start_time');
        $endTime    = getHttpParam('end_time');
        if($startTime && $endTime){
            $where[] = ['a.OUT_TIME','>=',strtotime(date(self::$dateFormat,strtotime($startTime)))];
            $where[] = ['a.OUT_TIME','<',strtotime(date(self::$dateFormat,strtotime('+1 day',strtotime($endTime))))];
        }

        // 是否输入条码
        if($outCode = getHttpParam('out_code')){
            if(strlen($outCode) < 18) sendJson(false,40201);
            $codeSplit  = splitOutCode($outCode);
            $where[]    = ['KIND_ID','=',$codeSplit['kindId']];
            $where[]    = ['DEGREE_ID','=',$codeSplit['degreeId']];
            $where[]    = ['CAPACITY_ID','=',$codeSplit['capacityId']];
            $where[]    = ['SPEC_ID','=',$codeSplit['specId']];
            $where[]    = ['BOX_ID','=',$codeSplit['boxId']];
        }

        if($where){
            $list = self::$indexModel->searchCpkOutSheet($where);
        }else{
            $list = self::$indexModel->getCpkOutSheet($where,[1,2]);
        }

        foreach($list as $k => &$v){
            $v['OUT_TIME'] = date('Y-m-d H:i:s',$v['OUT_TIME']);
        }
        sendJson(true,10000,'',$list);
    }

    /**
     * 获取出库商品统计
     */
    public function getCpkOutCodeStatistics(){
        // 检测参数
        $rule = [
            'manager_id'    => $this->currManagerId,
            'out_sheet_id'  => getHttpParam('out_sheet_id'),
        ];
        $check      = new ValidationIndex($rule);
        $isSuccess  = $check->validate('getCpkOutCodeList');
        if(!$isSuccess) sendJson(false,10001,$check->getError());

        $outSheetId = $rule['out_sheet_id'];

        // 获取当前出库编码的所有箱码
        $list = self::$indexModel->getCpkOutCodeStatistics($outSheetId);

        sendJson(true,10000,'',$list);
    }

    /**
     * 获取出库码列表
     */
    public function getCpkOutCodeList(){
        // 检测参数
        $rule = [
            'manager_id'    => $this->currManagerId,
            'out_sheet_id'  => getHttpParam('out_sheet_id'),
        ];
        $check      = new ValidationIndex($rule);
        $isSuccess  = $check->validate('getCpkOutCodeList');
        if(!$isSuccess) sendJson(false,10001,$check->getError());

        $outSheetId = $rule['out_sheet_id'];

        // 获取当前出库编码的所有箱码
        $list = self::$indexModel->getCpkOutCode($outSheetId);

        sendJson(true,10000,'',$list);
    }

    /**
     * 获取出库码商品信息
     */
    public function getCpkOutCodeGoodsInfo(){
        // 检测参数
        $rule = [
            'manager_id'    => $this->currManagerId,
            'out_code'      => getHttpParam('out_code'),
        ];
        $check      = new ValidationIndex($rule);
        $isSuccess  = $check->validate('getCpkOutCodeGoodsInfo');
        if(!$isSuccess) sendJson(false,10001,$check->getError());

        $outCode = $rule['out_code'];

        // 获取当前出库箱码商品信息
        $codeSplit  = splitOutCode($outCode);
        $info = self::$indexModel->getOutCodeGoodsInfo($codeSplit);
        $info['out_status'] = 0;
        $info['kind']       = $codeSplit['kindId'];
        $info['box_code']   = $outCode;
        $info['sweep_time'] = date('Y-m-d H:i:s',$this->currTime);

        // 检测箱码是否已经出过库
        $where = [
            ['BOX_CODE','=',$outCode]
        ];
        $outedCode = self::$indexModel->getCpkOutCodeList('BOX_CODE',$where);
        if($outedCode) $info['out_status'] = 1;
        sendJson(true,10000,'',$info);
    }

    /**
     * 出库
     */
    public function outSheet(){
        // 检测参数
        $rule = [
            'manager_id'    => $this->currManagerId,
            'merchant_id'   => getHttpParam('merchant_id'),
            'out_code'      => getHttpParam('out_code'),
        ];
        $check      = new ValidationIndex($rule);
        $isSuccess  = $check->validate('outSheet');
        if(!$isSuccess) sendJson(false,10001,$check->getError());

        $merchantId = $rule['merchant_id'];
        // 检测出库码格式是否正确
        $codeList   = json_decode(stripslashes($rule['out_code']),true);
        if(!$codeList) sendJson(false,40208);
        $codeList1  = array_column($codeList,'code');
        $outTime    = array_column($codeList,'out_time');
        if(!$codeList1 || !$outTime || count($codeList1) != count($outTime) || count($codeList1) != count($codeList)){
            sendJson(false,40208);
        }

        // 检测箱码是否重复
        $uniqueArr = array_unique($codeList1);
        if(count($uniqueArr) != count($codeList1)){
            // 获取重复数据
            $repeatArr = array_diff_assoc($codeList1,$uniqueArr);
            sendJson(false,10001,'箱码【'.implode(',',$repeatArr).'】重复',[]);
        }

        // 检测箱码是否已经出过库
        $where = [
            ['BOX_CODE','in',implode("','",$codeList1)]
        ];
        $outedCode = self::$indexModel->getCpkOutCodeList('BOX_CODE',$where);
        if($outedCode){
            $outCode = array_column($outedCode,'BOX_CODE');
            sendJson(false,10001,'箱码:'.implode(',',$outCode).'已经出库,不可重复出库',[]);
        }

        // 检测商户是否存在
        $merchantInfo = self::$indexModel->getMerchantInfo('ID',['ID' => $merchantId]);
        if(!$merchantInfo) sendJson(false,40202);

        // 添加出库操作
        self::$indexModel->startTrans();
        $data = [
            'ID'            => $this->createOutSheetId(),
            'DEALER'        => $merchantId,
            'CPK_ID'        => $this->currManagerInfo['cpk_id'],
            'OPERATOR'      => $this->currManagerInfo['manager_name'],
            'BOX'           => count($codeList),
            'OUT_TIME1'     => date('Y-m-d H:i:s',$this->currTime),
            'OUT_TIME'      => $this->currTime,
            'MONEY'         => 0,
        ];
        $isSuccess = self::$indexModel->createOutSheet($data);
        if(!$isSuccess){
            self::$indexModel->rollback(); // 回滚事务
            sendJson(false,40203);
        }

        // 批量生成出库码数据
        $outCode = [];$outCodeGoods = [];
        foreach($codeList as $v){
            if(strlen($v['code']) != 24) {
                self::$indexModel->rollback(); // 回滚事务
                sendJson(false,40204);
            }
            $splitOutCode = splitOutCode($v['code']);
            $outCode[] = [
                'SHEET_ID'      => $data['ID'],
                'DEALER_ID'     => $merchantId,
                'MACHINE_ID'    => $splitOutCode['machineId'],
                'KIND_ID'       => $splitOutCode['kindId'],
                'DEGREE_ID'     => $splitOutCode['degreeId'],
                'CAPACITY_ID'   => $splitOutCode['capacityId'],
                'SPEC_ID'       => $splitOutCode['specId'],
                'BOX_ID'        => $splitOutCode['boxId'],
                'BOTTLE_ID'     => $splitOutCode['bottleId'],
                'RAND_NUM'      => $splitOutCode['randNum'],
                'OUT_TIME1'     => $v['out_time'],
                'OUT_TIME'      => strtotime($v['out_time']),
                'BOX_CODE'      => $v['code'],
            ];

            // 出库码商品分类
            $tmpKeyArr  = [$splitOutCode['kindId'],$splitOutCode['degreeId'],$splitOutCode['capacityId'],$splitOutCode['specId']];
            $tmpKey     = implode('-',$tmpKeyArr);
            if(isset($outCodeGoods[$tmpKey])){
                $outCodeGoods[$tmpKey] += 1;
            }else{
                $outCodeGoods[$tmpKey] = 1;
            }

        }

        if(!$outCode || !$outCodeGoods){
            self::$indexModel->rollback(); // 回滚事务
            sendJson(false,40207);
        }

        // 添加出库箱码
        $isSuccess = self::$indexModel->createOutCodeAll($outCode);
        if(!$isSuccess){
            self::$indexModel->rollback(); // 回滚事务
            sendJson(false,40205);
        }

        // 添加出库码商品分类
        $goodsData = [];
        foreach($outCodeGoods as $k => $v){
            $tmp = explode('-',$k);
            $goodsData[] = [
                'SHEET_ID'  => $data['ID'],
                'KIND'      => $tmp[0],
                'DEGREE'    => $tmp[1],
                'CAPACITY'  => $tmp[2],
                'SPEC'      => $tmp[3],
                'BOX'       => $v,
                'PRICE'     => 0,
                'MONEY'     => 0,
            ];
        }

        $isSuccess = self::$indexModel->createOutCodeDataAll($goodsData);
        if(!$isSuccess){
            self::$indexModel->rollback(); // 回滚事务
            sendJson(false,40206);
        }

        self::$indexModel->commit(); // 回滚事务
        sendJson(true,40209);
    }

    /**
     * 生成出库编码
     * @return string
     */
    public function createOutSheetId(){
        // 获取当天出库次数
        $currDateStartTime  = strtotime(date(self::$dateFormat,$this->currTime));
        $currDateEndTime    = strtotime('+1 day',$currDateStartTime);
        $where = [
            ['OUT_TIME','>=',$currDateStartTime],
            ['OUT_TIME','<',$currDateEndTime],
        ];
        $num = self::$indexModel->getOutCpkNum($where);
        $num += 1;
        // 生成出库编号
        $lastNum = '';
        for($i = 0;$i < 4 - strlen($num);$i ++){
            $lastNum .= '0';
        }

        $outSheetId = $this->currManagerInfo['cpk_id'] . date('Ymd') . $lastNum . $num;
        return $outSheetId;
    }

    /**
     * 获取最后一次同步服务器的时间
     */
    public function lastUploadServerTime(){
        $where = [
            ['UPLOAD_TIME','>',0],
        ];
        $info = self::$indexModel->getCpkOutSheet($where,[1,1],'a.UPLOAD_TIME desc');
        if($info){
            $lastUploadTime = date('Y-m-d H:i:s',$info[0]['UPLOAD_TIME']);
        }else{
            $lastUploadTime = '';
        }

        sendJson(true,10000,'',['upload_time' => $lastUploadTime]);
    }
}
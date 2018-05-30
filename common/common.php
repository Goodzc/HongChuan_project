<?php
/**
 * 公共方法
 * Created by PhpStorm.
 * User: qzc
 * Date: 2017/12/5
 * Time: 下午3:54
 */

/**
 * api发送json数据
 * @param bool $status
 * @param int $errCode 错误码
 * @param string $errMsg 要返回的错误信息（如有错误码则无需）
 * @param array $data 返回数据
 */
function sendJson($status = true,$errCode = 10000,$errMsg = '',$data = []){
    if(is_string($data)){
        $data = [$data];
    }

    // 获取错误信息
    if($errMsg == ''){
        $errMsg_ = getErrMsg($errCode);
        $errMsg  = $errMsg_ ? $errMsg_ : getErrMsg();
    }

    // 组织返回数据
    $response = [
        'result'    => ($status === true) ? 'success' : 'fail',
        'data'      => $data,
        'msg'       => $errMsg,
    ];

    echo json_encode($response);exit;
}

/**
 * 获取错误信息
 * @param int $code 错误码
 * @return string 错误信息
 */
function getErrMsg($code = 40004){
    $errMsg = require_once __DIR__.'/../validation/errormsg.php';

    $msg = '';
    if(isset($errMsg[$code])){
        $msg = $errMsg[$code];
    }
    return $msg;
}

/**
 * 生成32位唯一ID
 * @return string
 */
function getUniqueId(){
    return md5(uniqid(mt_rand(),true));
}

/**
 * 检测主机是否是生产环境
 * @return bool
 */
function checkCurrHostIsProduct(){
    $host = [
        'wlc3000.com',
        'www.wlc3000.com',
    ];

    if(in_array($_SERVER['HTTP_HOST'],$host)){
        return true;
    }else{
        return false;
    }
}

/**
 * 检测主机是否测试环境
 * @return bool
 */
function checkCurrHostIsTest(){
    $host = [
        'ali.wlc3000.com',
        'dev.zgzlnet.com',
    ];
    if(in_array($_SERVER['HTTP_HOST'],$host)){
        return true;
    }else{
        return false;
    }
}

/**
 * 过滤数据
 */
function filterData(){
    if (!get_magic_quotes_gpc()) {
        !empty($_POST)    && filterData_($_POST);
        !empty($_GET)     && filterData_($_GET);
        !empty($_COOKIE)  && filterData_($_COOKIE);
        !empty($_SESSION) && filterData_($_SESSION);
    }
    !empty($_FILES) && filterData_($_FILES);
}
function filterData_(&$array){
    if (is_array($array)) {
        foreach ($array as $key => $value) {
            if($value === '' || $value === null){
                unset($array[$key]);
            }
//            if (!is_array($value)) {
//                $array[$key] = addslashes($value);
//            } else {
//                filterData_($array[$key]);
//            }
        }
    }
}

/**
 * 获取http请求参数
 * @param string $key 参数
 * @return mixed|string
 */
function getHttpParam($key = ''){
    $requestMethod = 'get';
    if(isset($_SERVER['REQUEST_METHOD'])){
        $requestMethod = strtolower($_SERVER['REQUEST_METHOD']);
    }

    switch($requestMethod){
        case 'get':
            $data = $_GET;break;
        case 'post':
            $data = $_POST;break;
        default:
            $data = isset($GLOBALS["HTTP_RAW_POST_DATA"]) ? $GLOBALS["HTTP_RAW_POST_DATA"] : [];
    }

    if($key){
        return isset($data[$key]) ? stripslashes($data[$key]) : '';
    }else{
        return $data;
    }
}

/**
 * 重定向
 * @param string $url
 */
function redirect($url = ''){
    header('Location: '. $url); exit;// 重定向
}

/**
 * 获取系统配置
 * @param string $key
 * @return array|mixed
 */
function getConf($key = ''){
    return isset($GLOBALS['CONF'][$key]) ? $GLOBALS['CONF'][$key] : $GLOBALS['CONF'];
}

/**
 * 获取字符串中的数字
 * @param string $str
 * @return string
 */
function findNum($str=''){
    $str = trim($str);
    if(empty($str)) return '';
    $result = '';
    for($i = 0;$i < strlen($str);$i ++){
        if(is_numeric($str[$i])) $result .= $str[$i];
    }
    return $result;
}

/**
 * 创建临时文件
 * @param string $fileName
 * @param string $str
 */
function createTmpFile($fileName = 'xxx.txt',$str = ''){
    $handle = fopen('./tmp/' . $fileName,'w');
    fwrite($handle,$str);
    fclose($handle);
}

/**
 * 分割出库码
 * @param $code
 * @return array
 */
function splitOutCode($code){
    $randNum      = substr($code,-24,6);
    $machineId    = substr($code,-18,2);
    $kindId       = substr($code,-16,2);
    $degreeId     = substr($code,-14,1);
    $capacityId   = substr($code,-13,1);
    $specId       = substr($code,-12,1);
    $boxId        = substr($code,-11,9);
    $bottleId     = substr($code,-2,2);
    return [
        'randNum'       => $randNum,
        'machineId'     => $machineId,
        'kindId'        => $kindId,
        'degreeId'      => $degreeId,
        'capacityId'    => $capacityId,
        'specId'        => $specId,
        'boxId'         => $boxId,
        'bottleId'      => $bottleId,
    ];
}

/**
 * 获取数字字符递增ID
 * @param $code
 * @param int $length 字符串长度
 * @return string
 */
function getNumberCharIncreaseId($code,$length = 4){
    $tmp = '';
    for($i = 0;$i < $length - strlen($code);$i ++){
        $tmp .= '0';
    }
    return $tmp.$code;
}



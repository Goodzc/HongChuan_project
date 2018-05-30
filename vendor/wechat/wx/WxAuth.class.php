<?php
/**
 * 微信oAuth认证示例
 * 官方文档：http://mp.weixin.qq.com/wiki/17/c0f37d5704f0b64713d5d2c37b468d75.html
 * UCToo示例:http://git.oschina.net/uctoo/uctoo/blob/master/Addons/Ucuser/UcuserAddon.class.php
 *
 * 微信oAuth认证类
 * @命名空间版本
 * @author uctoo (www.uctoo.com)
 * @date 2015-5-15 14:10
 */

class WxAuth {
    protected $options;
    protected $open_id;
    protected $wxuser;
    protected $weObj;
    protected $scope = 'snsapi_base';
	
	public function __construct($options){
        require_once __DIR__ . '/../wechat.class.php';
		$this->options = $options;
		$this->wxoauth();
	}

    public function wxoauth(){
        $code = isset($_GET['code']) ? $_GET['code'] : '';
        $tokenTime = isset($_SESSION['token_time']) ? $_SESSION['token_time'] : 0;
        if(!$code && isset($_SESSION['open_id']) && isset($_SESSION['user_token']) && $tokenTime > time() - 3600)
        {
            if (!$this->wxuser && isset($_SESSION['wxuser'])) {
                $this->wxuser = $_SESSION['wxuser'];
            }
            $this->open_id = $_SESSION['open_id'];
            return $this->open_id;
        }
        else
        {
            $weObj = new Wechat($this->options);
            if ($code) {
                $json = $weObj->getOauthAccessToken();
                // code被使用或过期则重新获取
                if (!$json) {
                    Header('Location: ' . $this->getRedirectUri());exit;
                }

                $_SESSION['open_id']    = $this->open_id = $json["openid"];
                $access_token           = $json['access_token'];
                $_SESSION['user_token'] = $access_token;
                $_SESSION['token_time'] = time();

                // 静默授权
                $userInfo = $weObj->getUserInfo($this->open_id);
                if ($userInfo && !empty($userInfo['nickname'])) {
                    $this->setWxUser($userInfo);
                }elseif(strstr($json['scope'],'snsapi_userinfo') !== false){
                    // 显示授权
                    $userInfo = $weObj->getOauthUserinfo($access_token,$this->open_id);
                    if ($userInfo && !empty($userInfo['nickname'])) {
                        $this->setWxUser($userInfo);
                    } else {
                        return $this->open_id;
                    }
                }else{
                    // 如果静默授权未获到用户信息
                    $this->scope = 'snsapi_userinfo';
                }

                if ($this->wxuser) {
                    $_SESSION['wxuser']  = $this->wxuser;
                    $_SESSION['open_id'] =  $json["openid"];
                    return $this->open_id;
                }
            }

            // 获取授权code
            $url = $this->getRedirectUri();
            $oauthUrl = $weObj->getOauthRedirect($url,"wxbase",$this->scope);
            Header("Location: $oauthUrl");exit;
        }
    }

    /**
     *  设置微信信息
     * @param $userInfo
     */
    public function setWxUser($userInfo){
        $this->wxuser = array(
            'subscribe' => isset($userInfo['subscribe']) ? $userInfo['subscribe'] : 0,
            'open_id'   => $this->open_id,
            'unionid'   => isset($userInfo['unionid']) ? $userInfo['unionid'] : '',
            'nickname'  => $userInfo['nickname'],
            'sex'       => intval($userInfo['sex']),
            'location'  => $userInfo['province'] . '-' . $userInfo['city'],
            'avatar'    => $userInfo['headimgurl']
        );
    }

    /**
     * 获取微信信息
     * @return mixed
     */
    public function getWxUser(){
        return $this->wxuser;
    }

    /**
     * 获取当前请求路径
     * @return string
     */
    public function getRedirectUri(){
        return $_SERVER['REQUEST_SCHEME']. '://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
    }
}

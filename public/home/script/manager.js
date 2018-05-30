/**
 * 管理员类
 * Created by qzc on 2018/5/26.
 */
function Manager(){
    this.managerInfo = this.checkLoginIn();
}

Manager.prototype = {
    /**
     * 检测登录
     */
    checkLoginIn : function(){
        var localStorage = window.localStorage;
        var info = localStorage.getItem('MANAGER_INFO');
        if(info && Object.keys(info).length){
            return JSON.parse(info);
        }else{
            window.location.href = '/home/html/login.html';
        }
    },

    /**
     * 退出登录
     */
    loginOut : function(){
        var localStorage = window.localStorage;
        localStorage.setItem('MANAGER_INFO','');
        window.location.href = '/home/html/login.html';
    }
};

var Manager = new Manager();
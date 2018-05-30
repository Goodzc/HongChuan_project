function login() {
    var url = baseHost + '?c=Login&a=loginIn';
    var user_name = $('#signin-email').val();
    var password = $('#signin-password').val();
    var param = {
        account: user_name,
        password: password
    };
    common_ajax('post', url, param, false, true, function (data) {
        if (data.result == 'success') {
            window.localStorage.setItem('MANAGER_INFO',JSON.stringify(data.data));
            swal({
                title: "登录成功!",
                text: data.msg,
                type: "success",
                timer: '2000',
                showConfirmButton: false
            });
            setTimeout(function () {
                page_to_other('/home/html/home_page.html');
            },2000);

        } else {
            swal({
                title: "登录失败!",
                text: data.msg,
                type: "error",
                showConfirmButton: true
            });
        }
    })
}

// 检测是否已经登录
var localStorage = window.localStorage;
var managerInfo = localStorage.getItem('MANAGER_INFO');
if(managerInfo && Object.keys(JSON.parse(managerInfo)).length){
    // 已经登录则跳转到首页
    page_to_other('/home/html/home_page.html');
}
// 接口访问根地址
var baseHost = window.location.protocol + '//' + window.location.host;

/**
 * 公共ajax
 * @param type
 * @param url
 * @param data
 * @param checktoken
 * @param async
 * @param success_callback
 * @returns {boolean}
 */
function common_ajax(type, url, data, checktoken, async, success_callback) {
    if (typeof(data) == "string") {
        if (checktoken) {
            var param = token_($('#' + data).serializeArray());
        } else {
            var param = $('#' + data).serializeArray();
        }

    } else if (typeof(data) == "object") {
        if (checktoken) {
            var param = arr_(data);
        } else {
            var param = data;
        }

    } else {
        return false;
    }
    $.ajax({
        cache: true,
        type: type,
        url: url,
        data: param,
        dataType: "json",
        async: async,
        error: function () {
            swal({
                title: "警告!",
                text: "请输入用户名或密码",
                type: "warning",
                showConfirmButton: false
            });
            // close_loading_animation('网络链接错误'); // 关闭loading框
        },
        success: function (para) {
            // if (check_real_name(para))return;
            // if (check_company(para))return;
            success_callback && success_callback(para);
        }
    });
}
function page_to_other(url) {
    window.location.href = baseHost + url;
}

/**
 * 数组删除指定元素
 * @param val
 */
Array.prototype.remove = function(val) {
    for(var i=0; i<this.length; i++) {
        if(this[i] == val) {
            this.splice(i, 1);
            break;
        }
    }
}

/**
 * 当前时间
 * @param date_str
 * @param time_str
 * @returns {{date_format: string, time_format: string}}
 */
function timestamp_format_date(date_str,time_str)   {
    var now =new Date();
    var year    = now.getFullYear();
    var month   = now.getMonth()+1;
    var date    = now.getDate();
    var hour    = now.getHours();
    var minutes  = now.getMinutes();
    var seconds  = now.getSeconds();
    if(month < 10) month = '0' + month;
    if(minutes < 10) minutes = '0' + minutes;
    if(seconds < 10) seconds = '0' + seconds;
    var date_format = year + '年' + month + '月' + date + '日';
    var time_format = hour + '时' + minutes + '分' + seconds + '秒';
    if(date_str){
        date_format = year + date_str + month + date_str + date;
    }
    if(time_str){
        time_format = hour + time_str + minutes + time_str + seconds;
    }
    return   {date_format : date_format,time_format : time_format};
}

/**
 * 时钟
 * @param obj
 * @private
 */
function timer_(obj){
    setInterval(function(){
        var timeFormat = timestamp_format_date('-',':');
        obj.text(timeFormat.date_format +' '+ timeFormat.time_format);
    },1000)
}
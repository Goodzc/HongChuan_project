//用户id
var id = Manager.managerInfo.id;

// 商户搜索框
function changeShow(el) {
    el.removeClass('on').siblings().addClass('on').focus();
}

//全局arr
var goods_infomation =  [];
//    未保存数量
var unsave_code_num = 0;
//商户
var company_id = '';

// 管理员信息
$('#warehouse').text('库房：'+Manager.managerInfo.cpk_name);
$('#manager-name').text('出库人：'+Manager.managerInfo.manager_name);
/**
 * 时钟
 */
timer_($('#timer'));


/**
 * 服务器同步时间
 */
lastUploadServerTime();
function lastUploadServerTime(){
    var url = baseHost + '?c=Index&a=lastUploadServerTime';
    var param = {};
    common_ajax('post', url, param, false, true, function (data) {
        if(data.result == 'success'){
            var uploadTime = '尚未同步';
            if(data.data.upload_time){
                uploadTime = data.data.upload_time;
            }
            $('#last-upload-time').text('上次同步时间：'+uploadTime);
        }else{
            swal({
                title: "获取服务器同步失败!",
                text: data.msg,
                type: "error",
                showConfirmButton: true
            });
        }
    });
}

/**
 * 上传服务器
 */
function uploadServer(){
    swal({
        title : "敬请期待!",
        text : '程序员同学正在加班加点开发中☺~',
        type : "info",
        showConfirmButton : false
    });
}

/***
 *
 * 商户切换
 *
 * */
//切换隐藏
function show_title(el) {
    checkIsSearch(0); // 检测是否是搜索
   /* if($('.unsaved').text() !=0){
        swal({
            title : '确认切换商户？',
            text  : '有待保存的条码',
            type  : 'warning',
            showConfirmButton : true
        })
    }*/
    if (el.hasClass('row')) {
        $('.content_company').addClass('on');
        $('.search_show').removeClass('on');
    }
    $('.code_input').css('display', 'block');
    $('.content_head').css('display', 'none');
    if (el.hasClass('bg_color')) {
        el.removeClass('bg_color');
        $('.code_input').css('display', 'none');
        $('.content_head').css('display', 'block');
        window.location.reload();
    } else {
        el.addClass('bg_color').siblings().removeClass('bg_color');
    }
    company_id = el.find('.company_id').text();
    var company_name = el.find('.company_name').text();
    $('.store_name').text(company_name);

    //    清空条码和信息框
    $('.code_statistics').empty();
    $('.code_list').empty();
//清空合计框
    $('.goods-box_count').empty();
    $('.total_piece').text('0');
    $('.saved_code').text('0');
    $('.unsaved').text('0');
//
    var local_code  = window.localStorage.getItem('code'+company_id);
    var local_goods = window.localStorage.getItem('goodsInfor'+company_id);
    var local_boxCode = window.localStorage.getItem('box-code'+company_id);// 当前商户已存箱码
    goods_infomation = [];
    goods_infomation['code_list'+company_id] = local_code ? JSON.parse(local_code) : [] ;
    goods_infomation['kind'+company_id] =local_goods ? JSON.parse(local_goods) : {};
    goods_infomation['box-code'+company_id] =local_boxCode ? JSON.parse(local_boxCode) : [];
    getLocalCode(goods_infomation['code_list'+company_id],goods_infomation['kind'+company_id]);
//扫码
    $("#barCode").startListen({
        barcodeLen: 24,
        letter: false,
        number: true,
        show: function (code) {
            add_code(code);
        }
    });
}
function add_code(code) {
    // layer.msg(code);
    unsave_code_num++;
    var codelist = $('.code_list>li');
    var num = codelist.length + 1;
    var html = ' <li>\n' +
        '                            <div class="delete col-sm-1"><i class="glyphicon glyphicon-trash" onclick="delete_code($(this))" data-kindType=""></i></div>\n' +
        '                            <div class="col-sm-1 index">' + num + '</div>\n' +
        '                            <div class="col-sm-3"><input class="code_input '+code+'" type="text" maxlength="24" value="'+code+'" readonly onclick="change_code($(this))"></div>\n' +
        '                            <div class="col-sm-1 "><span class="saved">已保存</span><span class="hasout">已出库</span></div>\n' +
        '                            <div class="col-sm-2 kind_name"></div>\n' +
        '                            <div class="col-sm-1 degree"></div>\n' +
        '                            <div class="col-sm-1 capacity"></div>\n' +
        '                            <div class="col-sm-1 spec"></div>\n' +
        '                            <div class="col-sm-2 date"></div>\n' +
        '                        </li>';
    if(codelist.length == 0){
        $('.code_list').html(html);
    }else {
        codelist.eq(0).before(html);
    }
    var audio = '';
    if(unsave_code_num%5 == 0){
        audio = '<video class="audio" style="display: none" controls="" autoplay="" name="media"><source src="/home/imgs/repeat.wav" type="audio/mpeg"></video>';
    }else {
        audio = '';
    }
    $('body').append(audio);

    search_repeat(code);
    getCodeInfo(code);
    $('.total_piece').text($('.code_list li').length);
    $('.unsaved').text(unsave_code_num);
}
//手动录入
function manual_entry() {
    var html = '<div class="code_change">\n' +
        '    <div class="change_box">\n' +
        '        <div class="change_title">\n' +
        '            手动录入条码\n' +
        '        </div>\n' +
        '        <div class="code_name">\n' +
        '            <input type="tel" name="code" class="add_code"  placeholder="" value="" maxlength="24">\n' +
        '        </div>\n' +
        '        <div>\n' +
        '            <input class="change_submit btn" type="submit" placeholder="确定">\n' +
        '        </div>\n' +
        '        <div><span class="glyphicon glyphicon-remove-sign close_icon" onclick="$(this).parents(\'.code_change\').hide();$(\'#barCode\').focus();"></span></div>\n' +
        '    </div>\n' +
        '</div>';
    $('body').append(html);

    $(function () {
        $('.add_code').focus();
        $('.change_submit').click(function () {
            var code = $(this).parent().prev().find('.add_code').val();
            add_code(code);
            $('#barCode').focus();
            $('.code_change').remove();
        })
    })
}

function hidden_title() {
    $('.content_company').removeClass('on');
    $('.search_show').addClass('on');
}
//查重
function search_repeat(el) {
    var rand =  Math.floor(Math.random()*10);

    var random = rand <= 15 ? rand : 15;
    var color = [
        '#c3d2ce',
        '#2aabd2',
        '#56c7d2',
        '#20d2a7',
        '#28d23b',
        '#9dd25b',
        '#a0d233',
        '#b3d22b',
        '#d2c839',
        '#d29230',
        '#d25721',
        '#d2343f',
        '#d2228a',
        '#a422d2',
        '#7834d2',
        '#2b26d2'
    ];
    var alarm = '';
    if($('.'+el).length>1){
        $('.'+el).css('color',color[random]);
        alarm = '<video class="audio" style="display: none" controls="" autoplay="" name="media"><source src="/home/imgs/prompt.wav" type="audio/mpeg"></video>'
        $('body').append(alarm);
    }
}

$(function () {
    // 商户搜索框
    $('.search_input').blur(function () {
        $(this).removeClass('on').siblings().addClass('on');
    });
    $('.search_input').on('focus', function () {
        document.onkeyup = function (event) {
            var e = event || window.event || arguments.callee.caller.arguments[0];
            if (e.keyCode == 13) {
                getMerchants();
            }
        }
    });

    //新建商户
    $('.close_icon').click(function () {
        $(this).parents('.create_merchant').css('display', 'none');
    });
    $('.create_shop').click(function () {
        $('.create_merchant').css('display', 'block');
    })
});
/***
 *商户列表获取
 *
 *
 * */
getMerchants();

function getMerchants() {
    var url = baseHost + '?c=Merchant&a=getMerchantList';
    var keywords = $('.search_input').val();
    var param = {
        keywords: keywords,
        manager_id: id
    };
    common_ajax('post', url, param, false, true, function (data) {
        if (data.result == 'success') {
            var list = data.data.list;
            var html = '';
            $.each(list, function (i, v) {
                html += '<li class="row" onclick="show_title($(this))">\n' +
                    '                    <div class="col-sm-4 company_id">' + v.ID + '</div>\n' +
                    '                    <div class="col-sm-8 company_name">' + v.NAME + '</div>\n' +
                    '                </li>';
            });
            $('.company_list').empty().append(html);
        }else{
            swal({
                title: "获取商户失败!",
                text: data.msg,
                type: "error",
                showConfirmButton: true
            });
        }
    })
}

/***
 *新建商户
 *
 *
 * */
function createMerchant() {
    var url = baseHost + '?c=Merchant&a=createMerchant';
    var merchant_name = $("input[name='merchant_name']").val();
    var tel = $("input[name='tel']").val();
    var param = {
        merchant_name: merchant_name,
        tel: tel,
        manager_id: id
    };
    common_ajax('post', url, param, false, true, function (data) {
        if (data.result == 'success') {
            swal({
                title: "商户创建成功!",
                text: data.msg,
                type: "success",
                timer: '2000',
                showConfirmButton: false
            });
            $('.create_merchant').css('display', 'none');
        } else {
            swal({
                title: "登录失败!",
                text: data.msg,
                type: "warning",
                showConfirmButton: true
            });
        }
    })
}

/***
 *时间和条码查询
 *
 *
 * */
//时间||条码查询单号


time_search();

function time_search() {
    checkIsSearch(1); // 检测是否是搜索
    hidden_title();
    var url = baseHost + '?c=Index&a=getCpkOutList';
    var start_time = $('.start_time').val();
    var end_time = $('.end_time').val();
    // var start_time = '2018-05-01';
    // var end_time = '2018-05-02';
    var out_code = $('.out_code').val();
    if (!start_time) {
        var myDate = new Date();
        myDate.getYear(); //获取当前年份(2位)
        myDate.getFullYear(); //获取完整的年份(4位,1970-????)
        myDate.getMonth(); //获取当前月份(0-11,0代表1月)
        myDate.getDate(); //获取当前日(1-31)
        myDate.getHours(); //返回 Date 对象的小时 (0 ~ 23)。
        myDate.getMinutes(); //返回 Date 对象的分钟 (0 ~ 59)。
        myDate.getSeconds(); //返回 Date 对象的秒数 (0 ~ 59)。
        var lastweek = myDate.getFullYear() + '-' + (myDate.getMonth() + 1) + '-' + (myDate.getDate() - 7);
        var now = myDate.getFullYear() + '-' + (myDate.getMonth() + 1) + '-' + myDate.getDate();
        $('.time_start').text(lastweek);
        $('.time_end').text(now);
    } else {
        $('.time_start').text(start_time);
        $('.time_end').text(end_time);
    }
    var param = {
        start_time: start_time,
        end_time: end_time,
        out_code: out_code,
        manager_id: id
    };
    common_ajax('post', url, param, false, true, function (data) {
        if (data.result == 'success') {
            var dataList = data.data;
            var html = '';
            $.each(dataList, function (i, v) {
                html += ' <li onclick="code_search($(this))" data-value="' + v.ID + '">\n' +
                    '                                <div class="col-sm-2">' + v.ID + '</div>\n' +
                    '                                <div class="col-sm-2">' + v.dealer_name + '</div>\n' +
                    '                                <div class="col-sm-2">' + v.cpk_name + '</div>\n' +
                    '                                <div class="col-sm-1">' + v.OPERATOR + '</div>\n' +
                    '                                <div class="col-sm-1 out_list_box">' + v.BOX + '</div>\n' +
                    '                                <div class="col-sm-2">' + v.MONEY + '</div>\n' +
                    '                                <div class="col-sm-2">' + v.OUT_TIME + '</div>\n' +
                    '                            </li>'
            });
            $('.out_list').empty().append(html);
            getCount('.out_list_box');
            //默认选择
            $(function () {
                $('.out_list li').eq(0).addClass('bg-color');
                var currObj = $('.out_list>li').eq(0);
                code_search(currObj);
            });
        }else {
            swal({
                title: "获取出库单失败!",
                text: data.msg,
                type: "error",
                showConfirmButton: true
            });
        }
    })
}

//单号查询条码等信息
function code_search(el) {
    el.addClass('bg_color').siblings().removeClass('bg_color');
    var url = baseHost + '?c=Index&a=getCpkOutCodeStatistics';
    var url1 = baseHost + '?c=Index&a=getCpkOutCodeList';
    var out_id = el.attr('data-value');
    var param = {
        out_sheet_id: out_id,
        manager_id: id
    };
    //获取出库商品统计
    common_ajax('post', url, param, false, true, function (data) {
        if (data.result == 'success') {
            var data = data.data;
            var html = '';
            $.each(data, function (i, v) {
                html += '<li>\n' +
                    '                            <div class="col-sm-2 goods_name">' + v.kind_name + '</div>\n' +
                    '                            <div class="col-sm-1 goods_degree">' + v.DEGREE + '</div>\n' +
                    '                            <div class="col-sm-1 goods_capacity">' + v.CAPACITY + '</div>\n' +
                    '                            <div class="col-sm-2 goods-spec">1x' + v.SPEC + '</div>\n' +
                    '                            <div class="col-sm-2 goods-box">' + v.BOX + '</div>\n' +
                    '                            <div class="col-sm-2 ">' + v.MONEY + '</div>\n' +
                    '                            <div class="col-sm-2 goods-price">' + v.PRICE + '</div>\n' +
                    '                        </li>'
            });
            $('.code_statistics').empty().append(html);
            getCount('.goods-box')

        }else {
            swal({
                title: "获取出库商品种类失败!",
                text: data.msg,
                type: "error",
                showConfirmButton: true
            });
        }
    });
    common_ajax('post', url1, param, false, true, function (data) {
        if (data.result == 'success') {
            var result = data.data;
            var html = '';
            var index = result.length;
            $.each(result, function (i, v) {
                html += '<li>\n' +
                    // '                            <div class="delete col-sm-1" data-value="'+v.kind+'"><i class="glyphicon glyphicon-trash" onclick="delete_code($(this))"></i></div>\n' +
                    '                            <div class="col-sm-1 index">' + index + '</div>\n' +
                    '                            <div class="col-sm-3"><input class="code_input" type="text" maxlength="24" value="' + v.box_code + '" readonly></div>\n' +
                    // '                            <div class="col-sm-1"><span class="saved">已保存</span></div>\n' +
                    '                            <div class="col-sm-2 kind_name">' + v.kind_name + '</div>\n' +
                    '                            <div class="col-sm-1 degree">' + v.DEGREE + '</div>\n' +
                    '                            <div class="col-sm-1 capacity">' + v.CAPACITY + '</div>\n' +
                    '                            <div class="col-sm-1 spec">1x' + v.SPEC + '</div>\n' +
                    '                            <div class="col-sm-2 date">' + v.OUT_TIME + '</div>\n' +
                    '                        </li>';
                index--;
            });
            $('.code_list').empty().append(html);

            $('.total_piece').text(result.length);
        }else {
            swal({
                title: "获取出库箱码失败!",
                text: data.msg,
                type: "error",
                showConfirmButton: true
            });
        }
    })
}

//删除一个
function delete_code(el) {
    $('#barCode').focus();
    swal({
        title: '确定删除',
        text: "您确定要删除当前条码?",
        type: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#55D621',
        cancelButtonColor: '#d33',
        confirmButtonText: '确定',
        cancelButtonText: '取消'
    }).then(function (isConfirm) {
        if (isConfirm === true) {
            // 移除当前要删除的对象
            var currCode = el.parent('.delete').next().next().children('input').val();
            var serial_number = el.parent().parent().index();
            el.parent().parent().remove();

            // 删除全局变量中的code
            goods_infomation['code_list'+company_id].splice(serial_number,1);

            // 删除全局变量中的kind
            var kindType = el.attr('data-kindType');
            var kindTypeNum = parseInt(goods_infomation['kind'+company_id][kindType].num);
            kindTypeNum -= 1;
            if(kindTypeNum <= 0){
                delete goods_infomation['kind'+company_id][kindType];
            }else{
                goods_infomation['kind'+company_id][kindType].num = kindTypeNum;
            }
            // 删除全局变量中的箱码
            goods_infomation['box-code'+company_id].remove(currCode);

            // 更新缓存
            var savedBoxCode = window.localStorage.getItem('box-code'+company_id);
            if(savedBoxCode && JSON.parse(savedBoxCode).length && $.inArray(currCode,JSON.parse(savedBoxCode)) > -1){
                var codes       = JSON.stringify(goods_infomation['code_list'+company_id]);
                var goods_infor = JSON.stringify(goods_infomation['kind'+company_id]);
                var boxCode     = JSON.stringify(goods_infomation['box-code'+company_id]);
                window.localStorage.setItem('goodsInfor'+company_id,goods_infor);
                window.localStorage.setItem('code'+company_id,codes);
                window.localStorage.setItem('box-code'+company_id,boxCode);
            }

            // 展示
            getLocalCode(goods_infomation['code_list'+company_id],goods_infomation['kind'+company_id]);
        }
    });
}

//删除所有
function delete_all() {
    swal({
        title: '是否删除所有条码',
        text: "您确定要删除所有条码?",
        type: 'warning',
        showCancelButton: true
    }).then(function (isConfirm) {
        if(isConfirm === true){
            $('.code_content').empty();
            window.localStorage.removeItem('goodsInfor'+company_id);
            window.localStorage.removeItem('code'+company_id);
            window.localStorage.removeItem('box-code'+company_id);
            goods_infomation = [];
            $('.total_piece').text(0);
            $('.saved_code').text(0);
            $('.unsaved').text(0);
            $('.goods-box_count').text(0);
            $('#barCode').focus();
        }
    });
}


//输入完成获取参数
function getCodeInfo(code) {
    // var el = $('.code_list>li').eq(0).find('.code_input');
    var el = $('.'+code);
    var url = baseHost + '?c=Index&a=getCpkOutCodeGoodsInfo';
    var out_code = code;
    var param = {
        out_code: out_code,
        manager_id: id
    };
    common_ajax('post', url, param, false, true, function (data) {
        if (data.result == 'success') {
            var data = data.data;
            var status = data.out_status;
            if(status == 1){
                data.has_out = 1;
                el.parent().siblings().find('.hasout').show().attr('data-type',status);
            }else {
                el.parent().siblings().find('.hasout').hide();
                data.has_out = 0;
            }
            el.parent().attr('data-value',data.kind);
            el.parent().siblings('.kind_name').html(data.kind_name);
            el.parent().siblings('.degree').html(data.DEGREE);
            el.parent().siblings('.capacity').html(data.CAPACITY);
            el.parent().siblings('.spec').html('1x'+data.SPEC);
            el.parent().siblings('.date').html(data.sweep_time);
            var kindType = [data.kind,data.DEGREE,data.CAPACITY,data.SPEC];
            el.parent().siblings('.delete').children('i').attr('data-kindType',kindType.join('-'));
            data.status = 1;
            var code_list =goods_infomation && goods_infomation.hasOwnProperty('code_list'+company_id)  ? goods_infomation['code_list'+company_id] : [];
            code_list.unshift(data);
            var code_arry = goods_infomation && goods_infomation.hasOwnProperty('kind'+company_id)  ? goods_infomation['kind'+company_id] : {};
            var key = data.kind+'-'+data.DEGREE+"-"+data.CAPACITY+'-'+data.SPEC;
            if(!code_arry[key]){
                code_arry[key] = {
                    num: 1,
                    name:data.kind_name
                };
            }else if(Object.keys(code_arry[key]).length>0) {
                code_arry[key].num++;
            }
            goods_infomation['kind'+company_id] = code_arry;
            goods_infomation['code_list'+company_id] = code_list;

            var local_boxCode = window.localStorage.getItem('box-code'+company_id);
            goods_infomation['box-code'+company_id] =local_boxCode ? JSON.parse(local_boxCode) : [];
            goods_infomation['box-code'+company_id].unshift(data.box_code);
            if(code_arry){
                var html = '';
                var key ='';
                $.each(code_arry,function (i,v) {
                   var keys = i.split('-');
                    key = '                            <div class="col-sm-1 goods_degree">'+keys[1]+'</div>\n' +
                        '                            <div class="col-sm-1 goods_capacity">'+keys[2]+'</div>\n' +
                        '                            <div class="col-sm-2 goods-spec">1x'+keys[3]+'</div>\n';
                    html += '<li>\n' +
                        '                            <div class="col-sm-2 goods_name">'+v.name+'</div>\n' +
                        key+
                        '                            <div class="col-sm-2 goods-box">'+v.num+'</div>\n' +
                        '                            <div class="col-sm-2">0.00</div>\n' +
                        '                            <div class="col-sm-2 goods-price">0.00</div>\n' +
                        '                        </li>'
                });
                $('.code_statistics').empty().append(html);
                getCount('.goods-box');
            }

        }
    });
}
//保存

function save_locl() {
    $('#barCode').focus();
    // 检测是否扫描条码
    if(goods_infomation['code_list'+company_id].length <= 0){
        swal({
            title: "请先扫描条码!",
            text: '',
            type: "warning",
            timer: '2000',
            showConfirmButton: false
        });
        return false;
    }

    var has_out = $('.hasout');
    var outCode = '';
    for(var i = 0;i<has_out.length;i++){
        if(has_out.eq(i).attr('data-type')==1){
            swal({
                title: "您有已出库的条码!",
                text: '',
                type: "warning",
                showConfirmButton: true
            });
            outCode = true;
            break;
        }
    }
    if(outCode){
        return false
    }
    var codes       = JSON.stringify(goods_infomation['code_list'+company_id]);
    var goods_infor = JSON.stringify(goods_infomation['kind'+company_id]);
    var boxCode     = JSON.stringify(goods_infomation['box-code'+company_id]);
    window.localStorage.setItem('goodsInfor'+company_id,goods_infor);
    window.localStorage.setItem('code'+company_id,codes);
    window.localStorage.setItem('box-code'+company_id,boxCode);
    if(goods_infor){
        swal({
            title: "成功!",
            text: '条码已保存成功',
            type: "success",
            timer: '2000',
            showConfirmButton: false
        });
        // $('.saved').addClass('show_s');
        getLocalCode()
    }else {
        swal({
            title: "保存失败!",
            text: '',
            type: "error",
            timer: '2000',
            showConfirmButton: false
        });
    }


}

//获取合计件数
function getCount(param) {
    var el = param+'_count';
    var total = 0;
    for (var i = 0; i<$(param).length;i++){
        total += Number($(param).eq(i).text())
    }
   $(el).text(total)
}


//获取localstorage 条码
function getLocalCode(codeList,kindList) {
    // 获取已保存的箱码
    var savedBoxCode = window.localStorage.getItem('box-code'+company_id);
    savedBoxCode = savedBoxCode ? JSON.parse(savedBoxCode) : [];

    var code_result = codeList ? codeList : JSON.parse(window.localStorage.getItem('code'+company_id));
    if(code_result){
        var index = code_result.length;
        var html = '';
        $.each(code_result, function (i, v) {
            if(v.status == 1){
                var kindType = [v.kind,v.DEGREE,v.CAPACITY,v.SPEC];
                // 判断是否保存
                var isSaved = '';
                if(v.has_out) isSaved = '<span class="hasout" data-type="1" style="display: inline-block">已保存</span>';
                else if($.inArray(v.box_code,savedBoxCode) > -1) isSaved = '<span class="saved show_s">已保存</span>';

                html += '<li>\n' +
                    '                            <div class="delete col-sm-1" data-value="'+v.kind+'"><i class="glyphicon glyphicon-trash" onclick="delete_code($(this))" data-kindType="'+kindType.join('-')+'"></i></div>\n' +
                    '                            <div class="col-sm-1 index">' + index + '</div>\n' +
                    '                            <div class="col-sm-3"><input class="code_input  '+ v.box_code + '" type="text" maxlength="24" value="' + v.box_code + '" onclick="change_code($(this))" readonly></div>\n' +
                    '                            <div class="col-sm-1">'+isSaved+'</div>\n' +
                    '                            <div class="col-sm-2 kind_name">' + v.kind_name + '</div>\n' +
                    '                            <div class="col-sm-1 degree">' + v.DEGREE + '</div>\n' +
                    '                            <div class="col-sm-1 capacity">' + v.CAPACITY + '</div>\n' +
                    '                            <div class="col-sm-1 spec">1x' + v.SPEC + '</div>\n' +
                    '                            <div class="col-sm-2 date">' + v.sweep_time + '</div>\n' +
                    '                        </li>';
                index--;
            }
            search_repeat(v.box_code);
        });

        $('.code_list').empty().append(html);
        $('.saved_code').text(savedBoxCode.length);
        $('.total_piece').text(code_result.length);
        //清空保存
        unsave_code_num = 0;
        $('.unsaved').text(code_result.length - savedBoxCode.length);
    }

    var outList_goods = kindList ? kindList : JSON.parse(window.localStorage.getItem('goodsInfor'+company_id));
    if(outList_goods){
        //获取条码商品信息
        var goodsInfor = '';
        var key ='';
        $.each(outList_goods,function (i,v) {
            var keys = i.split('-');
            key = '                            <div class="col-sm-1 goods_degree">'+keys[1]+'</div>\n' +
                '                            <div class="col-sm-1 goods_capacity">'+keys[2]+'</div>\n' +
                '                            <div class="col-sm-2 goods-spec">1x'+keys[3]+'</div>\n';
            goodsInfor += '<li>\n' +
                '                            <div class="col-sm-2 goods_name">'+v.name+'</div>\n' +
                key+
                '                            <div class="col-sm-2 goods-box">'+v.num+'</div>\n' +
                '                            <div class="col-sm-2">0.00</div>\n' +
                '                            <div class="col-sm-2 goods-price">0.00</div>\n' +
                '                        </li>'
        });
        $('.code_statistics').empty().append(goodsInfor);
        getCount('.goods-box');
    }
}

/**
 * 确认出库
 */
function confirmOut(){
    $('#barCode').focus();
    swal({
        title: '确认出库',
        text: "您确定扫码无误,立即出库?",
        type: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#55D621',
        cancelButtonColor: '#d33',
        confirmButtonText: '确定',
        cancelButtonText: '取消'
    }).then(function (isConfirm) {
        if(isConfirm) outWarehouse();
    });
}
/**
 * 出库
 * @returns {boolean}
 */
function outWarehouse(){
    if(!goods_infomation['code_list'+company_id] || !goods_infomation['code_list'+company_id].length){
        swal({
            title: "请先扫码!",
            text: '未扫描箱码',
            type: "warning",
            timer: '2000',
            showConfirmButton: false
        });
        return false;
    }
    // 检测箱码是否重复
    var codeList = goods_infomation['code_list'+company_id],allCodeObj = [],checkCode = [],isTrue = true;
    for(var i = 0;i < codeList.length;i ++){
        if($.inArray(codeList[i].box_code,checkCode) > -1){
            swal({
                title: "箱码重复!",
                text: '箱码:【'+codeList[i].box_code+'】重复请重新扫描',
                type: "warning",
                timer: '2000',
                showConfirmButton: false
            });
            isTrue = false;
            break;
        }else{
            allCodeObj[i] = {
                code : codeList[i].box_code,
                out_time : codeList[i].sweep_time
            };
            checkCode[i] = codeList[i].box_code;
        }
    }
    if(!isTrue) return false;

    // 检测
    var url = baseHost + '?c=Index&a=outSheet';
    var param = {
        manager_id : id,
        merchant_id : company_id,
        out_code : JSON.stringify(allCodeObj)
    };
    common_ajax('post', url, param, false, true, function (data) {
        if(data.result == 'success'){
            // 清空缓存
            window.localStorage.setItem('goodsInfor'+company_id,'');
            window.localStorage.setItem('code'+company_id,'');
            window.localStorage.setItem('box-code'+company_id,'');
            swal({
                title: "出库成功!",
                text: data.msg,
                type: "info",
                timer: '2000',
                showConfirmButton: false
            });
            setTimeout(function(){
                window.location.reload();
            },2000);
        }else{
            swal({
                title: "出库失败!",
                text: data.msg,
                type: "error",
                timer: '2000',
                showConfirmButton: false
            });
        }
    });
}

/**
 * 检测是否是搜索
 * @param type
 */
function checkIsSearch(type){
    if(type){
        $('.saved_code').parent().hide();
        $('.unsaved').parent().hide();
        $('#save-code').hide();
        $('.delete_all').hide();
        $('.code-status').hide();
        $('#print-code').show();
    }else{
        $('.saved_code').parent().show();
        $('.unsaved').parent().show();
        $('#save-code').show();
        $('.delete_all').show();
        $('.code-status').show();
        $('#print-code').hide();
    }
}

/**
 * 退出登录
 */
function loginLout(){
    Manager.loginOut();
}

/**
 * 打印
 * @returns {boolean}
 */
function openToPrintCode(){
    var codeList = $('#print-container .code_list>li').length;
    if(!codeList){
        swal({
            title: "无可打印箱码!",
            text: '',
            type: "warning",
            timer: '2000',
            showConfirmButton: false
        });
        return false;
    }
    var codeHtml = $('#print-container').html();

    var localStorage = window.localStorage;
    localStorage.setItem('PRINT-CODE',codeHtml);
    window.open('/home/html/print_code.html');

}

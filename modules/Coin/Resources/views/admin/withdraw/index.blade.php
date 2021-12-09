@extends('core::admin.layouts.app')

@section('content')
<div class="layui-card">
    <div class="layui-card-body">

        <div class="test-table-reload-btn" style="margin-bottom: 10px;">


            <div class="layui-inline">
                <input type="text" name="id" class="layui-input" id="id" placeholder=" 表ID ">
            </div>
            <div class="layui-inline">
                <input type="text" name="user_info" class="layui-input" id="user_info" placeholder=" 转账会员ID|会员名 ">
            </div>

            <div class="layui-inline">
                <input type="text" name="to" class="layui-input" id="to" placeholder=" 转入地址 ">
            </div>


            <div class="layui-inline">
                <select name="state" xm-select="order_status" id="state" lay-filter="status" lay-verify="required" class="layui-select">
                    <option value="1000">--状态类型--</option>
                    @foreach ($state as $key=> $vo)
                        <option value="{{ $key }}">{{ $vo }}</option>
                    @endforeach

                </select>
            </div>
            <div class="layui-inline">
                <select name="symbol" xm-select="symbol" id="symbol" lay-filter="status" lay-verify="required" class="layui-select">
                    <option value="">--币种--</option>
                    @foreach ($coin as $vo)
                        <option value="{{ $vo->symbol }}">{{ $vo->symbol }}</option>
                    @endforeach
                </select>
            </div>


            <div class="layui-inline">
                <input type="text" name="times" class="layui-input" id="laydate-range-datetime" style="width: 250px" placeholder=" 搜索日期 ">
            </div>

            <button class="layui-btn" data-type="reload">搜索</button>
        </div>

        <table id="LAY-user-back-role" lay-filter="LAY-user-back-role"></table>


        <div id="model" style="padding:10px;background:#FFFFFF;display:none;width:500px;">
            <div class="layui-form">
                <div class="layui-form-item">
                    <label class="layui-form-label" style="padding-left:0px;padding-right: 0px;">出金方式:</label>
                    <div class="layui-input-block">
                        <input type="radio" name="update_state" value="-2" checked title="手动打币">
                        <input type="radio" name="update_state" value="-1" title="自动出金">
                    </div>
                </div>
                <input type="hidden" value="" id="hidden_id">
            </div>
        </div>

    </div>
</div>
@endsection
@push('after-scripts')
    <script type="text/html" id="table-useradmin-admin">


        @{{# if(d.state == -2) { }}

        <a class="layui-btn layui-btn-normal layui-btn-xs"  lay-event="edit">设置已转账</a>

        @{{# } }}

        @{{# if(d.state == -2){ }}

        <a class="layui-btn layui-btn-danger layui-btn-xs"  lay-event="cancel">撤销</a>
        @{{# } }}
       {{-- <a class="layui-btn layui-btn-normal layui-btn-xs"  lay-event="examine">审核</a>--}}
    </script>
@push('after-scripts')

{{--<script type="text/html" id="tableToolbar">
    <div class="layui-btn-container">
        <button class="layui-btn layuiadmin-btn-role" lay-event="add">添加角色</button>
    </div>
</script>--}}

<!--引入点击复制js-->
<script src="/vendor/js/clipboard.min.js"></script>

<script>

    function ityzl_SHOW_LOAD_LAYER(){
        return layer.msg('处理中...', {icon: 16,shade: [0.5, '#b2b2b2'],scrollbar: false, time:0}) ;
    }
    function ityzl_CLOSE_LOAD_LAYER(index){
        layer.closeAll();
        layer.close(index);
    }

    layui.use(['form', 'table', 'util','laydate','laytpl'], function () {

        var $ = layui.$
            , util = layui.util
            , form = layui.form
            , table = layui.table
            , laytpl = layui.laytpl
            , laydate = layui.laydate;

        laydate.render({
            elem: '#laydate-range-datetime'
            ,type: 'date'
            ,range: '||'
        });

        table.render({
            elem: '#LAY-user-back-role',
            toolbar: '#tableToolbar',
            url: '{{ route('m.coin.api.admin.api.withdraw.index') }}',
            method:'post',
            parseData: function (res) { //res 即为原始返回的数据
                return {
                    'code': res.message ? 400 : 0, //解析接口状态
                    'msg':res.message || '加载失败', //解析提示文本
                    'count': res.total || 0, //解析数据长度
                    'data': res.data || [] //解析数据列表
                };
            },
            cols: [[

                {title:'操作', width:200,toolbar: '#table-useradmin-admin' }
                ,{field:'created_at', title: '创建时间', width:250, sort: true, templet: function (res) {
                        return moment(res.created_at).format("YYYY-MM-DD HH:mm:ss")
                    }}
                ,{field:'id', title: 'ID', width:100, sort: true}
                ,{field:'user_id', title: 'UID', width:100, sort: true}
                ,{field:'pay_num', title: '提现数量', width:150, sort: true}
                ,{field:'num', title: '获得数量', width:150, sort: true}
                ,{field:'cost', title: '手续费', width:150, sort: true}
                ,{field:'to_text', title: '转入地址',width:350,  event:'copy_to'}
                ,{field:'symbol', title: '币种', width:150, sort: true}
                ,{field:'username', title: '会员名', width:150, }
                ,{field:'state_text', title: '状态', width:200, }

            ]],
            text: {
                none: '无相关数据'
            },
            page: true
        });
        table.on("tool(LAY-user-back-role)", function(e) {
            if (events[e.event]) {
                events[e.event].call(this, e.data);
            }
        });
        util.event('lay-event', events);


        var btn=$('<button>');//新建按钮，并设置复制事件，最后触发按钮来执行事件
        var clipboard = new ClipboardJS(btn[0]);
        clipboard.on('success', function(e) {
            console.log(e);
            layer.msg((e.action=='copy'?'复制':'剪切')+'成功');
        });

        clipboard.on('error', function(e) {
            layer.msg((e.action=='copy'?'复制':'剪切')+'失败');
        });


        var events = {

            //点击复制转入地址
            copy_to: function (data) {

                /*clipboard.text=function(trigger) {
                    return data.to;
                };
                btn.click();*/
                if(data.to_link!=''){
                    window.open(data.to_link); //打开新窗口
                }
            },


            edit: function (data) {
                events.edit_state(data,0);
            },
            examine: function (data) {
                //events.edit_state(data,1);

                $("#hidden_id").val(data.id);
                $("input[name='update_state']").each(function (index, obj) {
                    if ($(obj).val() == data.status) {
                        $(obj).next().find('i').click();
                    } else {
                        $(obj).attr('checked', false);
                    }
                });
                form.render();
                layer.open({
                    type: 1,
                    title: false,
                    closeBtn: 0,
                    area: ['500px', '120px'],
                    shadeClose: true,
                    skin: 'yourclass',
                    content: $("#model"),
                    btn: ['确定', '取消'],
                    yes: function (index, layero) {
                        //let id = $("#hidden_id").val();
                        let state = $("input[name='update_state']:checked").val();
                        console.log(state);
                        if(!state){
                            layer.msg('请选择方式');
                            return false;
                        }

                        /*$.ajax({
                            url: "{{route('admin.api.certify.update',['id'=>'!id!'])}}".replace('!id!', id),
                            type: "post",
                            data: {id: id, status: status},
                            success: function (res) {
                                active.reload();
                            }
                        });*/
                        layer.confirm('确定操作该信息ID【'+data.id+'】？', function(index){
                            i = ityzl_SHOW_LOAD_LAYER();
                            $.ajax({
                                type: 'post',
                                url : '{{ route('m.coin.api.admin.api.withdraw.edit_state') }}',
                                data: {"id":data.id,'type':1,'state':state},
                                dataType: 'json',
                                success: function (resp) {
                                    ityzl_CLOSE_LOAD_LAYER(i);
                                    console.log(resp);

                                    layer.msg(resp.message,{icon: 1,time: 2000,shade: [0.8, '#393D49']},function(){
                                        //layer.close(index);
                                        window.location.reload();
                                        //table.reload('LAY-user-back-role')
                                    });

                                },
                                error:function (err) {
                                    ityzl_CLOSE_LOAD_LAYER(i);
                                    layer.msg(err.responseJSON.message ||  '请求失败', {time: 2000});
                                }
                            });
                        });
                        layer.close(index)
                    }
                });


                /*layer.confirm('确定操作该信息ID【'+obj_data.id+'】？', function(index){
                    i = ityzl_SHOW_LOAD_LAYER();
                    $.ajax({
                        type: 'post',
                        url : '{{ route('m.coin.api.admin.api.withdraw.edit_state') }}',
                        data: {"id":obj_data.id,'type':type},
                        dataType: 'json',
                        success: function (resp) {
                            ityzl_CLOSE_LOAD_LAYER(i);
                            console.log(resp);

                            layer.msg(resp.message,{icon: 1,time: 2000,shade: [0.8, '#393D49']},function(){
                                //layer.close(index);
                                window.location.reload();
                                //table.reload('LAY-user-back-role')
                            });

                        },
                        error:function (err) {
                            ityzl_CLOSE_LOAD_LAYER(i);
                            layer.msg(err.responseJSON.message ||  '请求失败', {time: 2000});
                        }
                    });
                });*/


            },
            cancel: function (data) {
                events.edit_state(data,2);
            },

            //设置为已转账
            edit_state: function (obj_data,type) {

                console.log(obj_data);
                console.log(type);


                layer.confirm('确定操作该信息ID【'+obj_data.id+'】？', function(index){
                    i = ityzl_SHOW_LOAD_LAYER();
                    $.ajax({
                        type: 'post',
                        url : '{{ route('m.coin.api.admin.api.withdraw.edit_state') }}',
                        data: {"id":obj_data.id,'type':type},
                        dataType: 'json',
                        success: function (resp) {
                            ityzl_CLOSE_LOAD_LAYER(i);
                            console.log(resp);

                            layer.msg(resp.message,{icon: 1,time: 2000,shade: [0.8, '#393D49']},function(){
                                //layer.close(index);
                                window.location.reload();
                                //table.reload('LAY-user-back-role')
                            });

                        },
                        error:function (err) {
                            ityzl_CLOSE_LOAD_LAYER(i);
                            layer.msg(err.responseJSON.message ||  '请求失败', {time: 2000});
                        }
                    });
                });
            },
        };




        //搜搜重载
        var $ = layui.$, active = {
            reload: function(){

                var times = $('#laydate-range-datetime').val();
                var symbol = $('#symbol').val();
                var from = $('#from').val();
                var to = $('#to').val();
                var hash = $('#hash').val();
                var user_info = $('#user_info').val();
                var id = $('#id').val();
                var state = $('#state').val();
                //执行重载
                table.reload('LAY-user-back-role', {
                    page: {
                        curr: 1 //重新从第 1 页开始
                    }
                    ,where: {
                        key: {
                            times:times,
                            symbol:symbol,
                            from:from,
                            to:to,
                            user_info:user_info,
                            id:id,
                            state:state,
                            hash:hash
                        }
                    }
                });
            }
        };

        $('.test-table-reload-btn .layui-btn').on('click', function(){
            var type = $(this).data('type');
            active[type] ? active[type].call(this) : '';
        });

    })
</script>
@endpush


@extends('core::admin.layouts.app')

@section('content')
    <div class="layui-card">
        <div class="layui-card-body">


            <div class="test-table-reload-btn" style="margin-bottom: 10px;">

                <div class="layui-inline">
                    <input type="text" name="user" class="layui-input" id="user" placeholder="会员UID">
                </div>

                <div class="layui-inline">
                    <input type="text" name="account" class="layui-input" id="account" placeholder="手机或邮箱">
                </div>


                <div class="layui-inline">
                    <select name="type" xm-select="order_status" id="type" lay-filter="status" lay-verify="required" class="layui-select">
                        <option value="">--购买方式--</option>
                        @foreach ($list as $key=>$vo)
                            <option value="{{$key}}">{{$vo}}</option>
                        @endforeach
                    </select>
                </div>

                <div class="layui-inline">
                    <select name="state" xm-select="order_status" id="state" lay-filter="status" lay-verify="required" class="layui-select">
                        <option value="">--审核状态--</option>
                        @foreach ($state as $key=>$vo)
                            <option value="{{$key}}">{{$vo}}</option>
                        @endforeach
                    </select>
                </div>

                <div class="layui-inline">
                    <input type="text" name="created_at" class="layui-input" id="created_at" style="width: 250px"
                           placeholder="时间">
                </div>



                <button class="layui-btn" data-type="reload">搜索</button>

            </div>

            <table id="lay-table" lay-filter="lay-table"></table>
        </div>

    </div>
@endsection

@push('after-scripts')
    <script type="text/html" id="toolColumn">
        @{{# if(d.state == '未审核') { }}
        <a class="layui-btn layui-btn-xs" lay-event="edit">审核</a>
        @{{# } }}
    </script>
    <script>
        layui.use(['form', 'table', 'util', 'laydate'], function () {

            var $ = layui.$
                , util = layui.util
                , form = layui.form
                , table = layui.table
                , laydate = layui.laydate;

            laydate.render({
                elem: '#created_at'
                , type: 'date'
                , range: '||'
            });

            var ins1=  table.render({
                elem: '#lay-table',
                url: '{{ route('m.dsy.api.admin.api.play.list') }}',
                toolbar: '#toolbarDemo' //开启头部工具栏，并为其绑定左侧模板
                ,defaultToolbar: ['exports'],
                parseData: function (res) { //res 即为原始返回的数据
                    return {
                        'code': res.message ? 400 : 0, //解析接口状态
                        'msg': res.message || '加载失败', //解析提示文本
                        'count': res.total, //解析数据长度
                        'data': res.data || [] //解析数据列表
                    };
                },
                page: true,
                id: "dataTable",
                cols: [[
                    {field: 'right', title: '操作', toolbar: '#toolColumn', width: 100},
                    {field: 'admin_id', title: '入单提交人', width: 100},
                    {field: 'user_id', title: '入单用户uid', width: 120},
                    {field: 'mobile', title: '入单用户手机', width: 120},
                    {field: 'email', title: '入单用户邮箱', width: 120},
                    {field: 'commodity_id', title: '入单产品id', width: 100},
                    {field: 'num', title: '入单产品数量', width: 120},
                    {field: 'buy_type', title: '购买方式', width: 100},
                    {field: 'state', title: '审核状态', width: 100},
                    {field: 'state_id',title: '审核人',width: 100},
                    {
                        field: 'buy_at', title: '入单提交的购买时间', width: 200, templet: function (res) {
                            return moment(res.buy_at).format("YYYY-MM-DD HH:mm:ss")
                        }
                    },
                    {
                        field: 'created_at', title: '入单提交时间', width: 200, templet: function (res) {
                            return moment(res.created_at).format("YYYY-MM-DD HH:mm:ss")
                        }
                    }
                ]],
                done: function (res, curr, count) {
                    exportData = res.data;
                },
                text: {
                    none: '没有可用数据'
                },
            });
            $(".export").click(function () {
                table.exportFile(ins1.config.id, exportData, 'xls');
            });
            function  ajax(url,data) {
                $.ajax({
                    url: url,
                    dataType: 'json',
                    type: 'post',
                    data:data,
                    success: function (data) {

                        window.location.reload();
                    },
                    error:function (data) {
                        alert('操作失败');
                    }
                });
            }
            table.on("tool(lay-table)", function (e) {
                var data = e.data;
                if (e.event === 'edit') {
                    layer.open({
                        content: '是否通过?'
                        ,btn: ['通过', '取消审核']
                        ,yes: function(index, layero){
                            let url='{{route('m.dsy.api.admin.api.play.succeed')}}';
                            let datas={id:data.id};
                            ajax(url,datas);
                        }
                        ,btn2: function(index, layero){
                        }
                        ,cancel: function(){
                        }
                    });
                }
            });

            let active = {
                reload: function () {
                    let user = $('#user');
                    let created_at = $('#created_at');
                    let account=$('#account');
                    let type=$('#type');
                    let  state=$('#state');
                    //执行重载
                    table.reload('dataTable', {
                        page: {
                            curr: 1 //重新从第 1 页开始
                        }
                        , where: {
                            user: user.val(),
                            created_at: created_at.val(),
                            account:account.val(),
                            type:type.val(),
                            state:state.val(),
                        }
                    }, 'data');
                },
            };


            $('.layui-btn').on('click', function () {
                var type = $(this).data('type');
                active[type] ? active[type].call(this) : '';
            });
        })


    </script>
@endpush



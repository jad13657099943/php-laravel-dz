@extends('core::admin.layouts.app')

@section('content')
    <div class="layui-card">
        <div class="layui-card-body">


            <div class="test-table-reload-btn" style="margin-bottom: 10px;">

                <div class="layui-inline">
                    <input type="text" name="user" class="layui-input" id="user" placeholder="会员UID|会员名">
                </div>

                <div class="layui-inline">
                    <input type="text" name="created_at" class="layui-input" id="created_at" style="width: 250px"
                           placeholder="时间">
                </div>
                <button class="layui-btn" data-type="reload">搜索</button>
            </div>
            <script type="text/html" id="toolColumn">
                <a class="layui-btn layui-btn-xs" lay-event="add">赠送</a>
                <a class="layui-btn layui-btn-xs" lay-event="del">删除</a>
            </script>
            <table id="lay-table" lay-filter="lay-table"></table>
        </div>

    </div>
@endsection

@push('after-scripts')

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
                url: '{{ route('m.dsy.api.admin.api.xian.index') }}',
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
                    {field: 'id', title: 'ID', width: 210},
                    {field: 'user_id', title: 'UID', width: 210},
                    {field: 'username', title: '用户名', width: 210},
                    {field: 'money', title: '收益', width: 260},
                    {field: 'symbol', title: '币种', width: 260},
                    {field: 'type',title: '类型',width: 260},
                    {
                        field: 'created_at', title: '创建时间', width: 200, templet: function (res) {
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
                        alert('赠送失败');
                    }
                });
            }
            table.on("tool(lay-table)", function (e) {
                var data = e.data;
                if (e.event === 'add') {
                    layer.prompt({
                        title: '请输入赠送数量',
                    }, function(val, index, elem){
                        let url='{{route('m.dsy.api.admin.api.order.add')}}';
                        let datas={num:val,id:data.id};
                        ajax(url,datas);
                    });
                }
                if (e.event === 'del') {
                    var url='{{route('m.dsy.api.admin.api.order.del')}}'
                    var datas={id:data.id}
                    ajax(url,datas);
                }
            });

            let active = {
                reload: function () {
                    let user = $('#user');
                    let created_at = $('#created_at');
                    //执行重载
                    table.reload('dataTable', {
                        page: {
                            curr: 1 //重新从第 1 页开始
                        }
                        , where: {
                            user: user.val(),
                            created_at: created_at.val(),
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



@extends('core::admin.layouts.app')

@section('content')
    <div class="layui-card">
        <div class="layui-card-body">


            <div class="test-table-reload-btn" style="margin-bottom: 10px;">

                <div class="layui-inline">
                    <input type="text" name="user" class="layui-input" id="user" placeholder="会员UID">
                </div>

                <div class="layui-inline">
                    <input type="text" name="created_at" class="layui-input" id="created_at" style="width: 250px"
                           placeholder="时间">
                </div>

                <button class="layui-btn" data-type="reload">搜索</button>
            </div>
            <script type="text/html" id="toolColumn">
                {{-- <a class="layui-btn layui-btn-xs" lay-event="add">赠送</a>--}}
                <a class="layui-btn layui-btn-xs" lay-event="del">删除</a>
                <a class="layui-btn layui-btn-xs" lay-event="time">时间设置</a>
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
                url: '{{ route('m.dsy.api.admin.api.order.index2') }}',
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
                    {title: '操作', toolbar: '#toolColumn', width: 160},
                    {field: 'id', title: 'ID', width: 100},
                    {field: 'chain', title: '模块', width: 100},
                    {field: 'username', title: '用户名', width: 100},
                    {field: 'mobile', title: '手机号', width: 100},
                    {field: 'title', title: '商品', width: 100},
                    {field: 'num', title: '购买数量', width: 100},
                    {field: 'save', title: '获得存力', width: 100},
                    {field: 'money', title: '支付金额(USDT)', width: 100},
                    {
                        field: 'start_time', title: '开挖时间', width: 200, templet: function (res) {
                            return moment(res.start_time).format("YYYY-MM-DD HH:mm:ss")
                        }
                    },
                    {
                        field: 'end_time', title: '到期时间', width: 200, templet: function (res) {
                            return moment(res.end_time).format("YYYY-MM-DD HH:mm:ss")
                        }
                    },
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
                if (e.event === 'time') {
                    layer.open({
                        title:'时间设置',
                        content: '<div class="layui-inline">\n' +
                            '      <label class="layui-form-label">开始时间设置</label>\n' +
                            '      <div class="layui-input-inline">\n' +
                            '        <input type="date" name="date" id="start" lay-verify="date" placeholder="yyyy-MM-dd" autocomplete="off" class="layui-input">\n' +
                            '      </div>\n' +
                            '    </div>'+'<div class="layui-inline">\n' +
                            '      <label class="layui-form-label">结束时间设置</label>\n' +
                            '      <div class="layui-input-inline">\n' +
                            '        <input type="date" name="date" id="end" lay-verify="date" placeholder="yyyy-MM-dd" autocomplete="off" class="layui-input">\n' +
                            '      </div>\n' +
                            '    </div>'
                        ,btn: ['确定']
                        ,yes: function(index, layero){
                            let start=$('#start').val();
                            let end=$('#end').val();
                            let id=data.id;
                            let DATA={id:id,start_time:start,end_time:end};
                            let URL='{{ route('m.dsy.api.admin.api.unite.time') }}';
                            ajax(URL,DATA);
                        }
                        ,cancel: function(){
                            //右上角关闭回调

                            //return false 开启该代码可禁止点击该按钮关闭
                        }
                    });
                }
                if (e.event === 'del') {
                    var url='{{route('m.dsy.api.admin.api.unite.del')}}'
                    var datas={id:data.id}
                    ajax(url,datas);
                }
            });

            let active = {
                reload: function () {
                    let user = $('#user');
                    let created_at = $('#created_at');
                    let chain=$('#chain');
                    //执行重载
                    table.reload('dataTable', {
                        page: {
                            curr: 1 //重新从第 1 页开始
                        }
                        , where: {
                            user: user.val(),
                            created_at: created_at.val(),
                            chain:chain.val(),
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


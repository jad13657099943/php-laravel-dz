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

            table.render({
                elem: '#lay-table',
                url: '{{ route('m.dsy.api.admin.api.fil.index') }}',
                parseData: function (res) { //res 即为原始返回的数据
                    return {
                        'code': res.message ? 400 : 0, //解析接口状态
                        'msg': res.message || '加载失败', //解析提示文本
                        'count': res.total, //解析数据长度
                        'data': res.data || [] //解析数据列表
                    };
                },
                page: {
                    layout: ['count', 'prev', 'page', 'next', 'skip'] //自定义分页布局
                },
                id: "dataTable",
                cols: [[
                    //  {field: 'right', title: '操作', toolbar: '#toolColumn', width: 120},
                    {field: 'id', title: 'ID', width: 100},
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
                text: {
                    none: '没有可用数据'
                },
            });
            table.on("tool(lay-table)", function (e) {
                // if (events[e.event]) {
                //     events[e.event].call(this, e.data);
                // }
                var data = e.data;
                if (e.event === 'edit_user') {
                    var url = '{{ route('m.dsy.admin.good.edit') }}?id=' + data.id;
                    layer.open({
                        type: 2
                        , title: '编辑：' + data['id'] + "信息"
                        , content: url
                        , area: ['90%', '90%']
                    })
                }
                if (e.event==='delete'){
                    var urls='{{route('m.dsy.api.admin.api.good.del')}}';
                    $.ajax({
                        url: urls,
                        dataType: 'json',
                        type: 'post',
                        data:{id:data.id},
                        success: function (data) {
                            window.parent.location.reload();
                        },
                        error:function (data) {
                            alert('删除失败');
                        }
                    });
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


@extends('core::admin.layouts.app')

@section('content')
    <div class="layui-card">
        <div class="layui-card-body">
            <div class="test-table-reload-btn" style="margin-bottom: 10px;">

                {{--<div class="layui-inline">
                    <select name="coin" xm-select="coin" id="coin" lay-verify="required" class="layui-select">
                        <option value="">--币种--</option>

                    </select>
                </div>

                <div class="layui-inline">
                    <select name="state" xm-select="state" id="state" lay-verify="required" class="layui-select">
                        <option value="">--状态--</option>

                    </select>
                </div>

                <button class="layui-btn" data-type="reload">搜索</button>--}}
                <button class="layui-btn" data-type="create">添加新任务</button>
            </div>


            <script type="text/html" id="toolColumn">

                <a class="layui-btn layui-btn-xs" lay-event="edit_info">编辑</a>

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
                url: '{{ route('m.getcoin.api.admin.api.quest_list.index') }}',
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
                    {field: 'right', title: '操作', toolbar: '#toolColumn', width: 120},
                    {field: 'id', title: 'ID', width: 80},
                    {field: 'title', title: '任务标题', width: 200},
                    {field: 'money_cny', title: '奖励(CNY)', width: 100},
                    {field: 'money_usd', title: '奖励(USD)', width: 100},
                    {field: 'unit', title: '奖励单位', width: 100},
                    {field: 'state_text', title: '状态', width: 100},
                    {field: 'is_show_text', title: '前端状态', width: 100},
                    {field: 'sort', title: '排序',width: 100},
                    {
                        field: 'created_at', title: '时间', width: 170, templet: function (res) {
                            return moment(res.created_at).format("YYYY-MM-DD HH:mm:ss")
                        }
                    },


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
                if (e.event === 'edit_info') {

                    var url = '{{ route('m.getcoin.admin.quest_list.edit_info') }}?id='+data.id;
                    location.href = url;
                    /*layer.open({
                        type: 2
                        , title: '编辑任务：' + data['title']
                        , content: url
                        , area: ['90%', '90%']
                    })*/
                }
            });


            let active = {
                reload: function () {

                    //执行重载
                    table.reload('dataTable', {
                        page: {
                            curr: 1 //重新从第 1 页开始
                        }
                        , where: {
                            state: $("#state").val(),
                            symbol: $("#coin").val(),
                        }
                    }, 'data');
                },
                create: function () {

                    var url = '{{ route('m.getcoin.admin.quest_list.create') }}';
                    location.href = url;
                    /*layer.open({
                        type: 2
                        , title: '创建新任务'
                        , content: url
                        , area: ['90%', '90%']
                    })*/
                }
            };


            $('.layui-btn').on('click', function () {
                var type = $(this).data('type');
                active[type] ? active[type].call(this) : '';
            });
        })

    </script>
@endpush


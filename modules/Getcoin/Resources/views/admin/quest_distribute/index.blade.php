@extends('core::admin.layouts.app')

@section('content')
    <div class="layui-card">
        <div class="layui-card-body">
            <div class="test-table-reload-btn" style="margin-bottom: 10px;">
                <div class="layui-inline">
                    <select name="qid" xm-select="qid" id="qid" lay-verify="required" class="layui-select">
                        <option value="">--任务名称--</option>
                        @foreach ($quest as $key=> $vo)
                            <option value="{{ $vo->id }}">{{ $vo->title }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="layui-inline">
                    <input type="number" name="user_id" id="user_id" placeholder="组长UID" class="layui-input">
                </div>

                <button class="layui-btn" data-type="reload">搜索</button>
            </div>


            <script type="text/html" id="toolColumn">

                {{--<a class="layui-btn layui-btn-xs" lay-event="edit_user">编辑</a>--}}
                <a class="layui-btn layui-btn-xs" lay-event="wallet">任务钱包</a>

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
                url: '{{ route('m.getcoin.api.admin.api.quest_distribute.index') }}',
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
                    {field: 'user_id', title: '组长UID', width: 120},
                    {field: 'user_name', title: '组长', width: 120},
                    {field: 'title', title: '任务标题', width: 300},
                    {field: 'release_num', title: '已报名数', width: 100},
                    {field: 'money_text', title: '任务奖励', width: 120},
                    {field: 'unit', title: '奖励单位', width: 120},
                    {field: 'state_text', title: '状态', width: 100},
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
                if (e.event === 'wallet') {

                    var url = '{{ route('m.getcoin.admin.quest_distribute.wallet') }}?id='+data.id;
                    layer.open({
                        type: 2
                        , title: '绑定的任务钱包：'
                        , content: url
                        , area: ['90%', '90%']
                    })
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
                            qid: $("#qid").val(),
                            user_id: $("#user_id").val(),
                        }
                    }, 'data');
                },
                create: function () {

                    var url = '{{ route('m.getcoin.admin.quest_wallet.create') }}';
                    layer.open({
                        type: 2
                        , title: '生成钱包地址'
                        , content: url
                        , area: ['90%', '90%']
                    })
                }
            };


            $('.layui-btn').on('click', function () {
                var type = $(this).data('type');
                active[type] ? active[type].call(this) : '';
            });
        })

    </script>
@endpush


@extends('core::admin.layouts.app')

@section('content')
    <div class="layui-card">
        <div class="layui-card-body">
            <div class="test-table-reload-btn" style="margin-bottom: 10px;">
                {{--                <form method="get"  action="">--}}
                {{--<div class="layui-inline">
                    <select name="coin" xm-select="coin" id="coin" lay-verify="required" class="layui-select">
                        <option value="">--币种--</option>
                        @foreach ($coin as $key=>$value)
                            <option value="{{ $value->symbol }}">{{ $value->symbol }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="layui-inline">
                    <select name="state" xm-select="state" id="state" lay-verify="required" class="layui-select">
                        <option value="">--状态--</option>
                        @foreach ($state as $key=>$value)
                            <option value="{{ $key }}">{{ $value }}</option>
                        @endforeach
                    </select>
                </div>

                <button class="layui-btn" data-type="reload">搜索</button>--}}
                <button class="layui-btn" data-type="create">生成钱包地址</button>
            </div>


            <script type="text/html" id="toolColumn">

                <a class="layui-btn layui-btn-xs" lay-event="edit_user">编辑</a>

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
                url: '{{ route('m.getcoin.api.admin.api.quest_wallet.index') }}?user_id='+{{$user_id}},
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
                id:"dataTable",
                cols: [[
                    //{field: 'right',title: '操作',toolbar: '#toolColumn',width:120},
                    {field: 'id',title: 'ID',width:80},
                    {field: 'chain',title: '主链',width:100},
                    {field: 'account_index',title: 'account',width:100},
                    {field: 'index',title: 'index',width:100},
                    {field: 'gas_balance',title: 'gas余额',width:100},
                    {field: 'description',title: '描述详情',width:150},
                    {field: 'created_at',title: '时间',width:170,templet: function (res) {
                            return moment(res.created_at).format("YYYY-MM-DD HH:mm:ss")}},
                    {field: 'address',title: '钱包地址'},

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
                if(e.event ==='edit_user'){

                    var url = '';
                    layer.open({
                        type: 2
                        , title: '编辑产品：' + data['name']
                        , content: url
                        , area: ['90%', '90%']
                    })
                }
            });



            let active = {
                reload: function(){

                    //执行重载
                    table.reload('dataTable', {
                        page: {
                            curr: 1 //重新从第 1 页开始
                        }
                        ,where: {
                            state: $("#state").val(),
                            symbol: $("#coin").val(),
                        }
                    }, 'data');
                },
                create:function(){

                    var url = '{{ route('m.getcoin.admin.quest_wallet.create') }}?user_id='+{{$user_id}};
                    layer.open({
                        type: 2
                        , title: '生成钱包地址'
                        , content: url
                        , area: ['90%', '90%']
                    })
                }
            };


            $('.layui-btn').on('click', function(){
                var type = $(this).data('type');
                active[type] ? active[type].call(this) : '';
            });
        })

    </script>
@endpush


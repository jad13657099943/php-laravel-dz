@extends('core::admin.layouts.app')

@section('content')
    <div class="layui-card">
        <div class="layui-card-body">

            <div class="test-table-reload-btn" style="margin-bottom: 10px;">

                <div class="layui-inline">
                    <input type="text" name="user" class="layui-input" id="user" placeholder="会员UID">
                </div>
                <div class="layui-inline">
                    <label class="layui-label">模块</label>
                    <div class="layui-inline">
                        <select id="chain" class="layui-input" lay-filter="aihao">
                            <option value=""></option>
                            @foreach($list as $team)
                                <option value="{{$team}}">{{$team}}</option>
                            @endforeach;
                        </select>
                    </div>
                </div>


                <button class="layui-btn" data-type="reload">搜索</button>
            </div>

            <script type="text/html" id="toolColumn">
                <a class="layui-btn layui-btn-xs" lay-event="edit">取消分配</a>
            </script>

            <table id="lay-table" lay-filter="lay-table" ></table>
        </div>

    </div>
@endsection

@push('after-scripts')

    <script>
        layui.use(['form', 'table', 'util', 'laydate','layer'], function () {

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
                url: '{{ route('m.dsy.api.admin.api.kernel.index5') }}',
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
                   // {type:'checkbox'},
                    {field: 'right', title: '操作', toolbar: '#toolColumn', width: 120},
                    {field: 'id', title: 'ID', width: 200},
                    {field: 'chain', title: '模块', width: 200},
                     {field: 'num', title: '每天分配', width: 200},
                       {field: 'min', title: '已分配', width: 200},
                       {field: 'max', title: '总分配', width: 200},
                    {
                        field: 'created_at', title: '创建时间', width: 500, templet: function (res) {
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
                if (e.event === 'edit') {
                    let URL='{{route('m.dsy.api.admin.api.kernel.del')}}';
                    let DATA={id:data.id};
                    ajax(URL,DATA);
                }
            });


            let active = {
                reload: function () {

                    let id = $('#user');
                    let chain = $('#chain');

                    //执行重载
                    table.reload('dataTable', {
                        page: {
                            curr: 1 //重新从第 1 页开始
                        }
                        , where: {
                            id: id.val(),
                            chain: chain.val(),
                        }
                    }, 'data');
                },
            };


            $('.layui-btn').on('click', function () {
                var type = $(this).data('type');
                active[type] ? active[type].call(this) : '';
            });
            table.on('checkbox(dataTable)', function(obj){
                console.log(obj)
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
                        layer.msg('操作失败');
                    }
                });
            }
        })


    </script>
@endpush


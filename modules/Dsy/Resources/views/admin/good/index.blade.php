@extends('core::admin.layouts.app')

@section('content')
    <div class="layui-card">
        <div class="layui-card-body">
            <div class="test-table-reload-btn" style="margin-bottom: 10px;">

                  <button class="layui-btn" data-type="create">添加</button>
              </div>

            <div class="test-table-reload-btn" style="margin-bottom: 10px;">


               {{-- <div class="layui-inline">
                    <label class="layui-label">模块</label>
                    <div class="layui-inline" style="width: 161px">
                        <select id="chain" class="layui-input" lay-filter="aihao">
                            <option value=""></option>
                            @foreach ($list as $item)
                                <option value={{$item}}>{{$item}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>--}}

               {{-- <button class="layui-btn" data-type="reload">搜索</button>--}}
            </div>
            <script type="text/html" id="toolColumn">

                <a class="layui-btn layui-btn-xs" lay-event="edit_user">编辑</a>
                <a class="layui-btn layui-btn-xs" lay-event="delete">删除</a>
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
                url: '{{ route('m.dsy.api.admin.api.good.index') }}',
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
                    {field: 'id', title: 'ID', width: 200},
                  //  {field: 'chain',title: '模块',width: 200},
                    {field: 'title', title: '名称', width: 400},
                    {field: 'money', title: '价格', width: 400},
                    {field: 'saves', title: '节点数',  width: 400},
                    {field: 'period', title: '合约周期', width: 400},
                    {field: 'start_time', title: '开挖时间', width: 400},
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
                            window.location.reload();
                        },
                        error:function (data) {
                            alert('操作失败');
                        }
                    });
                }
            });


            let active = {
                reload: function () {
                    let keyword = $('#keyword');
                    let id = $('#id');
                    let chain=$('#chain');


                    //执行重载
                    table.reload('dataTable', {
                        page: {
                            curr: 1 //重新从第 1 页开始
                        }
                        , where: {
                            chain: chain.val(),

                        }
                    }, 'data');
                },
               create: function () {

                    var url = '{{ route('m.dsy.admin.good.create') }}';
                    layer.open({
                        type: 2
                        , title: "添加矿机"
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


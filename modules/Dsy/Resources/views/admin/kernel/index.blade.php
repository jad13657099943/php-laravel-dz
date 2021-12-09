@extends('core::admin.layouts.app')

@section('content')
    <div class="layui-card">
        <div class="layui-card-body">
            <div class="test-table-reload-btn" style="margin-bottom: 10px;">
              <button class="layui-btn" data-type="create">添加</button>
            </div>
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
                url: '{{ route('m.dsy.api.admin.api.kernel.index') }}',
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
                    {field: 'id', title: 'ID', width: 200},
                    {field: 'symbol', title: '币种', width: 270,edit:'text'},
                    {field: 'price', title: '单价(USDT交易对)', width: 270,edit:'text'},
                    {field: 'free', title: '提现费用', width: 270,edit:'text'},
                    {field: 'min', title: '最小提现数量', width: 270,edit:'text'},
                ]],
                done: function (res, curr, count) {
                    exportData = res.data;
                },
                text: {
                    none: '没有可用数据'
                },
            });
            table.on('edit(lay-table)', function(obj){
                var value = obj.value //得到修改后的值
                    ,data = obj.data //得到所在行所有键值
                    ,field = obj.field; //得到字段
                let urls='{{ route('m.dsy.api.admin.api.kernel.update') }}'
                let text=field;
                let datas={'id':data.id};
                datas[text]=value;
                ajax(urls,datas);
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
                        alert('修改失败');
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
                create: function () {

                    layer.prompt({
                        title: '请输入币种(大写)',
                    }, function(val, index, elem){
                        let url='{{route('m.dsy.api.admin.api.kernel.add')}}';
                        let datas={symbol:val};
                        ajax(url,datas);
                    });
                }
            };


            $('.layui-btn').on('click', function () {
                var type = $(this).data('type');
                active[type] ? active[type].call(this) : '';
            });
        })


    </script>
@endpush


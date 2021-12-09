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


                <button class="layui-btn" data-type="reload">搜索</button>
            </div>
            <script type="text/html" id="toolColumn">

                <a class="layui-btn layui-btn-xs" lay-event="edit_order">手工打单</a>

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
                url: '{{ route('m.dsy.api.admin.api.kernel.index4') }}',
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
                    {field: 'user_id', title: 'UID', width: 200,edit:'text'},
                    {field: 'mobile',title: '手机',width: 200,edit: 'text'},
                    {field: 'email',title: '邮箱',width: 200,edit: 'text'},

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
                let urls='{{ route('m.dsy.api.admin.api.kernel.update4') }}'
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
                if (e.event === 'edit_grade') {
                    var url = '{{ route('m.dsy.admin.kernel.grade') }}?id=' + data.id;
                    layer.open({
                        type: 2
                        , title: '编辑：' + data['id'] + "信息"
                        , content: url
                        , area: ['90%', '90%']
                    })
                }
                if (e.event === 'edit_order') {
                    var url2 = '{{ route('m.dsy.admin.play.order') }}?id=' + data.user_id;
                    layer.open({
                        type: 2
                        , title: '编辑：' + data['user_id'] + "信息"
                        , content: url2
                        , area: ['90%', '90%']
                    })
                }
                if (e.event === 'edit_balance') {
                    var url3 = '{{ route('m.dsy.admin.kernel.balance') }}?id=' + data.user_id;
                    layer.open({
                        type: 2
                        , title: '编辑：' + data['id'] + "信息"
                        , content: url3
                        , area: ['90%', '90%']
                    })
                }
            });

            let active = {
                reload: function () {
                    let user = $('#user');
                    let chain = $('#chain');
                    let account=$('#account');
                    //执行重载
                    table.reload('dataTable', {
                        page: {
                            curr: 1 //重新从第 1 页开始
                        }
                        , where: {
                            user_id: user.val(),
                            chain: chain.val(),
                            account:account.val(),
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



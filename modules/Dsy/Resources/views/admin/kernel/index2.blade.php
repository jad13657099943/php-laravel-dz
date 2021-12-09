@extends('core::admin.layouts.app')

@section('content')
    <div class="layui-card">
        <div class="layui-card-body">
            <script type="text/html" id="toolColumn">
                <a class="layui-btn layui-btn-xs" lay-event="edit">编辑分配</a>
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
                url: '{{ route('m.dsy.api.admin.api.kernel.index2') }}',
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
                    {field: 'right', title: '操作', toolbar: '#toolColumn', width: 110},
                    {field: 'chain', title: '模块', width: 120},
                    {
                        field: 'save', title: '总节点数', width: 130
                    },
                    {
                        field: 'value', title: '总分配数(BZZ)', width: 130
                    },
                    {
                        field: 'day', title: '分配天数', width: 130
                    },
                    {
                        field: 'day_num', title: '每天分配数(BZZ)', width: 130
                    },
                    {
                        field: 'num', title: '每节点分配数(BZZ)', width: 160
                    },

                    {
                        field: 'start_at', title: '挖矿开始时间', width: 200, templet: function (res) {
                            return moment(res.start_at).format("YYYY-MM-DD HH:mm:ss")
                        }
                    },
                    {
                        field: 'start_at', title: '挖矿结束时间', width: 200, templet: function (res) {
                            return moment(res.end_at).format("YYYY-MM-DD HH:mm:ss")
                        }
                    },
                  /*  {field: 'num', title: '每节点挖矿', width: 470,edit:'text'},
                    {field: 'state', title: '1自动2手动', width: 470,edit:'text'},*/
                ]],
              /*  done: function (res, curr, count) {
                    exportData = res.data;
                },*/
                text: {
                    none: '没有可用数据'
                },
            });
           /* table.on('edit(lay-table)', function(obj){
                var value = obj.value //得到修改后的值
                    ,data = obj.data //得到所在行所有键值
                    ,field = obj.field; //得到字段
                let urls='{{ route('m.dsy.api.admin.api.kernel.update2') }}'
                let text=field;
                let datas={'id':data.id};
                datas[text]=value;
               /!* ajax(urls,datas);*!/
            });*/

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
                if (e.event === 'edit') {
                    layer.open({
                        title:'分配设置',
                        content: '<div class="layui-inline">\n' +
                            '      <label class="layui-form-label">挖矿开始时间设置</label>\n' +
                            '      <div class="layui-input-inline">\n' +
                            '        <input type="date" name="date" id="start" lay-verify="datetime" placeholder="yyyy-MM-dd HH:mm:ss" autocomplete="off" class="layui-input">\n' +
                            '      </div>\n' +
                            '    </div>'+'<div class="layui-inline">\n' +
                            '      <label class="layui-form-label">挖矿结束时间设置</label>\n' +
                            '      <div class="layui-input-inline">\n' +
                            '        <input type="date" name="date" id="end" lay-verify="datetime" placeholder="yyyy-MM-dd HH:mm:ss" autocomplete="off" class="layui-input">\n' +
                            '      </div>\n' +
                            '    </div>'+'<div class="layui-inline">\n' +
                            '      <label class="layui-form-label">分配数(BZZ数)</label>\n' +
                            '      <div class="layui-input-inline">\n' +
                            '        <input type="text" name="date" id="value" lay-verify="datetime"  autocomplete="off" class="layui-input">\n' +
                            '      </div>\n' +
                            '    </div>'
                        ,btn: ['确定']
                        ,yes: function(index, layero){
                            let start=$('#start').val();
                            let end=$('#end').val();
                            let id=data.id;
                            let value=$('#value').val();
                            let DATA={id:id,start_at:start,end_at:end,value:value};
                            let URL='{{route('m.dsy.api.admin.api.kernel.allot')}}';
                            ajax(URL,DATA);
                        }
                        ,cancel: function(){
                            //右上角关闭回调

                            //return false 开启该代码可禁止点击该按钮关闭
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


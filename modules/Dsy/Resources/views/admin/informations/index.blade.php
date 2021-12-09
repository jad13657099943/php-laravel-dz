@extends('core::admin.layouts.app')

@section('content')
    <div class="layui-card">
        <div class="layui-card-body">
            <div class="test-table-reload-btn" style="margin-bottom: 10px;">

                  <button class="layui-btn" data-type="create">添加</button>
              </div>
            <script type="text/html" id="toolColumn">
               <a class="layui-btn layui-btn-xs" lay-event="edit_user">编辑</a>
               <a class="layui-btn layui-btn-xs" lay-event="del">删除</a>
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
                url: '{{ route('m.dsy.api.admin.api.information.index') }}',
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
                    {field: 'id', title: 'ID', width: 400},
                    {field: 'title', title: '名称', width: 800},
                    {
                        field: 'created_at', title: '创建时间', width: 800, templet: function (res) {
                            return moment(res.created_at).format("YYYY-MM-DD HH:mm:ss")
                        }
                    },
                    {
                        field: 'updated_at', title: '更新时间', width: 800, templet: function (res) {
                            return moment(res.created_at).format("YYYY-MM-DD HH:mm:ss")
                        }
                    }
                ]],
                text: {
                    none: '没有可用数据'
                },
            });
           table.on("tool(lay-table)", function (e) {
                var data = e.data;
                if (e.event === 'edit_user') {
                    var url = '{{ route('m.dsy.admin.information.edit') }}?id=' + data.id;
                    layer.open({
                        type: 2
                        , title: '编辑：' + data['id'] + "信息"
                        , content: url
                        , area: ['90%', '90%']
                    })
                }
                if (e.event==='del'){

                        var urls = '{{ route('m.dsy.api.admin.api.information.del') }}';
                        var id=data.id;
                        var datas={id:id};
                        $.ajax({
                            url: urls,
                            dataType: 'json',
                            type: 'post',
                            data: datas,
                            success: function (data) {
                                window.location.reload();
                            },
                            error:function (data) {
                                window.location.reload();
                            }

                    })
                }
            });


            let active = {
                reload: function () {
                    let keyword = $('#keyword');
                    let id = $('#id');
                    let created_at = $('#created_at');
                    let is_export = $("input[type='checkbox']").is(':checked');
                    console.log(is_export);
                    //执行重载
                    table.reload('dataTable', {
                        page: {
                            curr: 1 //重新从第 1 页开始
                        }
                        , where: {
                            id: id.val(),
                            keyword: keyword.val(),
                            created_at: created_at.val(),
                            fund_grade: $('#fund_grade').val(),
                            is_site: $('#is_site').val(),
                            parent_id: $('#parent_id').val(),
                            farm_grade: $('#farm_grade').val(),
                            is_export: is_export
                        }
                    }, 'data');
                },
               create: function () {

                    var url = '{{ route('m.dsy.admin.information.create') }}';
                    layer.open({
                        type: 2
                        , title: "添加资讯"
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


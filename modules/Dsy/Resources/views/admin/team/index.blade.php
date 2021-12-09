@extends('core::admin.layouts.app')

@section('content')
    <div class="layui-card">
        <div class="layui-card-body">
            <script type="text/html" id="toolColumn">
                <a class="layui-btn layui-btn-xs" lay-event="add">赠送</a>
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

            var ins1=  table.render({
                elem: '#lay-table',
                url: '{{ route('m.dsy.api.admin.api.team.index') }}',
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
                   // {field: 'chain', title: '模块', width: 80},
                   // {field: 'zhi', title: '直推', width: 150,edit:'text'},
                  //  {field: 'fen', title: '分币比例(百分比)', width: 150,edit:'text'},
                  //  {field: 'ping', title: '平级', width: 150,edit:'text'},
                  //  {field: 'a_zhe', title: 'a.业绩折扣', width: 150,edit:'text'},
                    {field: 'a_fen', title: '会员矿池分红', width: 150,edit:'text'},
                    {field: 'a_shou', title: '会员销售提成', width: 150,edit:'text'},
                    {field: 'a_xiao', title: '会员投资资格', width: 150,edit:'text'},
                  //  {field: 'a_da', title: '会员团队业绩', width: 150,edit:'text'},
                   // {field: 'b_zhe', title: 'b.业绩折扣', width: 150,edit:'text'},
                    {field: 'b_fen', title: '区代矿池分红', width: 150,edit:'text'},
                    {field: 'b_shou', title: '区代销售提成', width: 150,edit:'text'},
                    {field: 'b_xiao', title: '区代投资资格', width: 150,edit:'text'},
                    {field: 'b_da', title: '区代团队业绩', width: 150,edit:'text'},
                   // {field: 'c_zhe', title: 'c.业绩折扣', width: 150,edit:'text'},
                    {field: 'c_fen', title: '市代矿池分红', width: 150,edit:'text'},
                    {field: 'c_shou', title: '市代销售提成', width: 150,edit:'text'},
                    {field: 'c_xiao', title: '市代投资资格', width: 150,edit:'text'},
                    {field: 'c_da', title: '市代团队业绩', width: 150,edit:'text'},
                  //  {field: 'd_zhe', title: 'd.业绩折扣', width: 150,edit:'text'},
                    {field: 'd_fen', title: '省代矿池分红', width: 150,edit:'text'},
                    {field: 'd_shou', title: '省代销售提成', width: 150,edit:'text'},
                    {field: 'd_xiao', title: '省代投资资格', width: 150,edit:'text'},
                    {field: 'd_da', title: '省代团队业绩', width: 150,edit:'text'},
                  //  {field: 'e_zhe', title: 'e.业绩折扣', width: 150,edit:'text'},
                    {field: 'e_fen', title: '分公司团队矿池分红', width: 150,edit:'text'},
                    {field: 'e_shou', title: '分公司团队销售提成', width: 150,edit:'text'},
                  //  {field: 'e_xiao', title: 'e.小区', width: 150,edit:'text'},
                  //  {field: 'e_da', title: 'e.大区', width: 150,edit:'text'},
                  //  {field: 'f_zhe', title: 'f.业绩折扣', width: 150,edit:'text'},
                    {field: 'f_fen', title: '直推分公司团队矿池分红', width: 150,edit:'text'},
                    {field: 'f_shou', title: '直推分公司团队销售提成', width: 150,edit:'text'},
                  //  {field: 'f_xiao', title: 'f.小区', width: 150,edit:'text'},
                  //  {field: 'f_da', title: 'f.大区', width: 150,edit:'text'},
                  //  {field: 'g_zhe', title: 'g.业绩折扣', width: 150,edit:'text'},
                    {field: 'g_fen', title: '合伙人全球矿池分红', width: 150,edit:'text'},
                    {field: 'g_shou', title: '合伙人全球销售分红', width: 150,edit:'text'},
                  //  {field: 'g_xiao', title: 'g.小区', width: 150,edit:'text'},
                  //  {field: 'g_da', title: 'g.大区', width: 150,edit:'text'},
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
                let urls='{{ route('m.dsy.api.admin.api.team.update') }}'
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
            };


            $('.layui-btn').on('click', function () {
                var type = $(this).data('type');
                active[type] ? active[type].call(this) : '';
            });
        })


    </script>
@endpush


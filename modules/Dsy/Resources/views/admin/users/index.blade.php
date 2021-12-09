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
                <div class="layui-inline">
                   {{-- <label class="layui-label">模块</label>
                    <div class="layui-inline">
                        <select id="chain" class="layui-input" lay-filter="aihao">
                            <option value=""></option>
                            @foreach($list as $team)
                                <option value="{{$team}}">{{$team}}</option>
                            @endforeach;
                        </select>
                    </div>--}}
                </div>


                <button class="layui-btn" data-type="reload">搜索</button>
            </div>

           <script type="text/html" id="toolColumn">
               {{-- <a class="layui-btn layui-btn-xs" lay-event="edit">编辑释放</a>
                <a class="layui-btn layui-btn-xs" lay-event="edit2">编辑质押</a>--}}
                <a class="layui-btn layui-btn-xs" lay-event="del">删除</a>
                <a class="layui-btn layui-btn-xs" lay-event="password">修改密码</a>
            </script>
          {{--  <div class="layui-btn-group demoTable">
                <button class="layui-btn" id="edit_time" data-type="getCheckData">设置产币时间</button>
                <button class="layui-btn" id="add_save" data-type="getCheckLength">分配算力</button>
                <button class="layui-btn" id="add_day_save" data-type="getCheckLength">每天分配算力</button>
            </div>--}}
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
                url: '{{ route('m.dsy.api.admin.api.user.index') }}',
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
                    {type:'checkbox'},
                    {field: 'right', title: '操作', toolbar: '#toolColumn', width: 230},
                    {field: 'id', title: 'ID', width: 200},
                    {field: 'mobile', title: '手机号', width: 250},
                    {field: 'email', title: '邮箱', width: 250},
                 /*   {field: 'grade', title: '等级', width: 300},
                    {field: 'zhi', title: '直推数', width: 300},
                    {field: 'team', title: '团队数', width: 300},*/
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
                    let chain=$('#chain').val();
                    if (chain==''){
                        layer.msg('请选择模块!');
                        return;
                    }
                    let url = '{{ route('m.dsy.admin.user.edit') }}?id='+data.id+'&chain='+chain;
                    layer.open({
                        type: 2
                        , title: "编辑释放"
                        , content: url
                        , area: ['90%', '90%']
                    })
                }
                if (e.event === 'edit2') {
                    let chain='FIL'/*$('#chain').val()*/;
                   /* if (chain==''){
                        layer.msg('请选择模块!');
                        return;
                    }*/
                    let url = '{{ route('m.dsy.admin.user.edit2') }}?id='+data.id+'&chain='+chain;
                    layer.open({
                        type: 2
                        , title: "编辑质押"
                        , content: url
                        , area: ['90%', '90%']
                    })
                }
                if (e.event === 'del') {
                    let url = '{{ route('m.dsy.api.admin.api.user.del') }}';
                    let id=data.id;
                    let DATA={id:id};
                    ajax(url,DATA);
                }
                if (e.event === 'password') {

                    let id=data.id;
                    layer.open({
                        title:'修改密码',
                        content: '<div class="layui-inline">\n' +
                            '      <label class="layui-form-label">新密码</label>\n' +
                            '      <div class="layui-input-inline">\n' +
                            '        <input type="text" name="date" id="date" lay-verify="date"  autocomplete="off" class="layui-input">\n' +
                            '      </div>\n' +
                            '    </div>'
                        ,btn: ['确定']
                        ,yes: function(index, layero){
                            let date=$('#date').val();
                            if (date==''){
                                layer.msg('请输入密码!');
                                return;
                            }
                            let datas={id:id,password:date};
                            let URL='{{ route('m.dsy.api.admin.api.user.password') }}';
                            ajax(URL,datas);
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

                    let id = $('#user');
                    let chain = $('#chain');
                    let account=$('#account');
                    //执行重载
                    table.reload('dataTable', {
                        page: {
                            curr: 1 //重新从第 1 页开始
                        }
                        , where: {
                            id: id.val(),
                            chain: chain.val(),
                            account:account.val(),
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
            table.on('checkbox(dataTable)', function(obj){
                console.log(obj)
            });

            $('#edit_time').click(function(){
                layer.open({
                    title:'设置开挖时间',
                    content: '<div class="layui-inline">\n' +
                        '      <label class="layui-form-label">日期</label>\n' +
                        '      <div class="layui-input-inline">\n' +
                        '        <input type="date" name="date" id="date" lay-verify="date" placeholder="yyyy-MM-dd" autocomplete="off" class="layui-input">\n' +
                        '      </div>\n' +
                        '    </div>'
                    ,btn: ['确定']
                    ,yes: function(index, layero){

                        let chain=$('#chain').val();
                        if (chain==''){
                            layer.msg('请选择模块!');
                            return;
                        }
                        let id=new Array();
                        let date=$('#date').val();
                        if (date==''){
                            layer.msg('请选择时间!');
                            return;
                        }
                        var checkStatus = table.checkStatus('dataTable')
                            ,data = checkStatus.data;
                        for (var i in data){
                            id[i]=data[i].id;
                        }
                        if (id==''){
                            layer.msg('请选择用户!');
                            return;
                        }
                        let datas={id:id,chain:chain,date:date};
                        let URL='{{ route('m.dsy.api.admin.api.user.time') }}';
                        ajax(URL,datas);
                    }
                    ,cancel: function(){
                        //右上角关闭回调

                        //return false 开启该代码可禁止点击该按钮关闭
                    }
                });
            });
            $('#add_save').click(function(){
                layer.open({
                    title:'分配算力',
                    content: '<div class="layui-inline">\n' +
                        '      <label class="layui-form-label">算力数</label>\n' +
                        '      <div class="layui-input-inline">\n' +
                        '        <input type="text" name="date" id="date" lay-verify="date"  autocomplete="off" class="layui-input">\n' +
                        '      </div>\n' +
                        '    </div>'
                    ,btn: ['确定']
                    ,yes: function(index, layero){

                        let chain=$('#chain').val();
                        if (chain==''){
                            layer.msg('请选择模块!');
                            return;
                        }
                        let id=new Array();
                        let date=$('#date').val();
                        if (date==''){
                            layer.msg('请输入算力数!');
                            return;
                        }
                        var checkStatus = table.checkStatus('dataTable')
                            ,data = checkStatus.data;
                        for (var i in data){
                            id[i]=data[i].id;
                        }
                        if (id==''){
                            layer.msg('请选择用户!');
                            return;
                        }
                        let datas={id:id,chain:chain,date:date};
                        let URL='{{ route('m.dsy.api.admin.api.user.save') }}';
                        ajax(URL,datas);
                    }
                    ,cancel: function(){
                        //右上角关闭回调

                        //return false 开启该代码可禁止点击该按钮关闭
                    }
                });
            });
            $('#add_day_save').click(function(){
                layer.open({
                    title:'每天分配算力',
                    content: '<div class="layui-inline">\n' +
                        '      <label class="layui-form-label">每日分配</label>\n' +
                        '      <div class="layui-input-inline">\n' +
                        '        <input type="text" name="date" id="date" lay-verify="date"  autocomplete="off" class="layui-input">\n' +
                        '      </div>\n' +
                        '    </div>'+'<div class="layui-inline">\n' +
                        '      <label class="layui-form-label">总分配</label>\n' +
                        '      <div class="layui-input-inline">\n' +
                        '        <input type="text" name="date" id="dates" lay-verify="date"  autocomplete="off" class="layui-input">\n' +
                        '      </div>\n' +
                        '    </div>'
                    ,btn: ['确定']
                    ,yes: function(index, layero){

                        let chain=$('#chain').val();
                        if (chain==''){
                            layer.msg('请选择模块!');
                            return;
                        }
                        let id=new Array();
                        let date=$('#date').val();
                        let dates=$('#dates').val();
                        if (date==''){
                            layer.msg('请输入每日分配数!');
                            return;
                        }
                        if (dates==''){
                            layer.msg('请输入总分配数!');
                            return;
                        }
                        if (dates%date!=0){
                            layer.msg('请输入倍数!');
                            return;
                        }
                        var checkStatus = table.checkStatus('dataTable')
                            ,data = checkStatus.data;
                        for (var i in data){
                            id[i]=data[i].id;
                        }
                        if (id==''){
                            layer.msg('请选择用户!');
                            return;
                        }
                        let DATA={id:id,chain:chain,date:date,dates:dates};
                        let URL='{{ route('m.dsy.api.admin.api.user.day') }}';
                        ajax(URL,DATA);
                    }
                    ,cancel: function(){
                        //右上角关闭回调

                        //return false 开启该代码可禁止点击该按钮关闭
                    }
                });
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


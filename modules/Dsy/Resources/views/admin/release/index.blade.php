@extends('core::admin.layouts.app')

@section('content')
    <div class="layui-card">
        <div class="layui-form-item">
            <label class="layui-form-label">今日发放</label>
            <div class="layui-input-inline" style="width: 100px">
                <input  type="text"  value="{{$list->award}}" id="award" autocomplete="off" class="layui-input">
            </div>
            <label style="width: 35px;" class="layui-form-label">fil/T</label>
            <label class="layui-form-label">当前全网有效算力</label>
            <div class="layui-input-inline">
                <input type="text" readonly="readonly" value="{{$save}}" id="award" autocomplete="off" class="layui-input">
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label"></label>
            <div class="layui-input-inline">

                <button class="layui-btn" lay-submit lay-filter="set" id="set">立即提交</button>
            </div>
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

        })

        $('#set').click(function () {
            var url='{{route('m.dsy.api.admin.api.release.edit')}}';
            var award=$('#award').val();
            $.ajax({
                url: url,
                dataType: 'json',
                type: 'post',
                data: {award:award},
                success: function (data) {
                    layer.msg('修改成功');
                },
                error:function (data) {
                    layer.msg('修改失败');
                }
            });


        })

    </script>
@endpush


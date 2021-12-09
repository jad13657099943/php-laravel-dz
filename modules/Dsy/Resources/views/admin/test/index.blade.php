@extends('core::admin.layouts.app')

@section('content')
    <div class="layui-card">

        <div class="layui-form-item">
            <label class="layui-form-label">每T质押</label>
            <div class="layui-input-inline">
                <input type="text" value="{{$list}}" id="zhi" autocomplete="off" class="layui-input">
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
            var url='{{route('m.dsy.api.admin.api.test.edit')}}';
            var zhi=$('#zhi').val();
            $.ajax({
                url: url,
                dataType: 'json',
                type: 'post',
                data: {zhi:zhi},
                success: function (data) {
                    window.location.reload();
                },
                error:function (data) {
                   alert('操作失败');
                }
            });


        })

    </script>
@endpush


@extends('core::admin.layouts.app')

@section('content')
    <form class="layui-form" action="">

        <div class="layui-card">
            <div class="layui-card-body">
                {{csrf_field()}}
                <div class="layui-form-item">
                    <label class="layui-form-label">标题</label>
                    <div class="layui-input-inline">
                        <input type="text" id="name" autocomplete="off" class="layui-input">
                    </div>
                </div>
                <div id="test12" class="demo-tree-more"></div>
                <div class="layui-form-item">
                    <label class="layui-form-label"></label>
                    <div class="layui-input-inline">
                        <button class="layui-btn" lay-submit lay-filter="add" id="add">立即提交</button>
                    </div>
                </div>

            </div>
        </div>
    </form>

@endsection

@push('after-scripts')
    <script>
        layui.use(['tree', 'util'], function() {
            var $ = layui.$
                , layer = layui.layer
                , util = layui.util
            $('#add').click(function () {
                var url='{{route('m.user.api.admin.api.article_cate.add')}}';
                var name=$('#name').val();
                var data={name:name};
                $.ajax({
                    url: url,
                    dataType: 'json',
                    type: 'post',
                    data:data,
                    async:false,
                    success: function (src) {

                        window.parent.location.reload();

                    },
                    error:function (data) {
                        layer.msg(data.responseJSON.message);
                    }
                });
            })
        });

    </script>
@endpush

<style>
    .layui-form-item .layui-input-inline {
        width: 800px !important;
    }

    .layui-form-label {
        box-sizing: initial;
        width: 200px !important;
    }
</style>

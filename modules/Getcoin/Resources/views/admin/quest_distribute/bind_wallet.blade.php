@extends('core::admin.layouts.app')

@section('content')
    <div class="layui-card">
        <div class="layui-card-body">
            <form method="post"  class="layui-form" lay-filter="test1">
                {{csrf_field()}}


                <div class="layui-form-item">
                    <label class="layui-form-label">开始钱包序号</label>
                    <div class="layui-input-inline">
                        <input type="number" name="min_index" value="" autocomplete="off" class="layui-input">
                    </div>
                </div>

                <div class="layui-form-item">
                    <label class="layui-form-label">结束钱包序号</label>
                    <div class="layui-input-inline">
                        <input type="number" name="max_index" value="" autocomplete="off" class="layui-input">
                    </div>
                </div>
                <div class="layui-form-item">
                    <label class="layui-form-label">选择主链</label>
                    <div class="layui-input-inline">
                        <select name="chain" lay-verify="required">
                            @foreach ($coin as $key=> $vo)
                                <option value="{{ $vo }}">{{ $vo }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="layui-form-item">
                    <label class="layui-form-label"></label>
                    <div class="layui-input-inline">
                        <input type="hidden" name="id" value="{{$id}}">
                        <button class="layui-btn" lay-submit lay-filter="add">立即提交</button>
                    </div>
                </div>
            </form>
        </div>
    </div>


@endsection

@push('after-scripts')
    <script>

        function ityzl_SHOW_LOAD_LAYER() {
            return layer.msg('处理中...', {icon: 16, shade: [0.5, '#b2b2b2'], scrollbar: false, time: 0});
        }

        function ityzl_CLOSE_LOAD_LAYER(index) {
            layer.closeAll();
            layer.close(index);
        }

        layui.use(['form', 'table', 'layedit','laydate'], function () {
            var $ = layui.$
                , form = layui.form
                , laydate = layui.laydate;
            form.render(null, 'test1');
            laydate.render({
                elem: '#start_time'
                , type: 'datetime'
            });

            form.on('submit(add)', function(data){

                i = ityzl_SHOW_LOAD_LAYER();
                var url = '{{ route('m.getcoin.api.admin.api.quest_distribute.bind_wallet') }}';
                $.ajax({
                    type: 'post',
                    url: url,
                    data: data.field,
                    dataType: 'json',
                    success: function (resp) {
                        ityzl_CLOSE_LOAD_LAYER(i);
                        layer.msg(resp.msg, {icon: 1, time: 2000, shade: [0.8, '#393D49']}, function () {
                            window.parent.location.reload();
                        });
                    },
                    error: function (err) {
                        ityzl_CLOSE_LOAD_LAYER(i);
                        layer.msg('请求失败：code' + err.status + "，msg：" + err.statusText, {time: 2000});
                    }
                });


                return false;
            });

        })
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

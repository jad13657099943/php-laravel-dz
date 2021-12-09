@extends('core::admin.layouts.app')

@section('content')
    <div class="layui-card">
        <div class="layui-card-body">
            {{csrf_field()}}
            <div class="layui-form-item">
                <label class="layui-form-label">产品</label>
                <div class="layui-input-inline" style="width: 161px">
                    <select id="order" class="layui-input" lay-filter="aihao">
                        @foreach ($list as $k=> $item)
                            <option value={{$k}}>{{$item}}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="layui-form-item">
                <label class="layui-form-label">数量</label>
                <div class="layui-input-inline">
                    <input type="text" id="num" value="1" autocomplete="off" class="layui-input">
                </div>
            </div>
            <div class="layui-inline">
                <label class="layui-form-label">日期</label>
                <div class="layui-input-inline">
                    <input type="text" class="layui-input" id="date" placeholder="yyyy-MM-dd HH:mm:ss">
                </div>
            </div>

            <div class="layui-form-item">
                <label class="layui-form-label">入单方式</label>
                <div class="layui-input-inline">
                    <input type="text" id="type"  autocomplete="off" class="layui-input">
                </div>
            </div>

            <div class="layui-form-item">
                <label class="layui-form-label"></label>
                <div class="layui-input-inline">
                    <button class="layui-btn" lay-submit lay-filter="add" id="add">立即打单</button>
                </div>
            </div>

        </div>
    </div>


@endsection

@push('after-scripts')
    <script>
        layui.use('laydate', function() {
            var laydate = layui.laydate;
            laydate.render({
                elem: '#date'
                ,type: 'datetime'
            });
        });
        $('#add').click(function () {
            var id ={{$id}};
            var url = '{{ route('m.dsy.api.admin.api.kernel.order') }}';
            var order = $('#order').val();
            var num = $('#num').val();
            let date=$('#date').val();
            let type=$('#type').val();
            $.ajax({
                url: url,
                dataType: 'json',
                type: 'post',
                data: {id: id, order: order, num: num,date:date,type:type},
                success: function (data) {

                    window.parent.location.reload();
                },
                error: function (data) {
                    alert('打单失败');
                }
            });
            return false;
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

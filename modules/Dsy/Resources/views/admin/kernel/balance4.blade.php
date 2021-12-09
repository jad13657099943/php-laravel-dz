@extends('core::admin.layouts.app')

@section('content')
    <div class="layui-card">
        <div class="layui-card-body">
            {{csrf_field()}}
            <div class="layui-form-item">
                <label class="layui-form-label">币种</label>
                <div class="layui-input-inline" style="width: 161px">
                    <select id="symbol" class="layui-input" lay-filter="aihao">
                        <option value='USDT'>USDT</option>
                        @foreach ($list as $k=> $item)
                            <option value={{$item}}>{{$item}}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label">操作</label>
                <div class="layui-input-inline" style="width: 161px">
                    <select id="set" class="layui-input" lay-filter="aihao">

                            <option value='1'>增加</option>
                            <option value='2'>减少</option>

                    </select>
                </div>
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label">数量</label>
                <div class="layui-input-inline">
                    <input type="text" id="num" value="0" autocomplete="off" class="layui-input">
                </div>
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label"></label>
                <div class="layui-input-inline">
                    <button class="layui-btn" lay-submit lay-filter="add" id="add">立即操作</button>
                </div>
            </div>

        </div>
    </div>


@endsection

@push('after-scripts')
    <script>
        $('#add').click(function () {
            var  id={{$id}};
            var     url = '{{ route('m.dsy.api.admin.api.kernel.balance') }}';
            var   symbol=$('#symbol').val();
            var    set=$('#set').val();
            var   num=$('#num').val();
            $.ajax({
                url: url,
                dataType: 'json',
                type: 'post',
                data:{id:id,symbol:symbol,set:set,num:num},
                success: function (data) {

                    window.parent.location.reload();
                },
                error:function (data) {
                    alert('操作失败');
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

@extends('core::admin.layouts.app')

@section('content')
    <div class="layui-card">
        <div class="layui-card-body">
            {{csrf_field()}}
                <div class="layui-form-item">
                    <label class="layui-form-label">等级</label>
                    <div class="layui-input-inline" style="width: 161px">
                        <select id="grade" class="layui-input" lay-filter="aihao">
                            @foreach ($list as $k=> $item)
                                <option value={{$k}}>{{$item}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

            <div class="layui-form-item">
                <label class="layui-form-label"></label>
                <div class="layui-input-inline">
                    <button class="layui-btn" lay-submit lay-filter="add" id="add">立即修改</button>
                </div>
            </div>

        </div>
    </div>


@endsection

@push('after-scripts')
    <script>
            $('#add').click(function () {
                var  id={{$id}};
                var     url = '{{ route('m.dsy.api.admin.api.kernel.grade') }}';
                var   grade=$('#grade').val();
                $.ajax({
                    url: url,
                    dataType: 'json',
                    type: 'post',
                    data:{id:id,grade:grade},
                    success: function (data) {

                        window.parent.location.reload();
                    },
                    error:function (data) {
                        alert('修改失败');
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

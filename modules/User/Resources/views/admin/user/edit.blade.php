@extends('core::admin.layouts.app')

@section('content')
    <div class="layui-card">
        <div class="layui-card-body">
            <form method="post"  class="layui-form" lay-filter="test1">
                {{csrf_field()}}


                <div class="layui-form-item">
                    <label class="layui-form-label">会员ID</label>
                    <div class="layui-input-inline">
                        <input type="text"  value="{{ $info->user_id }}"
                               disabled autocomplete="off" class="layui-input">
                    </div>
                </div>

                <div class="layui-form-item" style="display: none">
                    <label class="layui-form-label">等级</label>
                    <div class="layui-input-inline">

                        <select name="grade" lay-verify="required">
                            @foreach ($grade_list as $key=> $vo)
                                <option value="{{ $key }}" @if($info->grade== $key ) selected @endif >{{ $vo }}</option>
                            @endforeach
                        </select>

                    </div>
                </div>

                <div class="layui-form-item">
                    <label class="layui-form-label">修改登录密码</label>
                    <div class="layui-input-inline">
                        <input type="text" name="password" value="" placeholder="不修改不要填写" autocomplete="off"
                               class="layui-input">
                    </div>
                </div>

                <div class="layui-form-item">
                    <label class="layui-form-label">修改支付密码</label>
                    <div class="layui-input-inline">
                        <input type="text" name="pay_password" value="" placeholder="不修改不要填写" autocomplete="off"
                               class="layui-input">
                    </div>
                </div>

                <div class="layui-form-item">
                    <label class="layui-form-label">成为组长</label>
                    <div class="layui-input-inline">
                        <input type="radio" name="is_leader" value="1" title="是" @if($info->is_leader==1)  checked @endif>
                        <input type="radio" name="is_leader" value="0" title="否" @if($info->is_leader==0)  checked @endif>
                    </div>
                </div>


                <div class="layui-form-item">
                    <label class="layui-form-label"></label>
                    <div class="layui-input-inline">
                        <input type="hidden" name="user_id" value="{{ $info->user_id  }}">
                        <button class="layui-btn" lay-submit lay-filter="add">立即提交</button>
                    </div>
                </div>
            </form>
        </div>
    </div>


@endsection

@push('after-scripts')
    <script>
        layui.use(['form', 'table', 'layedit','laydate'], function () {
            var $ = layui.$
                , form = layui.form;
            form.render(null, 'test1');


            form.on('submit(add)', function(data){

                var url = '{{ route('m.user.api.admin.api.user.user_edit') }}';
                $.post(url,data.field,function(res){

                    console.log(res);

                    //if(res.code==200){

                    layer.msg(res.msg,{icon: 1,time: 2000,shade: [0.8, '#393D49']},function(){

                        window.parent.location.reload();
                    });

                    /*}else{
                        layer.msg(res.msg, {time: 2000});
                    }*/
                },'json');
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

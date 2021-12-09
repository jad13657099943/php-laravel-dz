<?php

namespace Modules\Dsy\Http\Controllers\admin;


class SettingsController extends \Modules\Core\Http\Controllers\Admin\App\SettingsController
{
    protected $schema = [
        'app_logo'          => [
            'key'         => 'app_logo',
            'type'        => 'image',
            'title'       => 'Logo',
            'value'       => '',
            'description' => 'App Logo',
        ],

        'app_name'          => [
            'key'         => 'app_name',
            'type'        => 'text',
            'title'       => '系统名称',
            'value'       => 'App名字',
            'description' => 'App 名字',
        ],
        'app_cover_images'  => [
            'key'         => 'cover_images',
            'type'        => 'image_list',
            'title'       => '启动图',
            'value'       => [],
            'description' => 'App启动图',
        ],
        'app_home_banner'          => [
            'key'         => 'app_home_banner',
            'type'        => 'image_list',
            'title'       => '轮播图',
            'value'       => [],
            'description' => '轮播图: 宽度: 343x112像素左右',
        ],
        'share_link'        => [
            'key'         => 'share_link',
            'type'        => 'text',
            'title'       => '分享链接',
            'value'       => '',
            'description' => '分享链接',
        ],
        'default_coin'      => [
            'key'         => 'default_coin',
            'type'        => 'radio',
            'title'       => '默认币种',
            'value'       => 'USDT',
            'data'        => [],
            'description' => '默认币种',
        ],
        'default_coin_list' => [
            'key'         => 'default_coin_list',
            'type'        => 'checkbox',
            'title'       => '可用币种列表',
            'value'       => ['USDT', 'ETH', 'BTC'],
            'data'        => [],
            'description' => '全部可以用的币种',
        ],
        'ios_version'       => [
            'key'         => 'ios_version',
            'type'        => 'text',
            'title'       => '苹果版本号',
            'value'       => '1.0.0',
            'description' => 'IOS Version ',
        ],
        'ios_download'      => [
            'key'         => 'ios_download',
            'type'        => 'text',
            'title'       => '苹果下载地址',
            'value'       => '1.0.0',
            'description' => '正式版下载地址',
        ],
        'android_version'   => [
            'key'         => 'android_version',
            'type'        => 'text',
            'title'       => '安卓版本号',
            'value'       => '1.0.0',
            'description' => 'android Version ',
        ],
        'android_download'  => [
            'key'         => 'android_download',
            'type'        => 'text',
            'title'       => '安卓下载地址',
            'value'       => '1.0.0',
            'description' => '正式版下载地址',
        ],
        /*'share_link' => [
            'key' => 'share_link',
            'type' => 'text',
            'title' => '分享链接',
            'value' => '',
            'description' => '分享链接',
        ],*/
        'admin_mobile'      => [
            'key'         => 'admin_mobile',
            'type'        => 'text',
            'title'       => '管理员手机',
            'value'       => '',
            'description' => '可用于接收系统通知',
        ],
        'admin_email'       => [
            'key'         => 'admin_email',
            'type'        => 'text',
            'title'       => '管理员邮箱',
            'value'       => '',
            'description' => '可用于接收系统通知',
        ],

    ];
}

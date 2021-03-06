// 注意: pm2 的执行权限问题. 如果web服务器(apache)创建的是wwww:www权限
// pm2 是root执行权限会造成fpm下www权限问题
module.exports = {
    apps: [
        {
            name: 'laravel_schedule',
            interpreter: "php",
            script: 'artisan',
            args: ["schedule:run"],
            instances: 1,
            exec_mode: 'fork',
            cron_restart: '1 0 * * *',
            watch: false,
            autorestart: false
        },
        {
            name: 'laravel_queue_worker',
            interpreter: "php",
            script: 'artisan',
            args: ["queue:work", "--tries=3", "--sleep=1"],
            instances: 1,
            exec_mode: 'fork',
            /*watch: [
                '/xxx/storage/framework',
            ],
            ignore_watch:[
                '/xxx/storage/framework/cache',
                '/xxxx/storage/framework/sessions',
            ]*/
        },
        // laravel echo server 有项目需求可以自定义开启
        // {
        //     name: 'laravel_echo_server',
        //     script: 'laravel-echo-server start',
        //     args: [],
        //     instances: 1,
        // },

    ]
}

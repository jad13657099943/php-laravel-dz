<?php


namespace Modules\zfil\Services\Traits;


use Modules\zfil\Services\ProjectConfigService;

trait ConfigTraits
{

    public function getValueByKey($key)
    {

        $service = resolve(ProjectConfigService::class);
        $info = $service->one([
            'key' => $key
        ]);

        $value = $info->value ?? '';
        return is_numeric($value) ? floatval($value) : $value;
    }


    public function getConfigAll()
    {

        $seconds = 600;
        $list = \Cache::remember('project_config',$seconds, function () {
            $service = resolve(ProjectConfigService::class);
            $list = $service->all(null)->pluck('value', 'key')->toArray();
            return $list;
        });


        foreach ($list as $key => $item) {

            if (is_numeric($item)) {
                $list[$key] = floatval($item);
            }

            if (empty($key)) {
                unset($list[$key]);
            }
        }

        return $list;
    }

}

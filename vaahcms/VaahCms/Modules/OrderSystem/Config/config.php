<?php

return [
    "name"=> "OrderSystem",
    "title"=> "Product buying sytem",
    "slug"=> "ordersystem",
    "thumbnail"=> "https://img.site/p/300/160",
    "is_dev" => env('MODULE_ORDERSYSTEM_ENV')?true:false,
    "excerpt"=> "Na",
    "description"=> "Na",
    "download_link"=> "",
    "author_name"=> "Kartik",
    "author_website"=> "https://vaah.dev",
    "version"=> "0.0.1",
    "is_migratable"=> true,
    "is_sample_data_available"=> false,
    "db_table_prefix"=> "vh_ordersystem_",
    "providers"=> [
        "\\VaahCms\\Modules\\OrderSystem\\Providers\\OrderSystemServiceProvider"
    ],
    "aside-menu-order"=> null
];

<?php

return [
    "name"=> "BlogSystem",
    "title"=> "Blogging System",
    "slug"=> "blogsystem",
    "thumbnail"=> "https://img.site/p/300/160",
    "is_dev" => env('MODULE_BLOGSYSTEM_ENV')?true:false,
    "excerpt"=> "Blog display Site",
    "description"=> "Blog display Site",
    "download_link"=> "",
    "author_name"=> "Kartik",
    "author_website"=> "https://vaah.dev",
    "version"=> "0.0.1",
    "is_migratable"=> true,
    "is_sample_data_available"=> false,
    "db_table_prefix"=> "vh_blogsystem_",
    "providers"=> [
        "\\VaahCms\\Modules\\BlogSystem\\Providers\\BlogSystemServiceProvider"
    ],
    "aside-menu-order"=> null
];

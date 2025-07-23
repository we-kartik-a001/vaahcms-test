<?php  namespace VaahCms\Modules\OrderSystem\Http\Controllers\Backend;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use VaahCms\Modules\OrderSystem\Models\customer;
use VaahCms\Modules\OrderSystem\Models\order;
use VaahCms\Modules\OrderSystem\Models\Product;

class ExtendController extends Controller
{

    //----------------------------------------------------------
    public function __construct()
    {
    }
    //----------------------------------------------------------
    public static function topLeftMenu()
    {
        $links = [];

        $response['success'] = true;
        $response['data'] = $links;

        return vh_response($response);

    }
    //----------------------------------------------------------
    public static function topRightUserMenu()
    {
        $links = [];

        $response['success'] = true;
        $response['data'] = $links;

        return vh_response($response);
    }
    //----------------------------------------------------------
    public static function sidebarMenu()
    {
        $links = [];


        $links[0] = [
            'icon' => 'table',
            'label'=> 'OrderSystem',
            'link'=> route('vh.backend.ordersystem')
        ];


        if(version_compare(config('vaahcms.version'), '2.0.0', '<' )){
            $links[0]['link'] = route('vh.backend.ordersystem');
        } else{
            $links[0]['url'] = route('vh.backend.ordersystem');
        }


        $response['success'] = true;
        $response['data'] = $links;

        return vh_response($response);
    }
    //----------------------------------------------------------
    

   public function getDashboardItems()
    {

        $data = array();

        $data['card'] = [
            "title" => "Order System Details",
            "list" => [

                 [
                    "count" => order::count(),
                    "label" => 'Total Orders',
                    "icon" => "pi pi-box",
                    "type" => "success",
                   
                ],

                [
                    "count" => Product::count(),
                    "label" => 'Total Products',
                    "icon" => "pi-shopping-bag",
                    "type" => "success",
                ],
                [
                    "count" => customer::where('is_active',1)->count(),
                    "label" => 'Active Cusomter',
                    "icon" => "pi-user",
                    "type" => "success",
                ]
            ],
        ];

        $response['success'] = true;
        $response['data'] = $data;
        return $response;
    }


}

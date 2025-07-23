<?php namespace VaahCms\Modules\OrderSystem\Models;

use App\Jobs\OrderSendMail;
use Customers;
use DateTimeInterface;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Faker\Factory;
use VaahCms\Modules\OrderSystem\Mails\orderstatusMail;
use VaahCms\Modules\OrderSystem\Mails\OrderupdateMail;
use VaahCms\Modules\OrderSystem\Mails\TrashMail;
use VaahCms\Modules\OrderSystem\Traits\TrashEmail;
use WebReinvent\VaahCms\Libraries\VaahMail;
use WebReinvent\VaahCms\Models\VaahModel;
use WebReinvent\VaahCms\Traits\CrudWithUuidObservantTrait;
use WebReinvent\VaahCms\Models\User;
use WebReinvent\VaahCms\Libraries\VaahSeeder;
use WebReinvent\VaahCms\Models\Taxonomy;

class order extends VaahModel
{

    use SoftDeletes;
    use CrudWithUuidObservantTrait;
    use TrashEmail;

    //-------------------------------------------------
    protected $table = 'orders';
    //-------------------------------------------------
    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at'
    ];
    //-------------------------------------------------
    protected $fillable = [
        'uuid',
        'name',
        'customer_id',
        'total_price',
        'total_quantity',
        'status_id',
        'slug',
        'is_active',
        'created_by',
        'updated_by',
        'deleted_by',
    ];
    //-------------------------------------------------
    protected $fill_except = [

    ];

    //-------------------------------------------------
    protected $appends = [
    ];


    //-------------------------------------------------
    // Relation
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function products()
    {
        return $this->belongsToMany(Product::class, 'order_product', 'order_id', 'product_id')
            ->withPivot('quantity')
            ->withTimestamps();
    }
    
    public function status()
    {
        return $this->belongsTo(Taxonomy::class, 'status_id',);
    }

    //-------------------------------------------------
    protected function serializeDate(DateTimeInterface $date)
    {
        $date_time_format = config('settings.global.datetime_format');
        return $date->format($date_time_format);
    }

    //-------------------------------------------------
    public static function getUnFillableColumns()
    {
        return [
            'uuid',
            'created_by',
            'updated_by',
            'deleted_by',
        ];
    }
    //-------------------------------------------------
    public static function getFillableColumns()
    {
        $model = new self();
        $except = $model->fill_except;
        $fillable_columns = $model->getFillable();
        $fillable_columns = array_diff(
            $fillable_columns, $except
        );
        return $fillable_columns;
    }
    //-------------------------------------------------
    public static function getEmptyItem()
    {
        $model = new self();
        $fillable = $model->getFillable();
        $empty_item = [];
        foreach ($fillable as $column)
        {
            $empty_item[$column] = null;
        }
        $empty_item['is_active'] = 1;

        return $empty_item;
    }

    //-------------------------------------------------

    public function createdByUser()
    {
        return $this->belongsTo(User::class,
            'created_by', 'id'
        )->select('id', 'uuid', 'first_name', 'last_name', 'email');
    }

    //-------------------------------------------------
    public function updatedByUser()
    {
        return $this->belongsTo(User::class,
            'updated_by', 'id'
        )->select('id', 'uuid', 'first_name', 'last_name', 'email');
    }

    //-------------------------------------------------
    public function deletedByUser()
    {
        return $this->belongsTo(User::class,
            'deleted_by', 'id'
        )->select('id', 'uuid', 'first_name', 'last_name', 'email');
    }

    //-------------------------------------------------
    public function getTableColumns()
    {
        return $this->getConnection()->getSchemaBuilder()
            ->getColumnListing($this->getTable());
    }

    //-------------------------------------------------
    public function scopeExclude($query, $columns)
    {
        return $query->select(array_diff($this->getTableColumns(), $columns));
    }

    //-------------------------------------------------

    //create order mail

    public static function orderMail($item)
    {
        $subject = 'Order Confirmation';

        // Ensure related models are loaded
        $item->load('customer', 'products', 'status');

        $customer = $item->customer;
        $products = $item->products;
        $statusName = $item->status ? $item->status->name : 'Unknown';

        // Build product table rows
        $productRows = '';
        foreach ($products as $product) {
            $productRows .= sprintf(
                '<tr>
                    <td style="padding:8px; border:1px solid #ddd;">%s</td>
                    <td style="padding:8px; border:1px solid #ddd;">%d</td>
                    <td style="padding:8px; border:1px solid #ddd;">₹%s</td>
                </tr>',
                htmlspecialchars($product->name),
                $product->pivot->quantity,
                number_format($product->price, 2)
            );
        }

        // Email body HTML
        $emailContent = sprintf(
            '<body style="background-color:#f9fafb; font-family:Arial, sans-serif; padding:2rem;">
                <table style="width:100%%; max-width:600px; margin:0 auto; background-color:#ffffff; border-radius:8px; padding:2rem; box-shadow:0 0 10px rgba(0,0,0,0.05);">
                    <tr>
                        <td style="padding-bottom:1rem;">
                            <h1 style="font-size:1.5rem; font-weight:600; color:#3b82f6; margin:0;">Order Confirmation</h1>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding-bottom:1rem;">Hi %s,</td>
                    </tr>
                    <tr>
                        <td style="padding-bottom:1rem;">Your order has been successfully placed. Below are your order details:</td>
                    </tr>
                    <tr><td><strong>Order Slug:</strong> %s</td></tr>
                    <tr><td><strong>Total Quantity:</strong> %d</td></tr>
                    <tr><td><strong>Total Price:</strong> ₹%s</td></tr>
                    <tr><td><strong>Status:</strong> %s</td></tr>
                    <tr><td><strong>Active:</strong> %s</td></tr>
                </table> 
            </body>',
            htmlspecialchars($customer->name),
            htmlspecialchars($item->slug),
            $item->total_quantity,
            number_format($item->total_price, 2),
            htmlspecialchars($statusName),
            $item->is_active ? 'Yes' : 'No',
            $productRows
        );

        // Send the email
        VaahMail::dispatchGenericMail($subject, $emailContent, [
            ['email' => $customer->email, 'name' => $customer->name]
        ]);
    }

    //-------------------------------------------------

    //update in order
    
    public static function sendOrderUpdateMail($item, $changes)
    {
        if (empty($changes)) {
            return;
        }

        $subject = 'Order Update Notification';

        $item->load('customer');
        $customer = $item->customer;

        $allowedFields = [
            'name', 'slug', 'status_id', 'total_price', 'total_quantity', 'is_active'
        ];

        $changeDetails = '';
        $statusChanged = false;

        foreach ($changes as $field => $vals) {
            if (!in_array($field, $allowedFields)) {
                continue;
            }

            $oldVal = $vals['old'];
            $newVal = $vals['new'];

            if ($field === 'status_id') {
                $oldStatus = Taxonomy::find($oldVal);
                $newStatus = Taxonomy::find($newVal);

                $oldVal = $oldStatus ? $oldStatus->name : 'Unknown';
                $newVal = $newStatus ? $newStatus->name : 'Unknown';

                $field = 'Status';
                $statusChanged = true;
            }

            if (is_array($oldVal) || is_object($oldVal)) {
                $oldVal = json_encode($oldVal);
            }

            if (is_array($newVal) || is_object($newVal)) {
                $newVal = json_encode($newVal);
            }

            $changeDetails .= sprintf(
                '<tr>
                    <td style="padding:8px; border:1px solid #ddd;">%s</td>
                    <td style="padding:8px; border:1px solid #ddd;">%s</td>
                    <td style="padding:8px; border:1px solid #ddd;">%s</td>
                </tr>',
                ucfirst(str_replace('_', ' ', $field)),
                htmlspecialchars($oldVal),
                htmlspecialchars($newVal)
            );
        }

        if (empty($changeDetails)) {
            return;
        }

        // Extra message if status changed
        $extraInfo = '';
        if ($statusChanged) {
            $extraInfo = sprintf(
                '<p style="margin:1rem 0; font-weight:bold;">Order ID: %s<br>Customer: %s</p>',
                htmlspecialchars($item->id),
                htmlspecialchars($customer->name)
            );
        }

        $emailContent = sprintf(
            '<body style="background-color:#f9fafb; font-family:Arial, sans-serif; padding:2rem;">
                <table style="width:100%%; max-width:600px; margin:0 auto; background-color:#ffffff; border-radius:8px; padding:2rem; box-shadow:0 0 10px rgba(0,0,0,0.05);">
                    <tr><td style="padding-bottom:1rem;">
                        <h1 style="font-size:1.5rem; font-weight:600; color:#3b82f6; margin:0;">Order Update</h1>
                    </td></tr>
                    <tr><td style="padding-bottom:1rem;">Hi %s,</td></tr>
                    <tr><td style="padding-bottom:1rem;">The following changes have been made to your order <strong>%s</strong>:</td></tr>
                    <tr><td>%s</td></tr>
                    <tr><td>
                        <table style="width:100%%; border-collapse:collapse; margin-top:1rem;">
                            <thead>
                                <tr style="background-color:#f1f5f9;">
                                    <th style="padding:8px; border:1px solid #ddd;">Field</th>
                                    <th style="padding:8px; border:1px solid #ddd;">Old Value</th>
                                    <th style="padding:8px; border:1px solid #ddd;">New Value</th>
                                </tr>
                            </thead>
                            <tbody>
                                %s
                            </tbody>
                        </table>
                    </td></tr>
                    <tr><td style="padding-top:1rem;">Thank you,<br>Support Team</td></tr>
                </table>
            </body>',
            htmlspecialchars($customer->name),
            htmlspecialchars($item->slug ?? $item->name ?? 'Order #' . $item->id),
            $extraInfo,
            $changeDetails
        );

        VaahMail::dispatchGenericMail($subject, $emailContent, [
            ['email' => $customer->email, 'name' => $customer->name]
        ]);
    }

    //-------------------------------------------------
    public function scopeBetweenDates($query, $from, $to)
    {

        if ($from) {
            $from = \Carbon::parse($from)
                ->startOfDay()
                ->toDateTimeString();
        }

        if ($to) {
            $to = \Carbon::parse($to)
                ->endOfDay()
                ->toDateTimeString();
        }

        $query->whereBetween('updated_at', [$from, $to]);
    }

    //-------------------------------------------------
    public static function createItem($request)
    {
        $inputs = $request->all();

        // Step 1: Validate input
        $validation = self::validation($inputs);
        if (!$validation['success']) {
            return $validation;
        }

        // Step 2: Check if name exists
        $item = self::where('name', $inputs['name'])->withTrashed()->first();
        if ($item) {
            return [
                'success' => false,
                'errors' => ["This name is already exist" . ($item->deleted_at ? ' in trash.' : '.')],
            ];
        }

        // Step 3: Check if slug exists
        $item = self::where('slug', $inputs['slug'])->withTrashed()->first();
        if ($item) {
            return [
                'success' => false,
                'errors' => ["This slug is already exist" . ($item->deleted_at ? ' in trash.' : '.')],
            ];
        }

        // Step 4: Separate product data
        $products = isset($inputs['products']) ? $inputs['products'] : [];
        unset($inputs['products']);

        // Step 5: Validate stock BEFORE saving order
        foreach ($products as $prod) {
            $product = Product::find($prod['id']);
            $ordered_qty = $prod['quantity'];

            if (!$product) {
                return [
                    'success' => false,
                    'errors' => ["Product ID {$prod['id']} not found."],
                ];
            }

            if ($product->stock < $ordered_qty) {
                return [
                    'success' => false,
                    'errors' => ["Not enough stock for '{$product->name}'. Available: {$product->stock}, Requested: {$ordered_qty}"],
                ];
            }
        }

        // Step 6: Now safely create order
        $item = new self();
        $item->fill($inputs);
        $item->save();

        $item->load('customer');

        if ($item) {
            
            VaahMail::addInQueue(new orderstatusMail($item), $item->customer->email);
        }


        // Step 8: Deduct stock and attach pivot
        foreach ($products as $prod) {
            $product = Product::find($prod['id']);
            $product->stock -= $prod['quantity'];
            $product->save();
        }

        $pivot_data = collect($products)->mapWithKeys(function ($prod) {
            return [$prod['id'] => ['quantity' => $prod['quantity']]];
        })->toArray();

        $item->products()->attach($pivot_data);

        // Step 9: Return response
        $response = self::getItem($item->id);
        $response['messages'][] = trans("vaahcms-general.saved_successfully");

        return $response;
    }

    public function scopeFilterByPrice($query, $filter)
    {
        // If both are missing, skip filtering
        if (!isset($filter['price_min']) && !isset($filter['price_max'])) {
            return $query;
        }

        // Set defaults if only one bound is provided
        $min = isset($filter['price_min']) ? (float) $filter['price_min'] : null;
        $max = isset($filter['price_max']) ? (float) $filter['price_max']: null;

        // dd($min,$max);
        
        // Apply range condition
        return $query->whereBetween('total_price', [$min, $max]);
    }


    //-------------------------------------------------
    public function scopeFilterByStatus($query, $filter)
    {
        if (!isset($filter['status_id'])) {
            return $query;
        }

        $status_id = $filter['status_id'];

        if ($status_id) {
            return $query->where('status_id', $status_id);
        }

    }

    //-------------------------------------------------
    public function scopeGetSorted($query, $filter)
    {

        if(!isset($filter['sort']))
        {
            return $query->orderBy('id', 'desc');
        }

        $sort = $filter['sort'];


        $direction = Str::contains($sort, ':');

        if(!$direction)
        {
            return $query->orderBy($sort, 'asc');
        }

        $sort = explode(':', $sort);

        return $query->orderBy($sort[0], $sort[1]);
    }
    //-------------------------------------------------
    public function scopeIsActiveFilter($query, $filter)
    {

        if(!isset($filter['is_active'])
            || is_null($filter['is_active'])
            || $filter['is_active'] === 'null'
        )
        {
            return $query;
        }
        $is_active = $filter['is_active'];

        if($is_active === 'true' || $is_active === true)
        {
            return $query->where('is_active', 1);
        } else{
            return $query->where(function ($q){
                $q->whereNull('is_active')
                    ->orWhere('is_active', 0);
            });
        }

    }
    //-------------------------------------------------
    public function scopeTrashedFilter($query, $filter)
    {

        if(!isset($filter['trashed']))
        {
            return $query;
        }
        $trashed = $filter['trashed'];

        if($trashed === 'include')
        {
            return $query->withTrashed();
        } else if($trashed === 'only'){
            return $query->onlyTrashed();
        }

    }
    //-------------------------------------------------
    public function scopeSearchFilter($query, $filter)
    {

        if(!isset($filter['q']))
        {
            return $query;
        }
        $search_array = explode(' ',$filter['q']);
        foreach ($search_array as $search_item){
            $query->where(function ($q1) use ($search_item) {
                $q1->where('name', 'LIKE', '%' . $search_item . '%')
                    ->orWhere('total_price', 'LIKE', '%' . $search_item . '%')
                    ->orWhere('total_quantity', 'LIKE', '%' . $search_item . '%')
                    ->orWhere('slug', 'LIKE', '%' . $search_item . '%')
                    ->orWhere('id', 'LIKE', $search_item . '%')
                     ->orWhereHas('customer', function ($q2) use ($search_item) {
                        $q2->where('name', 'LIKE', '%' . $search_item . '%');
                    })
                    ->orWhereHas('status', function ($q3) use ($search_item) {
                        $q3->where('name', 'LIKE', '%' . $search_item . '%');
                    });
                   
            });
        }

    }
    
    //-------------------------------------------------
    public static function getList($request)
    {
        $list = self::getSorted($request->filter);
        $list->isActiveFilter($request->filter);
        $list->trashedFilter($request->filter);
        $list->searchFilter($request->filter);
        $list->filterByStatus($request->filter);
         // Filter by customer_id
        if (isset($request->filter['customer_id'])) {
            $list->where('customer_id', $request->filter['customer_id']);
        }
        $list->filterByPrice($request->filter);
        $list->with(['createdByUser', 'updatedByUser', 'deletedByUser','customer','status']); 

        $rows = config('vaahcms.per_page');

        if($request->has('rows'))
        {
            $rows = $request->rows;
        }

        $list = $list->paginate($rows);

        $response['success'] = true;
        $response['data'] = $list;

        return $response;


    }

    //-------------------------------------------------
    public static function updateList($request)
    {

        $inputs = $request->all();

        $rules = array(
            'type' => 'required',
        );

        $messages = array(
            'type.required' => trans("vaahcms-general.action_type_is_required"),
        );


        $validator = \Validator::make($inputs, $rules, $messages);
        if ($validator->fails()) {

            $errors = errorsToArray($validator->errors());
            $response['success'] = false;
            $response['errors'] = $errors;
            return $response;
        }

        if(isset($inputs['items']))
        {
            $items_id = collect($inputs['items'])
                ->pluck('id')
                ->toArray();
        }

        $items = self::whereIn('id', $items_id);

        switch ($inputs['type']) {
            case 'deactivate':
                $items->withTrashed()->where(['is_active' => 1])
                    ->update(['is_active' => null]);
                break;
            case 'activate':
                $items->withTrashed()->where(function ($q){
                    $q->where('is_active', 0)->orWhereNull('is_active');
                })->update(['is_active' => 1]);
                break;
            case 'trash':
                self::whereIn('id', $items_id)
                    ->get()->each->delete();
                break;
            case 'restore':
                self::whereIn('id', $items_id)->onlyTrashed()
                    ->get()->each->restore();
                break;
        }

        $response['success'] = true;
        $response['data'] = true;
        $response['messages'][] = trans("vaahcms-general.action_successful");

        return $response;
    }

    //-------------------------------------------------
    public static function deleteList($request): array
    {
        $inputs = $request->all();

        $rules = array(
            'type' => 'required',
            'items' => 'required',
        );

        $messages = array(
            'type.required' => trans("vaahcms-general.action_type_is_required"),
            'items.required' => trans("vaahcms-general.select_items"),
        );

        $validator = \Validator::make($inputs, $rules, $messages);
        if ($validator->fails()) {

            $errors = errorsToArray($validator->errors());
            $response['success'] = false;
            $response['errors'] = $errors;
            return $response;
        }

        $items_id = collect($inputs['items'])->pluck('id')->toArray();
        self::whereIn('id', $items_id)->forceDelete();

        $response['success'] = true;
        $response['data'] = true;
        $response['messages'][] = trans("vaahcms-general.action_successful");

        return $response;
    }
    //-------------------------------------------------
    public static function listAction($request, $type): array
    {

        $list = self::query();

        if($request->has('filter')){
            $list->getSorted($request->filter);
            $list->isActiveFilter($request->filter);
            $list->filterByStatus($request->filter);
            $list->trashedFilter($request->filter);
            $list->searchFilter($request->filter);
        }

        switch ($type) {
            case 'activate-all':
                $list->withTrashed()->where(function ($q){
                    $q->where('is_active', 0)->orWhereNull('is_active');
                })->update(['is_active' => 1]);
                break;
            case 'deactivate-all':
                $list->withTrashed()->where(['is_active' => 1])
                    ->update(['is_active' => null]);
                break;
            case 'trash-all':
                $items = $list->get();
                $items->each->delete();
                if ($items->count() > 0) {
                    self::sendDeleteMail($items);
                }
                break;
            case 'restore-all':
                $list->onlyTrashed()->get()
                    ->each->restore();
                break;
            case 'delete-all':
                 $list->each(function ($order) {
                    $order->products()->detach();
                });
                $list->forceDelete();
                break;
            case 'create-100-records':
            case 'create-1000-records':
            case 'create-5000-records':
            case 'create-10000-records':

            if(!config('ordersystem.is_dev')){
                $response['success'] = false;
                $response['errors'][] = 'User is not in the development environment.';

                return $response;
            }

            preg_match('/-(.*?)-/', $type, $matches);

            if(count($matches) !== 2){
                break;
            }

            self::seedSampleItems($matches[1]);
            break;
        }

        $response['success'] = true;
        $response['data'] = true;
        $response['messages'][] = trans("vaahcms-general.action_successful");

        return $response;
    }
    //-------------------------------------------------
    public static function getItem($id)
    {

        $item = self::where('id', $id)
            ->with(['createdByUser', 'updatedByUser', 'deletedByUser','customer','status', 'products' => function($q) {
                $q->select('products.id', 'products.name', 'products.price');
            }])
            ->withTrashed()
            ->first();

        if(!$item)
        {
            $response['success'] = false;
            $response['errors'][] = 'Record not found with ID: '.$id;
            return $response;
        }

        
         // Add pivot quantity to each product object for Vue use
        if ($item->products) {
            $item->products->transform(function ($product) {
                $product->quantity = $product->pivot->quantity ?? 0;
                return $product;
            });
        }

        $response['success'] = true;
        $response['data'] = $item;

        return $response;

    }
    //-------------------------------------------------

    public static function updateItem($request, $id)
    {
        $inputs = $request->all();

        $validation = self::validation($inputs);
        if (!$validation['success']) {
            return $validation;
        }

        // Check if name exists
        $item = self::where('id', '!=', $id)
            ->withTrashed()
            ->where('name', $inputs['name'])->first();

        if ($item) {
            $error_message = "This name is already exist" . ($item->deleted_at ? ' in trash.' : '.');
            return [
                'success' => false,
                'errors' => [$error_message],
            ];
        }

        // Check if slug exists
        $item = self::where('id', '!=', $id)
            ->withTrashed()
            ->where('slug', $inputs['slug'])->first();

        if ($item) {
            $error_message = "This slug is already exist" . ($item->deleted_at ? ' in trash.' : '.');
            return [
                'success' => false,
                'errors' => [$error_message],
            ];
        }

        // Load existing order
        $item = self::where('id', $id)
            ->with(['status', 'customer', 'products'])
            ->withTrashed()
            ->first();

        $original = $item->getOriginal();

        // Capture product input before unset
        $products = isset($inputs['products']) ? $inputs['products'] : [];
        unset($inputs['products']);

        // === STEP 1: Validate Stock Availability Before Making Any Changes ===
        foreach ($products as $prod) {
            $product = Product::find($prod['id']);
            $requested_qty = $prod['quantity'];

            if (!$product) {
                return [
                    'success' => false,
                    'errors' => ["Product ID {$prod['id']} not found."],
                ];
            }

            $existing_pivot = $item->products->firstWhere('id', $prod['id']);
            $restored_stock = $product->stock + ($existing_pivot?->pivot->quantity ?? 0);

            if ($restored_stock < $requested_qty) {
                return [
                    'success' => false,
                    'errors' => [
                        "Not enough stock for '{$product->name}'. Available after restore: {$restored_stock}, Requested: {$requested_qty}"
                    ],
                ];
            }
        }

        // === STEP 2: Restore Old Stock ===
        foreach ($item->products as $old_product) {
            $product = Product::find($old_product->id);
            if ($product) {
                $product->stock += $old_product->pivot->quantity;
                $product->save();
            }
        }

        // === STEP 3: Deduct New Stock ===
        foreach ($products as $prod) {
            $product = Product::find($prod['id']);
            $product->stock -= $prod['quantity'];
            $product->save();
        }

        // === STEP 4: Detect Changes in Non-Product Fields ===
        $changes = [];
        foreach ($inputs as $key => $new_value) {
            $old_value = $original[$key] ?? null;

            if (is_numeric($old_value) && is_numeric($new_value)) {
                $old_value = (string)$old_value;
                $new_value = (string)$new_value;
            }

            if ($old_value !== $new_value) {
                $changes[$key] = [
                    'old' => $old_value,
                    'new' => $new_value
                ];
            }
        }

        // === STEP 5: Update Order Fields ===
        $item->fill($inputs);
        $item->save();

        // === STEP 6: Sync Products with New Quantities ===
        $pivot_data = collect($products)->mapWithKeys(function ($prod) {
            return [$prod['id'] => ['quantity' => $prod['quantity']]];
        })->toArray();

        $item->products()->sync($pivot_data);

        // === STEP 7: Notify if Changes Occurred ===
       if (!empty($changes)) {
            VaahMail::addInQueue(
                new OrderupdateMail($item, $changes),
                $item->customer->email
            );
        }


        // === STEP 8: Return Updated Item ===
        $response = self::getItem($item->id);
        $response['messages'][] = trans("vaahcms-general.saved_successfully");

        return $response;
    }


    //-------------------------------------------------
    public static function deleteItem($request, $id): array
    {
        $item = self::where('id', $id)->withTrashed()->first();
        if (!$item) {
            $response['success'] = false;
            $response['errors'][] = trans("vaahcms-general.record_does_not_exist");
            return $response;
        }
        $item->products()->detach(); 
    
        $item->forceDelete();

        $response['success'] = true;
        $response['data'] = [];
        $response['messages'][] = trans("vaahcms-general.record_has_been_deleted");

        return $response;
    }
    //-------------------------------------------------
    public static function itemAction($request, $id, $type): array
    {
        switch($type)
        {
            case 'activate':
                self::where('id', $id)
                    ->withTrashed()
                    ->update(['is_active' => 1]);
                break;
            case 'deactivate':
                self::where('id', $id)
                    ->withTrashed()
                    ->update(['is_active' => null]);
                break;
           case 'trash':
                $item = self::find($id);

                if ($item) {
                    $item->delete();

                     $super_admin = User::first();

                    VaahMail::addInQueue(
                        new TrashMail($item, $super_admin),
                        'test@gmail.com'
                    );
                }
                break;
            case 'restore':
                self::where('id', $id)
                    ->onlyTrashed()
                    ->first()->restore();
                break;
        }

        return self::getItem($id);
    }
    //-------------------------------------------------

    public static function validation($inputs)
    {

        $rules = array(
            'name' => 'required|max:150',
            'slug' => 'required|max:150',
            'customer_id'=> 'required|integer|exists:customers,id', 
            'total_price'=> 'required|numeric|min:0',
            'total_quantity'=> 'required|integer|min:1',
            'status_id'=> 'required|integer|exists:vh_taxonomies,id',
            'products'=> 'required|array|min:1',
            'products.*.id' => 'required|integer|exists:products,id',
            'products.*.quantity' => 'required|integer|min:1',
        );

        $validator = \Validator::make($inputs, $rules);
        if ($validator->fails()) {
            $messages = $validator->errors();
            $response['success'] = false;
            $response['errors'] = $messages->all();
            return $response;
        }

        $response['success'] = true;
        return $response;

    }

    //-------------------------------------------------
    public static function getActiveItems()
    {
        $item = self::where('is_active', 1)
            ->withTrashed()
            ->first();
        return $item;
    }

    //-------------------------------------------------
    //-------------------------------------------------

    public static function seedSampleItems($records = 100)
    {
        $i = 0;

        while ($i < $records) {
            $inputs = self::fillItem(false);

            // Skip if no valid product data found
            if (empty($inputs['products'])) {
                continue;
            }

            // Create the order
            $item = new self();
            $item->fill($inputs);
            $item->save();

            $pivot_data = [];
            foreach ($inputs['products'] as $prod) {
                $product = Product::find($prod['id']);
                $ordered_qty = $prod['quantity'];

                if (!$product || $product->stock < $ordered_qty) {
                    continue; // skip if product not valid or not enough stock
                }

                // Deduct stock
                $product->stock -= $ordered_qty;
                $product->save();

                $pivot_data[$prod['id']] = ['quantity' => $ordered_qty];
            }

            // Attach products with pivot data
            if (!empty($pivot_data)) {
                $item->products()->attach($pivot_data);
                $i++;
            } else {
                // If no products were attached (e.g., due to low stock), delete the order
                $item->delete();
            }
        }
    }

    //------------------------------------------------

    public static function getOrderStatuses()
    {
        $statuses = Taxonomy::getTaxonomyByType('order-status');
        return $statuses->pluck('id')->toArray();
    }

    //------------------------------------------------
   public static function fillItem($is_response_return = true)
    {
        $request = new Request([
            'model_namespace' => self::class,
            'except' => self::getUnFillableColumns()
        ]);

        $fillable = VaahSeeder::fill($request);

        if (!$fillable['success']) {
            return $fillable;
        }

        $inputs = $fillable['data']['fill'];
        $faker = Factory::create();

        // Add fake name and slug
        $inputs['name'] = $faker->words(2, true);
        $inputs['slug'] = Str::slug($inputs['name']);
        $inputs['is_active'] = 1;

        // Add random customer ID
        $active_customer_count = rand(1, Customer::where('is_active', 1)->count());
        $inputs['customer_id'] = Customer::where('is_active',1)->inRandomOrder()->take($active_customer_count)->value('id');

        // Add status_id from order_status taxonomy
        $status_ids = self::getOrderStatuses();
        $inputs['status_id'] = $faker->randomElement($status_ids);

        // Add random products with quantities, and include name and price
        $active_products_count = rand(1, Product::where('is_active', 1)->count());
        $products = Product::where('is_active',1)->inRandomOrder()->take($active_products_count)->get();
        $pivot_products = [];
        $total_quantity = 0;
        $total_price = 0;

        foreach ($products as $product) {
            $quantity = rand(1, 5);
            $total_quantity += $quantity;
            $total_price += $product->price * $quantity;

            $pivot_products[] = [
                'id' => $product->id,
                'name' => $product->name,
                'price' => $product->price,
                'quantity' => $quantity,
            ];
        }

        $inputs['products'] = $pivot_products;
        $inputs['total_quantity'] = $total_quantity;
        $inputs['total_price'] = $total_price;

        if (!$is_response_return) {
            return $inputs;
        }

        $response['success'] = true;
            $response['data']['fill'] = $inputs;
            return $response;
    }

    protected static function booted()
    {
        static::created(function ($order) {
            $customer = $order->customer;
            $customer->total_orders = $customer->orders()->count();
            $customer->save();
        });
    }

    //-------------------------------------------------
    //-------------------------------------------------
    //-------------------------------------------------


}

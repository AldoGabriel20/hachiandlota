<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Coupon;
use App\Models\Slide;
use App\Models\Contact;
use App\Models\Setting;
use App\Models\Order;
use App\Models\User;
use App\Models\Transaction;
use Illuminate\Support\Str;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Intervention\Image\Laravel\Facades\Image;

class AdminController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        $orders = Order::orderBy("created_at", "DESC")->get()->take(10);
        $dashboardDatas = DB::select("Select sum(total) As TotalAmount,
                                sum(if(status='ordered',total,0)) As TotalOrderedAmount,
                                sum(if(status='delivered',total,0)) As TotalDeliveredAmount,
                                sum(if(status='cancelled',total,0)) As TotalCancelledAmount,
                                Count(*) As Total,
                                sum(if(status='ordered',1,0)) As TotalOrdered,
                                sum(if(status='delivered',1,0)) As TotalDelivered,
                                sum(if(status='cancelled',1,0)) As TotalCancelled 
                                From Orders
                                ");

        $monthlyDatas = DB::select("SELECT M.id As MonthNo, M.name As MonthName,
                                IFNULL (D.TotalAmount,0) As TotalAmount,
                                IFNULL (D.TotalOrderedAmount,0) As TotalOrderedAmount,
                                IFNULL (D.TotalDeliveredAmount,0) As TotalDeliveredAmount,
                                IFNULL (D.TotalCancelledAmount,0) As TotalCancelledAmount FROM month_names M
                                LEFT JOIN (Select DATE_FORMAT(created_at, '%b') As MonthName,
                                MONTH(created_at) As MonthNo,
                                sum(total) As TotalAmount,
                                sum(if(status='ordered',total,0)) As TotalOrderedAmount,
                                sum(if(status='delivered',total,0)) As TotalDeliveredAmount,
                                sum(if(status='cancelled',total,0)) As TotalCancelledAmount
                                FROM Orders WHERE YEAR(created_at)=YEAR(NOW()) GROUP BY YEAR(created_at), MONTH(created_at), DATE_FORMAT(created_at, '%b')
                                ORDER BY MONTH(created_at)) D ON D.MonthNo=M.id");

        $AmountM = implode(',', collect($monthlyDatas)->pluck('TotalAmount')->toArray());
        $OrderedAmountM = implode(',', collect($monthlyDatas)->pluck('TotalOrderedAmount')->toArray());
        $DeliveredAmountM = implode(',', collect($monthlyDatas)->pluck('TotalDeliveredAmount')->toArray());
        $CancelledAmountM = implode(',', collect($monthlyDatas)->pluck('TotalCancelledAmount')->toArray());

        $TotalAmount = collect($monthlyDatas)->sum('TotalAmount');
        $TotalOrderedAmount = collect($monthlyDatas)->sum('TotalOrderedAmount');
        $TotalDeliveredAmount = collect($monthlyDatas)->sum('TotalDeliveredAmount');
        $TotalCancelledAmount = collect($monthlyDatas)->sum('TotalCancelledAmount');

        return view('admin.index', compact('user', 'orders', 'dashboardDatas', 'AmountM', 'OrderedAmountM', 'DeliveredAmountM', 'CancelledAmountM', 'TotalAmount', 'TotalOrderedAmount', 'TotalDeliveredAmount', 'TotalCancelledAmount'));
    }

    public function categories()
    {
        $user = Auth::user();
        $categories = Category::orderBy('id', 'DESC')->paginate(10);
        return view('admin.categories', compact('categories', 'user'));
    }

    public function category_add()
    {
        $user = Auth::user();
        return view('admin.category-add', compact('user'));
    }

    public function category_store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'slug' => 'required|unique:categories,slug',
            'image' => 'mimes:png,jpg,jpeg|max:2048'
        ]);

        $category = new Category();
        $category->name = $request->name;
        $category->slug = Str::slug($request->name);
        $image = $request->file('image');
        $file_extention = $request->file('image')->extension();
        $file_name = Carbon::now()->timestamp . '.' . $file_extention;
        $this->GenerateCategoryThumbailsImage($image, $file_name);
        $category->image = $file_name;
        $category->save();
        return redirect()->route('admin.categories')->with('status', 'Category has been added successfully!');
    }

    public function GenerateCategoryThumbailsImage($image, $imageName)
    {
        $destinationPath = public_path('uploads/categories');
        $img = Image::read($image->path());
        $img->cover(124, 124, "top");
        $img->resize(124, 124, function ($constraint) {
            $constraint->aspectRatio();
        })->save($destinationPath . '/' . $imageName);
    }

    public function category_edit($id)
    {
        $user = Auth::user();
        $category = Category::find($id);
        return view('admin.category-edit', compact('category', 'user'));
    }

    public function category_update(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'slug' => 'required|unique:categories,slug,' . $request->id,
            'image' => 'mimes:png,jpg,jpeg|max:2048'
        ]);

        $category = Category::find($request->id);
        $category->name = $request->name;
        $category->slug = Str::slug($request->name);
        if ($request->hasFile('image')) {
            if (File::exists(public_path('uploads/categories') . '/' . $category->image)) {
                File::delete(public_path('uploads/categories') . '/' . $category->image);
            }
            $image = $request->file('image');
            $file_extension = $request->file('image')->extension();
            $file_name = Carbon::now()->timestamp . '.' . $file_extension;
            $this->GenerateCategoryThumbailsImage($image, $file_name);
            $category->image = $file_name;
        }

        $category->save();
        return redirect()->route('admin.categories')->with('status', 'Category has been updated successfully!');
    }

    public function category_delete($id)
    {
        $category = Category::find($id);
        if (File::exists(public_path('uploads/categories') . '/' . $category->image)) {
            File::delete(public_path('uploads/categories') . '/' . $category->image);
        }
        $category->delete();
        return redirect()->route('admin.categories')->with('status', 'Category has been deleted successfully!');
    }

    public function products()
    {
        $user = Auth::user();
        $products = Product::orderBy('created_at', 'DESC')->paginate(10);
        return view('admin.products', compact('products', 'user'));
    }

    public function product_add()
    {
        $user = Auth::user();
        $categories = Category::select('id', 'name')->orderBy('name')->get();
        return view('admin.product-add', compact('categories', 'user'));
    }

    public function product_store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'slug' => 'required|unique:products,slug',
            'short_description' => 'required',
            'description' => 'required',
            'regular_price' => 'required',
            'sale_price' => 'required',
            'SKU' => 'required',
            'stock_status' => 'required',
            'featured' => 'required',
            'quantity' => 'required',
            'image' => 'required|mimes:png,jpg,jpeg|max:2048',
            'category_id' => 'required'
        ]);

        $product = new Product();
        $product->name = $request->name;
        $product->slug = Str::slug($request->name);
        $product->description = $request->description;
        $product->short_description = $request->short_description;
        $product->regular_price = $request->regular_price;
        $product->sale_price = $request->sale_price;
        $product->SKU = $request->SKU;
        $product->stock_status = $request->stock_status;
        $product->featured = $request->featured;
        $product->quantity = $request->quantity;
        $product->category_id = $request->category_id;

        $current_timestamp = Carbon::now()->timestamp;
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = $current_timestamp . '_' . Str::random(10) . '.' . $image->extension();
            $this->GenerateProductThumbnailImage($image, $imageName);
            $product->image = $imageName;
        }

        $galery_arr = array();
        $counter = 1;

        if ($request->hasFile('images')) {
            $allowedFileExtensions = ['jpg', 'png', 'jpeg'];
            $files = $request->file('images');

            foreach ($files as $file) {
                $extension = $file->getClientOriginalExtension();

                if (in_array($extension, $allowedFileExtensions)) {
                    $imageName = Carbon::now()->timestamp . '_' . Str::random(10) . "_" . $counter . "." . $extension;
                    $this->GenerateProductThumbnailImage($file, $imageName);
                    array_push($galery_arr, $imageName);
                    $counter++;
                }
            }

            $galery_images = implode(',', $galery_arr);
        } else {
            $galery_images = '';
        }

        $product->images = $galery_images;
        $product->save();

        return redirect()->route('admin.products')->with("status", "Product has been added successfully!");
    }

    public function GenerateProductThumbnailImage($image, $imageName)
    {
        $destinationPath = public_path('uploads/products');
        $destinationPathThumbnail = public_path('uploads/products/thumbnails');
        $img = Image::read($image->path());

        $img->cover(540, 689, "top");
        $img->resize(540, 689, function ($constraint) {
            $constraint->aspectRatio();
        })->save($destinationPath . '/' . $imageName);

        $img->resize(104, 104, function ($constraint) {
            $constraint->aspectRatio();
        })->save($destinationPathThumbnail . '/' . $imageName);
    }

    public function product_edit($id)
    {
        $user = Auth::user();
        $product = Product::find($id);
        $categories = Category::select('id', 'name')->orderBy('name')->get();
        return view('admin.product-edit', compact('product', 'categories', 'user'));
    }

    public function product_update(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'slug' => 'required|unique:products,slug,' . $request->id,
            'short_description' => 'required',
            'description' => 'required',
            'regular_price' => 'required',
            'sale_price' => 'required',
            'SKU' => 'required',
            'stock_status' => 'required',
            'featured' => 'required',
            'quantity' => 'required',
            'image' => 'mimes:png,jpg,jpeg|max:2048',
            'category_id' => 'required'
        ]);

        $product = Product::find($request->id);
        $product->name = $request->name;
        $product->slug = Str::slug($request->name);
        $product->description = $request->description;
        $product->short_description = $request->short_description;
        $product->regular_price = $request->regular_price;
        $product->sale_price = $request->sale_price;
        $product->SKU = $request->SKU;
        $product->stock_status = $request->stock_status;
        $product->featured = $request->featured;
        $product->quantity = $request->quantity;
        $product->category_id = $request->category_id;

        $current_timestamp = Carbon::now()->timestamp;
        if ($request->hasFile('image')) {
            if (File::exists(public_path('uploads/products') . '/' . $product->image)) {
                File::delete(public_path('uploads/products') . '/' . $product->image);
            }
            if (File::exists(public_path('uploads/products/thumbnails') . '/' . $product->image)) {
                File::delete(public_path('uploads/products/thumbnails') . '/' . $product->image);
            }
            $image = $request->file('image');
            $imageName = $current_timestamp . '_' . Str::random(10) . '.' . $image->extension();
            $this->GenerateProductThumbnailImage($image, $imageName);
            $product->image = $imageName;
        }

        $galery_arr = array();
        $counter = 1;

        if ($request->hasFile('images')) {
            foreach (explode(',', $product->images) as $ofile) {
                if (File::exists(public_path('uploads/products') . '/' . $ofile)) {
                    File::delete(public_path('uploads/products') . '/' . $ofile);
                }
                if (File::exists(public_path('uploads/products/thumbnails') . '/' . $ofile)) {
                    File::delete(public_path('uploads/products/thumbnails') . '/' . $ofile);
                }
            }

            $allowedFileExtensions = ['jpg', 'png', 'jpeg'];
            $files = $request->file('images');

            foreach ($files as $file) {
                $extension = $file->getClientOriginalExtension();

                if (in_array($extension, $allowedFileExtensions)) {
                    $imageName = Carbon::now()->timestamp . '_' . Str::random(10) . "_" . $counter . "." . $extension;
                    $this->GenerateProductThumbnailImage($file, $imageName);
                    array_push($galery_arr, $imageName);
                    $counter++;
                }
            }

            $galery_images = implode(',', $galery_arr);
        } else {
            $galery_images = '';
        }

        $product->images = $galery_images;
        $product->save();
        return redirect()->route('admin.products')->with('status', 'Product has been updated successfully!');
    }

    public function product_delete($id)
    {
        $product = Product::find($id);
        if (File::exists(public_path('uploads/products') . '/' . $product->image)) {
            File::delete(public_path('uploads/products') . '/' . $product->image);
        }
        if (File::exists(public_path('uploads/products/thumbnails') . '/' . $product->image)) {
            File::delete(public_path('uploads/products/thumbnails') . '/' . $product->image);
        }

        foreach (explode(',', $product->images) as $ofile) {
            if (File::exists(public_path('uploads/products') . '/' . $ofile)) {
                File::delete(public_path('uploads/products') . '/' . $ofile);
            }
            if (File::exists(public_path('uploads/products/thumbnails') . '/' . $ofile)) {
                File::delete(public_path('uploads/products/thumbnails') . '/' . $ofile);
            }
        }

        $product->delete();
        return redirect()->route('admin.products')->with('status', 'Product has been deleted successfully!');
    }

    public function coupons()
    {
        $user = Auth::user();
        $coupons = Coupon::orderBy('expiry_date', 'DESC')->paginate(12);
        return view('admin.coupons', compact('coupons', 'user'));
    }

    public function coupon_add()
    {
        $user = Auth::user();
        return view('admin.coupon-add', compact('user'));
    }

    public function coupon_store(Request $request)
    {
        $request->validate([
            'code' => 'required',
            'type' => 'required',
            'value' => 'required|numeric',
            'cart_value' => 'required|numeric',
            'expiry_date' => 'required|date'
        ]);

        $coupon = new Coupon();
        $coupon->code = $request->code;
        $coupon->type = $request->type;
        $coupon->value = $request->value;
        $coupon->cart_value = $request->cart_value;
        $coupon->expiry_date = $request->expiry_date;
        $coupon->save();
        return redirect()->route('admin.coupons')->with('status', 'Coupon has been added successfully!');
    }

    public function coupon_edit($id)
    {
        $user = Auth::user();
        $coupon = Coupon::find($id);
        return view('admin.coupon-edit', compact('coupon', 'user'));
    }

    public function coupon_update(Request $request)
    {
        $request->validate([
            'code' => 'required',
            'type' => 'required',
            'value' => 'required|numeric',
            'cart_value' => 'required|numeric',
            'expiry_date' => 'required|date'
        ]);

        $coupon = Coupon::find($request->id);
        $coupon->code = $request->code;
        $coupon->type = $request->type;
        $coupon->value = $request->value;
        $coupon->cart_value = $request->cart_value;
        $coupon->expiry_date = $request->expiry_date;
        $coupon->save();
        return redirect()->route('admin.coupons')->with('status', 'Coupon has been updated successfully!');
    }

    public function coupon_delete($id)
    {
        $coupon = Coupon::find($id);
        $coupon->delete();
        return redirect()->route('admin.coupons')->with('status', 'Coupon has been deleted successfully!');
    }

    public function slides()
    {
        $user = Auth::user();
        $slides = Slide::orderBy('id', 'DESC')->paginate(12);
        return view('admin.slides', compact('slides', 'user'));
    }

    public function slide_add()
    {
        $user = Auth::user();
        return view('admin.slide-add', compact('user'));
    }

    public function slide_store(Request $request)
    {
        $request->validate([
            'tagline' => 'required',
            'title' => 'required',
            'subtitle' => 'required',
            'link' => 'required',
            'status' => 'required',
            'image' => 'required|mimes:png,jpg,jpeg|max:2048'
        ]);

        $slide = new Slide();
        $slide->tagline = $request->tagline;
        $slide->title = $request->title;
        $slide->subtitle = $request->subtitle;
        $slide->link = $request->link;
        $slide->status = $request->status;

        $image = $request->file('image');
        $file_extention = $request->file('image')->extension();
        $file_name = Carbon::now()->timestamp . '_' . Str::random(10) . '.' . $file_extention;
        $this->GenerateSlideThumbailsImage($image, $file_name);
        $slide->image = $file_name;
        $slide->save();
        return redirect()->route('admin.slides')->with('status', 'Slide has been added successfully!');
    }

    public function GenerateSlideThumbailsImage($image, $imageName)
    {
        $destinationPath = public_path('uploads/slides');
        $img = Image::read($image->path());
        $img->cover(400, 690, "top");
        $img->resize(400, 690, function ($constraint) {
            $constraint->aspectRatio();
        })->save($destinationPath . '/' . $imageName);
    }

    public function slide_edit($id)
    {
        $user = Auth::user();
        $slide = Slide::find($id);
        return view('admin.slide-edit', compact('slide', 'user'));
    }

    public function slide_update(Request $request)
    {
        $request->validate([
            'tagline' => 'required',
            'title' => 'required',
            'subtitle' => 'required',
            'link' => 'required',
            'status' => 'required',
            'image' => 'required|mimes:png,jpg,jpeg|max:2048'
        ]);

        $slide = Slide::find($request->id);
        $slide->tagline = $request->tagline;
        $slide->title = $request->title;
        $slide->subtitle = $request->subtitle;
        $slide->link = $request->link;
        $slide->status = $request->status;

        if ($request->hasFile('image')) {
            if (File::exists(public_path('uploads/slides') . '/' . $slide->image)) {
                File::delete(public_path('uploads/slides') . '/' . $slide->image);
            }

            $image = $request->file('image');
            $file_extention = $request->file('image')->extension();
            $file_name = Carbon::now()->timestamp . '_' . Str::random(10) . '.' . $file_extention;
            $this->GenerateSlideThumbailsImage($image, $file_name);
            $slide->image = $file_name;
        }

        $slide->save();
        return redirect()->route('admin.slides')->with('status', 'Slide has been updated successfully!');
    }

    public function slide_delete($id)
    {
        $slide = Slide::find($id);
        if (File::exists(public_path('uploads/slides') . '/' . $slide->image)) {
            File::delete(public_path('uploads/slides') . '/' . $slide->image);
        }
        $slide->delete();
        return redirect()->route('admin.slides')->with('status', 'Slide has been deleted successfully!');
    }

    public function contacts()
    {
        $user = Auth::user();
        $contacts = Contact::orderBy('created_at', 'DESC')->paginate(10);
        return view('admin.contacts', compact('contacts', 'user'));
    }

    public function contact_delete($id)
    {
        $contact = Contact::find($id);
        $contact->delete();
        return redirect()->route('admin.contacts')->with('status', 'Contact has been deleted successfully!');
    }

    public function settings()
    {
        // at this moment, settings table only have one data
        $user = Auth::user();
        $setting = Setting::first();
        return view('admin.settings', compact('setting', 'user'));
    }

    public function setting_update(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'phone' => 'required|numeric|regex:/^[0-9]{10,12}$/',
            'address' => 'required'
        ]);

        // at this moment, settings table only have one data 
        $setting = Setting::first();
        if ($setting) {
            $setting->email = $request->email;
            $setting->phone = $request->phone;
            $setting->phone_second = $request->phone_second;
            $setting->address = $request->address;
            $setting->map = $request->map;
            $setting->twitter = $request->twitter;
            $setting->instagram = $request->instagram;
            $setting->youtube = $request->youtube;
            $setting->pinterest = $request->pinterest;
            $setting->facebook = $request->facebook;
        } else {
            $setting = new Setting();
            $setting->email = $request->email;
            $setting->phone = $request->phone;
            $setting->phone_second = $request->phone_second;
            $setting->address = $request->address;
            $setting->map = $request->map;
            $setting->twitter = $request->twitter;
            $setting->instagram = $request->instagram;
            $setting->youtube = $request->youtube;
            $setting->pinterest = $request->pinterest;
            $setting->facebook = $request->facebook;
        }

        $setting->save();
        return redirect()->route('admin.settings')->with('status', 'Setting has been updated successfully!');
    }

    public function orders()
    {
        $user = Auth::user();
        $orders = Order::orderBy('created_at', 'DESC')->paginate(12);
        return view('admin.orders', compact('orders', 'user'));
    }

    public function order_details($order_id)
    {
        $user = Auth::user();
        $order = Order::find($order_id);
        $orderItems = OrderItem::where('order_id', $order_id)->orderBy('id')->paginate(12);
        $transaction = Transaction::where('order_id', $order_id)->first();
        return view('admin.order-details', compact('order', 'orderItems', 'transaction', 'user'));
    }

    public function update_order_status(Request $request)
    {
        $order = Order::find($request->order_id);
        $order->status = $request->order_status;
        if ($request->order_status == 'delivered') {
            $order->delivered_date = Carbon::now();
        } else if ($request->order_status == 'cancelled') {
            $order->cancelled_date = Carbon::now();
        }
        $order->save();

        if ($request->order_status == 'delivered') {
            $transaction = Transaction::where('order_id', $request->order_id)->first();
            $transaction->status = 'approved';
            $transaction->save();
        }

        return back()->with('status', 'Status changed successfully!');
    }

    public function search(Request $request)
    {
        $query = $request->input('query');
        $results = Product::where('name', 'LIKE', "%{$query}%")->get()->take(8);
        return response()->json($results);
    }

    public function users()
    {
        $user = Auth::user();
        $selected_users = User::withCount('orders')->orderBy('created_at', 'ASC')->paginate(12);
        return view('admin.users', compact('selected_users', 'user'));
    }

    public function user_edit($id)
    {
        $user = Auth::user();
        $selected_user = User::find($id);
        return view('admin.user-edit', compact('selected_user', 'user'));
    }

    public function user_update_role(Request $request)
    {
        $request->validate([
            'utype' => 'required|in:ADM,USR'
        ]);

        $user = User::find($request->id);
        if ($user) {
            $user->utype = $request->utype;
            $user->save();

            return redirect()->route('admin.users')->with('status', 'User updated successfully!');
        }

        return redirect()->route('admin.users')->with('failed', 'User not found!');
    }
}

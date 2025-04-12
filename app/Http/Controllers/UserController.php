<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Transaction;
use App\Models\Address;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index()
    {
        return view('user.index');
    }

    public function orders()
    {
        $orders = Order::where('user_id', Auth::user()->id)->orderBy('created_at', 'DESC')->paginate(10);
        return view('user.orders', compact('orders'));
    }

    public function order_details($order_id)
    {
        $order = Order::where('user_id', Auth::user()->id)->where('id', $order_id)->first();
        if ($order) {
            $orderItems = OrderItem::where('order_id', $order->id)->orderBy('id')->paginate(12);
            $transaction = Transaction::where('order_id', $order->id)->first();
            return view('user.order-details', compact('order', 'orderItems', 'transaction'));
        }

        return redirect()->route('login');
    }

    public function order_cancel(Request $request)
    {
        $order = Order::find($request->order_id);
        $order->status = "cancelled";
        $order->cancelled_date = Carbon::now();
        $order->save();
        return back()->with('status', 'Order has been cancelled successfully!');
    }

    public function addresses()
    {
        $addresses = Address::where('user_id', Auth::user()->id)->orderBy('created_at', 'DESC')->paginate(10);
        return view('user.addresses', compact('addresses'));
    }

    public function address_add()
    {
        return view('user.address-add');
    }

    public function address_store(Request $request)
    {
        $user_id = Auth::user()->id;

        // remove previous default address if user add new default address  
        $default_exist = Address::where('user_id', $user_id)->where('isDefault', true)->first();
        if ($default_exist && $request->has('isdefault') && $request->isdefault) {
            $default_exist->isdefault = false;
            $default_exist->save();
        }

        $request->validate([
            'name' => 'required|max:100',
            'zip' => 'required|numeric|regex:/^[0-9]{4,6}$/',
            'phone' => 'required|numeric|regex:/^[0-9]{10,12}$/',
            'state' => 'required',
            'city' => 'required',
            'address' => 'required',
            'locality' => 'required',
            'landmark' => 'required',
        ]);

        $address = new Address();
        $address->name = $request->name;
        $address->phone = $request->phone;
        $address->state = $request->state;
        $address->zip = $request->zip;
        $address->city = $request->city;
        $address->address = $request->address;
        $address->locality = $request->locality;
        $address->landmark = $request->landmark;
        $address->country = 'Indonesia';
        $address->user_id = $user_id;
        if (!$request->isdefault) {
            $address->isdefault = false;
        } else {
            $address->isdefault = true;
        }

        $address->save();

        return redirect()->route('user.addresses')->with('status', 'Address has been added successfully!');
    }

    public function address_edit($id)
    {
        $address = Address::find($id);
        return view('user.address-edit', compact('address'));
    }

    public function address_update(Request $request)
    {
        $user_id = Auth::user()->id;

        // remove previous default address if user add new default address  
        $default_exist = Address::where('user_id', $user_id)->where('isDefault', true)->first();
        if ($default_exist && $request->has('isdefault') && $request->isdefault) {
            $default_exist->isdefault = false;
            $default_exist->save();
        }

        $request->validate([
            'name' => 'required|max:100',
            'zip' => 'required|numeric|regex:/^[0-9]{4,6}$/',
            'phone' => 'required|numeric|regex:/^[0-9]{10,12}$/',
            'state' => 'required',
            'city' => 'required',
            'address' => 'required',
            'locality' => 'required',
            'landmark' => 'required',
        ]);

        $address = Address::find($request->id);
        $address->name = $request->name;
        $address->phone = $request->phone;
        $address->state = $request->state;
        $address->zip = $request->zip;
        $address->city = $request->city;
        $address->address = $request->address;
        $address->locality = $request->locality;
        $address->landmark = $request->landmark;
        $address->country = 'Indonesia';
        $address->user_id = $user_id;
        if (!$request->isdefault) {
            $address->isdefault = false;
        } else {
            $address->isdefault = true;
        }

        $address->save();
        return redirect()->route('user.addresses')->with('status', 'Address has been updated successfully!');
    }

    public function details()
    {
        $user = User::find(Auth::user()->id);
        return view('user.details', compact('user'));
    }

    public function detail_update(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'mobile' => 'required|numeric|regex:/^[0-9]{10,12}$/',
            'name' => 'required|max:255'
        ]);

        $user = User::find(Auth::user()->id);
        $user->email = $request->email;
        $user->name = $request->name;
        $user->mobile = $request->mobile;

        if ($request->old_password && $request->new_password) {
            $request->validate([
                'old_password' => 'required',
                'new_password' => 'required|min:8|confirmed',
            ]);

            if (!Hash::check($request->old_password, Auth::user()->password)) {
                return back()->withErrors(['old_password' => 'The provided password does not match our records.']);
            }

            $user->password = Hash::make($request->new_password);
        }

        $user->save();

        return redirect()->route('user.details')->with('status', 'Account setting has been updated successfully!');
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Coupon;
use Illuminate\Http\Request;

class CouponController extends Controller
{
    public function index()
    {
        $coupons = Coupon::latest()->paginate(15);
        return view('admin.coupons.index', compact('coupons'));
    }

    public function create()
    {
        return view('admin.coupons.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'code'       => 'required|unique:coupons,code',
            'type'       => 'required|in:fixed,percentage',
            'value'      => 'required|numeric|min:0.01',
            'max_uses'   => 'nullable|integer|min:1',
            'expires_at' => 'nullable|date|after:today',
        ]);

        Coupon::create($request->all());

        return redirect()->route('coupons.index')->with('success', 'Coupon created successfully.');
    }

    public function edit(Coupon $coupon)
    {
        return view('admin.coupons.edit', compact('coupon'));
    }

    public function update(Request $request, Coupon $coupon)
    {
        $request->validate([
            'code'       => 'required|unique:coupons,code,' . $coupon->id,
            'type'       => 'required|in:fixed,percentage',
            'value'      => 'required|numeric|min:0.01',
            'max_uses'   => 'nullable|integer|min:1',
            'expires_at' => 'nullable|date|after:today',
        ]);

        $coupon->update($request->all());

        return redirect()->route('coupons.index')->with('success', 'Coupon updated successfully.');
    }


    public function destroy(Coupon $coupon)
    {
        $coupon->delete();
        return back()->with('success', 'Coupon deleted.');
    }
}

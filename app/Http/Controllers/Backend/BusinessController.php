<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Business;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class BusinessController extends Controller
{
    public function index()
    {
        $this->checkAuthorization(auth()->user(), ['business.view']);

        $businesses = auth()->user()->businesses()->paginate(10);
        $currentBusiness = app()->has('current_business') ? app('current_business') : null;

        return view('backend.pages.businesses.index', compact('businesses', 'currentBusiness'));
    }

    public function create()
    {
        $this->checkAuthorization(auth()->user(), ['business.create']);
        return view('backend.pages.businesses.create');
    }

    public function store(Request $request)
    {
        $this->checkAuthorization(auth()->user(), ['business.create']);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'nullable|string',
            'email' => 'nullable|email',
            'phone' => 'nullable|string',
            'whatsapp_phone_number_id' => 'required|string|unique:businesses',
            'whatsapp_business_account_id' => 'required|string',
            'whatsapp_access_token' => 'required|string',
            'whatsapp_catalog_id' => 'nullable|string',
            'currency' => 'required|string|max:10',
            'delivery_charge' => 'required|numeric|min:0',
        ]);

        $validated['user_id'] = auth()->id();
        $validated['slug'] = Str::slug($validated['name']);
        $validated['whatsapp_verify_token'] = Str::random(32);

        $business = Business::create($validated);

        // Set as current business
        session(['current_business_id' => $business->id]);

        session()->flash('success', __('Business has been created successfully.'));

        return redirect()->route('admin.businesses.index');
    }

    public function edit(int $id)
    {
        $this->checkAuthorization(auth()->user(), ['business.edit']);

        $business = Business::findOrFail($id);

        // Check ownership
        if ($business->user_id !== auth()->id() && !auth()->user()->hasRole('Super Admin')) {
            abort(403, 'Unauthorized action.');
        }

        return view('backend.pages.businesses.edit', compact('business'));
    }

    public function update(Request $request, int $id)
    {
        $this->checkAuthorization(auth()->user(), ['business.edit']);

        $business = Business::findOrFail($id);

        // Check ownership
        if ($business->user_id !== auth()->id() && !auth()->user()->hasRole('Super Admin')) {
            abort(403, 'Unauthorized action.');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'nullable|string',
            'email' => 'nullable|email',
            'phone' => 'nullable|string',
            'whatsapp_phone_number_id' => 'required|string|unique:businesses,whatsapp_phone_number_id,' . $id,
            'whatsapp_business_account_id' => 'required|string',
            'whatsapp_access_token' => 'required|string',
            'whatsapp_catalog_id' => 'nullable|string',
            'currency' => 'required|string|max:10',
            'delivery_charge' => 'required|numeric|min:0',
            'is_active' => 'nullable|boolean',
        ]);

        $validated['is_active'] = $request->has('is_active');

        $business->update($validated);

        session()->flash('success', __('Business has been updated successfully.'));

        return back();
    }

    public function destroy(int $id)
    {
        $this->checkAuthorization(auth()->user(), ['business.delete']);

        $business = Business::findOrFail($id);

        // Check ownership
        if ($business->user_id !== auth()->id() && !auth()->user()->hasRole('Super Admin')) {
            abort(403, 'Unauthorized action.');
        }

        // Don't allow deleting if it's the only business
        if (auth()->user()->businesses()->count() <= 1) {
            session()->flash('error', __('You cannot delete your only business.'));
            return back();
        }

        $business->delete();

        // If deleted business was current, switch to another
        if (session('current_business_id') == $id) {
            $newBusiness = auth()->user()->businesses()->first();
            session(['current_business_id' => $newBusiness ? $newBusiness->id : null]);
        }

        session()->flash('success', __('Business has been deleted successfully.'));

        return redirect()->route('admin.businesses.index');
    }

    public function switchBusiness(int $id)
    {
        $business = Business::findOrFail($id);

        // Check ownership
        if ($business->user_id !== auth()->id()) {
            abort(403, 'Unauthorized action.');
        }

        session(['current_business_id' => $business->id]);

        session()->flash('success', __('Switched to :name', ['name' => $business->name]));

        return redirect()->route('admin.dashboard');
    }
}
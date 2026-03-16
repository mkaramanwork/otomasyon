<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    // Listeleme
    public function index()
    {
        $customers = Customer::with('user')->latest()->get();
        return view('customers.index', compact('customers'));
    }

    // Ekleme formu
    public function create()
    {
        return view('customers.create');
    }

    // Veriyi kaydet
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email',
            'phone' => 'nullable|string|max:20',
        ]);

        Customer::create([
            'name' => $request->name,
            'contact_person' => $request->contact_person,
            'email' => $request->email,
            'phone' => $request->phone,
            'address' => $request->address,
            'added_by' => auth()->id()
        ]);

        return redirect()->route('customers.index')->with('success', 'Müşteri başarıyla eklendi.');
    }

    // Düzenleme formu
    public function edit(Customer $customer)
    {
        return view('customers.edit', compact('customer'));
    }

    // Güncelleme işlemi
    public function update(Request $request, Customer $customer)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email',
            'phone' => 'nullable|string|max:20',
        ]);

        $customer->update($request->all());

        return redirect()->route('customers.index')->with('success', 'Müşteri başarıyla güncellendi.');
    }

    // Silme işlemi
    public function destroy(Customer $customer)
    {
        $customer->delete();
        return redirect()->route('customers.index')->with('success', 'Müşteri silindi.');
    }
}
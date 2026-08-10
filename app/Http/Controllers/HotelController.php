<?php

namespace App\Http\Controllers;

use App\Models\Hotel;
use Illuminate\Http\Request;

class HotelController extends Controller
{
    public function index()
    {
        return view('admin.hotels.index', ['hotels' => Hotel::all()]);
    }

    public function create()
    {
        return view('admin.hotels.create');
    }

    public function store(Request $request)
    {
        Hotel::create($request->validate(['name' => 'required|string|max:255', 'address' => 'nullable|string', 'phone_number' => 'nullable|string|max:50']));
        return redirect()->route('admin.hotels.index')->with('success', 'Hotel berhasil ditambahkan!');
    }

    public function edit(Hotel $hotel)
    {
        return view('admin.hotels.edit', compact('hotel'));
    }

    public function update(Request $request, Hotel $hotel)
    {
        $hotel->update($request->validate(['name' => 'required|string|max:255', 'address' => 'nullable|string', 'phone_number' => 'nullable|string|max:50']));
        return redirect()->route('admin.hotels.index')->with('success', 'Data hotel berhasil diperbarui!');
    }

    public function destroy(Hotel $hotel)
    {
        $hotel->delete();
        return redirect()->route('admin.hotels.index')->with('success', 'Hotel berhasil dihapus!');
    }
}

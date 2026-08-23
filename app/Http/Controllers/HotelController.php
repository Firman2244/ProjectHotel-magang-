<?php

namespace App\Http\Controllers;

use App\Models\Hotel;
use Illuminate\Http\Request;
use Illuminate\Database\QueryException;

class HotelController extends Controller
{
    public function index()
    {
        $hotels = Hotel::all();

        return view('admin.hotels.index', compact('hotels'));
    }

    public function create()
    {
        return view('admin.hotels.create');
    }

    public function store(Request $request)
    {
        Hotel::create($this->validatedData($request));

        return redirect()->route('admin.hotels.index')->with('success', 'Hotel berhasil ditambahkan!');
    }

    public function edit(Hotel $hotel)
    {
        return view('admin.hotels.edit', compact('hotel'));
    }

    public function update(Request $request, Hotel $hotel)
    {
        $hotel->update($this->validatedData($request));

        return redirect()->route('admin.hotels.index')->with('success', 'Data hotel berhasil diperbarui!');
    }

    public function destroy(Hotel $hotel)
    {
        try {
            $hotel->delete();

            return redirect()->route('admin.hotels.index')->with('success', 'Hotel berhasil dihapus!');
        } catch (QueryException $e) {
            return redirect()->back()->withErrors('Gagal menghapus! Hotel ini tidak bisa dihapus karena masih menjadi penempatan bagi data Karyawan atau Laporan aktif.');
        }
    }

    private function validatedData(Request $request)
    {
        return $request->validate([
            'name'         => 'required|string|max:255',
            'address'      => 'nullable|string',
            'phone_number' => 'nullable|string|max:50',
        ]);
    }
}

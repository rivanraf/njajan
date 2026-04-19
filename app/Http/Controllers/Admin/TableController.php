<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Table;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class TableController extends Controller
{
    public function index()
    {
        $tables = Table::orderBy('number', 'asc')->get();
        return view('admin.tables.index', compact('tables'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'number' => 'required|unique:tables,number',
        ]);

        Table::create([
            'number' => $request->number,
            'hash' => Str::random(10), // Hash unik untuk QR Code
            'status' => 'available'
        ]);

        return redirect()->back()->with('success', 'Meja berhasil ditambahkan!');
    }

    public function destroy($id)
    {
        $table = Table::findOrFail($id);

        if ($table->orders()->exists()) {
            return redirect()->back()->with('error', 'Meja memiliki transaksi, gunakan fitur Nonaktifkan.');
        }

        $table->delete();
        return redirect()->back()->with('success', 'Meja berhasil dihapus!');
    }

    public function toggleStatus($id)
    {
        $table = Table::findOrFail($id);
        
        // Toggle status: dari available ke nonaktif atau sebaliknya
        $table->status = $table->status === 'available' ? 'nonaktif' : 'available';
        $table->save();

        return redirect()->back()->with('success', 'Status meja berhasil diperbarui!');
    }

    public function print($id)
    {
        $table = Table::findOrFail($id);
        $url = route('scan.qr', $table->hash);
        return view('admin.tables.print', compact('table', 'url'));
    }
}
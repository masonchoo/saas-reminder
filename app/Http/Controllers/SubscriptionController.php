<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\SubscriptionImport;

class SubscriptionController extends Controller
{
    public function import_excel(Request $request)
    {
        // validation
        $request->validate([
            'file' => 'required|mimes:csv,xls,xlsx'
        ]);

        // get file excel
        $file = $request->file('file');

        // create unique file name
        $file_name = rand() . $file->getClientOriginalName();

        // upload file to public folder
        $file->move('file/excel', $file_name);

        // import data
        Excel::import(new SubscriptionImport, 'file/excel/' . $file_name);


        // redirect to main page upload
        return back()->with('success', 'Subscriptions imported successfully!');
    }
}

<?php

namespace App\Http\Controllers\Admin\MasterData;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::with(['userDetail.kabupaten'])
            ->whereHas('userDetail', function ($q) {
                $q->where('is_verified', true);
            })
            ->whereHas('roles', function ($q) {
                $q->where('name', 'operator');
            });

        // Filter by date range if provided
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $startDate = $request->start_date;
            $endDate = $request->end_date;

            $query->whereHas('userDetail', function ($q) use ($startDate, $endDate) {
                $q->whereBetween('verified_at', [$startDate, $endDate]);
            });
        }

        $users = $query->get();

        return view('pages.admin.master-data.user.index', compact('users'));
    }
}

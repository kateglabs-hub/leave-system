<?php

namespace App\Http\Controllers;

use App\Models\LeaveRequest;
use App\Models\LeaveType;
use App\Models\Department;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function individual(Request $request, $userId = null)
    {
        $user = Auth::user();
        $target = $userId ?: $user->id;
        if ($target != $user->id && !in_array($user->role,['hr','admin'])) abort(403);

        $year = $request->query('year',date('Y'));

        $userData = User::with('department')->findOrFail($target);
        $balances = DB::table('leave_balances')->join('leave_types','leave_balances.leave_type_id','=','leave_types.id')
            ->where('leave_balances.user_id',$target)->where('leave_balances.year',$year)->select('leave_balances.*','leave_types.name as leave_type_name')->get();

        $history = LeaveRequest::where('user_id',$target)->whereYear('start_date',$year)->get();

        $stats = LeaveRequest::where('user_id',$target)->whereYear('start_date',$year)
            ->selectRaw('count(*) as total_requests,sum(case when status="approved" then 1 else 0 end) as approved_count,sum(case when status="approved" then total_days else 0 end) as total_days_taken')
            ->first();

        return response()->json(['success'=>true,'user'=>$userData,'balances'=>$balances,'history'=>$history,'statistics'=>$stats,'year'=>$year]);
    }

    public function company(Request $request)
    {
        $user = Auth::user();
        if (!in_array($user->role,['hr','admin'])) abort(403);
        $year = $request->query('year',date('Y'));

        $totalEmployees = User::where('role','!=','admin')->count();
        $overall = LeaveRequest::whereYear('start_date',$year)
            ->selectRaw('count(*) as total_requests,sum(case when status="approved" then 1 else 0 end) as approved_requests,sum(case when status="pending" then 1 else 0 end) as pending_requests,sum(case when status="rejected" then 1 else 0 end) as rejected_requests,sum(case when status="approved" then total_days else 0 end) as total_days_taken')->first();

        return response()->json(['success'=>true,'total_employees'=>$totalEmployees,'overall_stats'=>$overall,'year'=>$year]);
    }
}

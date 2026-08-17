<?php

namespace App\Http\Controllers;

use App\Models\LeaveRequest;
use App\Models\LeaveBalance;
use App\Models\LeaveType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class LeaveController extends Controller
{
    // List leave requests
    public function index(Request $request)
    {
        $user = Auth::user();

        if ($request->has('user_id')) {
            $userId = $request->query('user_id');
            if ($userId != $user->id && !in_array($user->role,['hr','admin','manager'])) {
                abort(403);
            }
            $requests = LeaveRequest::where('user_id',$userId)->get();
        } else {
            if (!in_array($user->role,['hr','admin','manager'])) {
                abort(403);
            }
            $requests = LeaveRequest::with('user')->get();
        }

        return response()->json(['success'=>true,'requests'=>$requests]);
    }

    // Create leave request
    public function store(Request $request)
    {
        $data = $request->validate([
            'leave_type_id'=>'required|integer|exists:leave_types,id',
            'start_date'=>'required|date',
            'end_date'=>'required|date|after_or_equal:start_date',
            'reason'=>'nullable|string'
        ]);

        $user = Auth::user();
        $data['user_id'] = $user->id;

        $totalDays = $this->calculateWorkingDays(new \DateTime($data['start_date']), new \DateTime($data['end_date']));

        $balance = LeaveBalance::where('user_id',$user->id)->where('leave_type_id',$data['leave_type_id'])->where('year',date('Y'))->first();
        if (!$balance || $balance->remaining_days < $totalDays) {
            return response()->json(['success'=>false,'message'=>'Insufficient leave balance'],422);
        }

        $data['total_days'] = $totalDays;
        $requestModel = LeaveRequest::create($data);

        return response()->json(['success'=>true,'request_id'=>$requestModel->id,'total_days'=>$totalDays]);
    }

    // Approve or reject
    public function updateStatus(Request $request, $id)
    {
        $user = Auth::user();
        if (!in_array($user->role,['hr','admin','manager'])) abort(403);

        $payload = $request->validate(['status'=>'required|in:approved,rejected','rejection_reason'=>'nullable|string']);

        $requestModel = LeaveRequest::findOrFail($id);
        $requestModel->status = $payload['status'];
        $requestModel->approved_by = $user->id;
        $requestModel->approved_at = now();
        $requestModel->rejection_reason = $payload['rejection_reason'] ?? null;
        $requestModel->save();

        if ($payload['status'] === 'approved') {
            $year = date('Y',strtotime($requestModel->start_date));
            $balance = LeaveBalance::where('user_id',$requestModel->user_id)->where('leave_type_id',$requestModel->leave_type_id)->where('year',$year)->first();
            if ($balance) {
                $balance->used_days += $requestModel->total_days;
                $balance->remaining_days -= $requestModel->total_days;
                $balance->save();
            }
        }

        return response()->json(['success'=>true]);
    }

    private function calculateWorkingDays($start,$end)
    {
        $days = 0;
        $current = clone $start;
        while ($current <= $end) {
            $day = (int)$current->format('N');
            if ($day < 6) $days++;
            $current->modify('+1 day');
        }
        return $days;
    }
}

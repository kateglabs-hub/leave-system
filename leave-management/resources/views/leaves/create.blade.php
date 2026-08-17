@extends('layouts.app')

@section('content')
<h2>Request Leave</h2>
<form method="POST" action="/leaves">
    @csrf
    <label>Leave Type
        <select name="leave_type_id">
            @foreach(App\Models\LeaveType::all() as $lt)
                <option value="{{ $lt->id }}">{{ $lt->name }}</option>
            @endforeach
        </select>
    </label>
    <label>Start Date <input type="date" name="start_date" required></label>
    <label>End Date <input type="date" name="end_date" required></label>
    <label>Reason <textarea name="reason"></textarea></label>
    <button type="submit">Submit</button>
</form>
@endsection

// function approve_leave(Leave $leave, $mode){
// if($mode === 'leave'){
// $curr_leave = User::find(Auth::id())->overwork_allowance;
// $new_balance = $curr_leave - $leave->leave_period;
// User::find(Auth::id())->update(['overwork_allowance' => $new_balance]);
// }
// else if($mode === 'overwork') {
// $curr_leave = User::find(Auth::id())->total_overwork;
// $new_balance = $curr_leave - $leave->leave_period;
// User::find(Auth::id())->update(['total_overwork' => $new_balance]);
// }
// }
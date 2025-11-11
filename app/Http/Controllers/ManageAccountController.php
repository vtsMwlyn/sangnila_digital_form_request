<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Leave;
use Illuminate\Http\Request;

class ManageAccountController extends Controller
{
    /**
     * Display the specified resource.
     */
    public function show()
    {
        $data = User::all()->sortByDesc('created_at');

        return view('view.admin.manage-account', compact('data'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id, string $status)
    {
        $user = User::findOrFail($id);

        if (!in_array($status, ['active', 'suspended'])) {
            return redirect()->back()->with('fail', [
                'title' => 'Invalid status',
                'message' => 'Status must be either active or suspended.',
            ]);
        }

        $user->forceFill(['status_account' => $status])->save();

        $user->refresh();
        return redirect()->back()->with('success', [
            'title' => "{$user->name} is now {$status}",
            'message' => 'Account status has been updated successfully.',
        ]);
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        User::findOrFail($id)->delete();

        return redirect()->back()->with('success', [
            'title' => 'Account deleted',
            'message' => 'The account has been successfully removed.',
        ]);
    }


    public function fetch(string $id)
    {
        $user = User::findOrFail($id);
        return response()->json($user);
    }

}
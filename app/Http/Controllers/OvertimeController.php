<?php

namespace App\Http\Controllers;

use Exception;
use Carbon\Carbon;
use App\Models\Evidence;
use App\Models\Overtime;
use App\Models\ActionLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Mail;

class OvertimeController
{
    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('view.users.overtime-request');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validate = $request->validate([
            'date' => ['required', 'date'],
            'start' => ['required'],
            'finish' => ['required'],
            'desc' => ['required'],
            'user_id' => ['required'],
            'photo' => 'required_without:video|array',
            'video' => 'required_without:photo|array',
            'photo*' => 'mimes:jpg,jpeg,png,webp|max:5120',
            'video*' => 'mimes:mp4,mov,avi|max:10240'
        ], [
            'photo.required_without' => 'Please upload a photo or video',
            'video.required_without' => 'Please upload a photo or video'
        ]);

        $status = $request->action === 'submit' ? 'review' : 'draft';

        try {
            DB::beginTransaction();

            // Make sure overtime duration is floored to 0.5 multiplication
            $start = Carbon::parse($validate['start']);
            $end   = Carbon::parse($validate['finish']);

            if ($end->lessThan($start)) {
                $end->addDay();
            }

            $minutes = $start->diffInMinutes($end);

            $hours = $minutes / 60;
            $adjustedHourDiff = floor($hours * 2) / 2;

            $overtime = Overtime::create([
                'overtime_date' => $validate['date'],
                'start_overtime' => $validate['start'],
                'finished_overtime' => $validate['finish'],
                'duration' => $adjustedHourDiff,
                'task_description' => $validate['desc'],
                'request_status' => $status,
                'user_id' => $validate['user_id'],
            ]);

            $path = [];
            if ($request->hasFile('photo')) {
                foreach ($request->file('photo') as $photo) {
                    $path[] = $photo->store('evidence/photo', 'public');
                }
            }
            if ($request->hasFile('video')) {
                foreach ($request->file('video') as $photo) {
                    $path[] = $photo->store('evidence/video', 'public');
                }
            }

            foreach ($path as $p) {
                Evidence::create([
                    'path' => $p,
                    'overtime_id' => $overtime->id,
                ]);
            }

            ActionLog::create([
                'user_id' => Auth::id(),
                'mode' => 'overtime',
                'message' => $status !== 'draft' ? 'Submitted an overtime request' : 'Created an overtime request draft'
            ]);

            if($status === 'review'){
                Mail::html('Hello Sangnila HR, <b>' . $overtime->user->name . '</b> has submitted an <b>overtime</b> request that he/she did on <b>' . $overtime->overtime_date . '</b>. <br/>Please do a review to the request <a href="https://ems.sangnilaindonesia.com">here</a>. Thank you!', function ($message) use ($overtime) {
                    $message->to('hr@sangnilaindonesia.com')
                            ->subject('New Employee Overtime Request from ' . $overtime->user->name);
                });
            }

            DB::commit();
        }
        catch (Exception $e) {
            DB::rollBack();
            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => $e->getMessage()]);
            }
            return redirect()->back()->withErrors(['err' => $e->getMessage()]);
        }

        if ($status == 'draft')
            return redirect()->route('overtime.show')->with('success', [
                'title' => 'Saved to draft!',
                'message' => 'Your overtime request has been saved to draft.',
                'time' => now()->setTimezone('Asia/Jakarta')->format('Y-m-d | H:i'),
            ]);

        if ($status === 'review') return redirect()->route('overtime.show')->with('success', [
            'title' => 'Overtime Submitted!',
            'message' => 'Please wait for admin approval.',
            'time' => now()->setTimezone('Asia/Jakarta')->format('Y-m-d | H:i'),
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Overtime $overtime) {}

    /**
     * Show the form for editing the specified resource.
     */
    
    public function edit(Overtime $overtime)
    {
        $request = new RequestController;
        $evidence = [];
        foreach ($request->requestData() as $item) {
            if ($item->id === $overtime->id && $item->type === 'overtime') {
                foreach ($item->evidence as $e) {
                    $evidence[] = $e;
                }
                break;
            }
        }
        return view('view.users.overtime-request', compact('evidence', 'overtime'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Overtime $overtime)
    {
        $validated = $request->validate([
            'date'   => ['required', 'date'],
            'start'  => ['required'],
            'finish' => ['required'],
            'desc'   => ['required'],

            'photo.*' => ['mimes:jpg,jpeg,png,webp', 'max:5120'],
            'video.*' => ['mimes:mp4,mov,avi', 'max:10240'],

            'deleted_evidence_ids'   => ['array'],
            'deleted_evidence_ids.*' => ['integer'],
        ]);

        $status = $request->action === 'submit' ? 'review' : 'draft';

        try {
            // ===== UPDATE MAIN DATA =====
            $overtime->update([
                'overtime_date'     => $validated['date'],
                'start_overtime'    => $validated['start'],
                'finished_overtime' => $validated['finish'],
                'task_description'  => $validated['desc'],
                'request_status'    => $status,
            ]);

            // ===== DELETE REMOVED EVIDENCE =====
            if (!empty($validated['deleted_evidence_ids'])) {
                $evidences = Evidence::whereIn('id', $validated['deleted_evidence_ids'])
                    ->where('overtime_id', $overtime->id)
                    ->get();

                foreach ($evidences as $evidence) {
                    Storage::disk('public')->delete($evidence->path);
                    $evidence->delete();
                }
            }

            // ===== SAVE NEW PHOTOS =====
            if ($request->hasFile('photo')) {
                foreach ($request->file('photo') as $photo) {
                    Evidence::create([
                        'overtime_id' => $overtime->id,
                        'path' => $photo->store('evidence/photo', 'public'),
                    ]);
                }
            }

            // ===== SAVE NEW VIDEOS =====
            if ($request->hasFile('video')) {
                foreach ($request->file('video') as $video) {
                    Evidence::create([
                        'overtime_id' => $overtime->id,
                        'path' => $video->store('evidence/video', 'public'),
                    ]);
                }
            }

            // ===== LOG ACTION =====
            ActionLog::create([
                'user_id' => Auth::id(),
                'mode'    => 'overtime',
                'message' => $status === 'draft'
                    ? 'Updated an overtime request draft'
                    : 'Submitted an overtime request',
            ]);

            // ===== RESPONSE =====
            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => $status === 'draft'
                        ? 'Draft updated successfully'
                        : 'Overtime submitted successfully',
                ]);
            }

            return redirect()
                ->route('overtime.show')
                ->with('success', [
                    'title'   => $status === 'draft'
                        ? 'Draft Updated!'
                        : 'Overtime Submitted!',
                    'message' => $status === 'draft'
                        ? 'Your overtime request has been saved as draft.'
                        : 'Please wait for admin approval.',
                    'time'    => now()
                        ->setTimezone('Asia/Jakarta')
                        ->format('Y-m-d | H:i'),
                ]);

        } catch (\Exception $e) {

            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Something went wrong.',
                ], 500);
            }

            return redirect()
                ->back()
                ->withErrors(['error' => $e->getMessage()]);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Overtime $overtime)
    {
        try {
            $overtime->delete();

            ActionLog::create([
                'user_id' => Auth::id(),
                'mode' => 'overtime',
                'message' => 'Deleted an overtime request draft'
            ]);

            return redirect()->back()->with('success', [
                'title' => 'Overtime draft deleted successfully',
                'message' => 'Your overtime draft has been deleted.',
                'time' => now()->setTimezone('Asia/Jakarta')->format('Y-m-d | H:i'),
            ]);
        } catch (Exception $e) {
            return redirect()->route('dashboard')->withErrors(['error' => 'Failed to delete overtime draft: ' . $e->getMessage()]);
        }
    }

    /**
     * Delete a specific evidence.
     */
    public function deleteEvidence(Evidence $evidence)
    {
        try {
            $evidence->delete();
            return response()->json(['success' => true]);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }
}

<?php

namespace App\Http\Controllers;

use Exception;
use Carbon\Carbon;
use App\Models\Evidence;
use App\Models\Overwork;
use App\Models\ActionLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class OverworkController
{
    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('view.users.overwork-request');
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

            // Make sure overwork duration is floored to 0.5 multiplication
            $start = Carbon::parse($validate['start']);
            $end   = Carbon::parse($validate['finish']);

            if ($end->lessThan($start)) {
                $end->addDay();
            }

            $minutes = $start->diffInMinutes($end);

            $hours = $minutes / 60;
            $adjustedHourDiff = floor($hours * 2) / 2;

            $overwork = Overwork::create([
                'overwork_date' => $validate['date'],
                'start_overwork' => $validate['start'],
                'finished_overwork' => $validate['finish'],
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
                    'overwork_id' => $overwork->id,
                ]);
            }

            ActionLog::create([
                'user_id' => Auth::id(),
                'mode' => 'overwork',
                'message' => $status !== 'draft' ? 'Submitted an overwork request' : 'Created an overwork request draft'
            ]);

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
            return redirect()->route('overwork.show')->with('success', [
                'title' => 'Saved to draft!',
                'message' => 'Your overwork request has been saved to draft.',
                'time' => now()->setTimezone('Asia/Jakarta')->format('Y-m-d | H:i'),
            ]);

        if ($status === 'review') return redirect()->route('overwork.show')->with('success', [
            'title' => 'Overwork Submitted!',
            'message' => 'Please wait for admin approval.',
            'time' => now()->setTimezone('Asia/Jakarta')->format('Y-m-d | H:i'),
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Overwork $overwork) {}

    /**
     * Show the form for editing the specified resource.
     */
    
    public function edit(Overwork $overwork)
    {
        $request = new RequestController;
        $evidence = [];
        foreach ($request->requestData() as $item) {
            if ($item->id === $overwork->id && $item->type === 'overwork') {
                foreach ($item->evidence as $e) {
                    $evidence[] = $e;
                }
                break;
            }
        }
        return view('view.users.overwork-request', compact('evidence', 'overwork'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Overwork $overwork)
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
            $overwork->update([
                'overwork_date'     => $validated['date'],
                'start_overwork'    => $validated['start'],
                'finished_overwork' => $validated['finish'],
                'task_description'  => $validated['desc'],
                'request_status'    => $status,
            ]);

            // ===== DELETE REMOVED EVIDENCE =====
            if (!empty($validated['deleted_evidence_ids'])) {
                $evidences = Evidence::whereIn('id', $validated['deleted_evidence_ids'])
                    ->where('overwork_id', $overwork->id)
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
                        'overwork_id' => $overwork->id,
                        'path' => $photo->store('evidence/photo', 'public'),
                    ]);
                }
            }

            // ===== SAVE NEW VIDEOS =====
            if ($request->hasFile('video')) {
                foreach ($request->file('video') as $video) {
                    Evidence::create([
                        'overwork_id' => $overwork->id,
                        'path' => $video->store('evidence/video', 'public'),
                    ]);
                }
            }

            // ===== LOG ACTION =====
            ActionLog::create([
                'user_id' => Auth::id(),
                'mode'    => 'overwork',
                'message' => $status === 'draft'
                    ? 'Updated an overwork request draft'
                    : 'Submitted an overwork request',
            ]);

            // ===== RESPONSE =====
            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => $status === 'draft'
                        ? 'Draft updated successfully'
                        : 'Overwork submitted successfully',
                ]);
            }

            return redirect()
                ->route('overwork.show')
                ->with('success', [
                    'title'   => $status === 'draft'
                        ? 'Draft Updated!'
                        : 'Overwork Submitted!',
                    'message' => $status === 'draft'
                        ? 'Your overwork request has been saved as draft.'
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
    public function destroy(Overwork $overwork)
    {
        try {
            $overwork->delete();

            ActionLog::create([
                'user_id' => Auth::id(),
                'mode' => 'overwork',
                'message' => 'Deleted an overwork request draft'
            ]);

            return redirect()->back()->with('success', [
                'title' => 'Overwork draft deleted successfully',
                'message' => 'Your overwork draft has been deleted.',
                'time' => now()->setTimezone('Asia/Jakarta')->format('Y-m-d | H:i'),
            ]);
        } catch (Exception $e) {
            return redirect()->route('dashboard')->withErrors(['error' => 'Failed to delete overwork draft: ' . $e->getMessage()]);
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

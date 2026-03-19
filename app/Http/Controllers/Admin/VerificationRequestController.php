<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\VerificationRequest;
use App\Notifications\VerificationRequestStatusChangedNotification;
use Illuminate\Http\Request;

class VerificationRequestController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware(\App\Http\Middleware\EnsureUserIsAdmin::class);
    }

    public function index(Request $request)
    {
        $q = VerificationRequest::query();
        if ($request->filled('status')) {
            $q->where('status', $request->status);
        }

        $requests = $q->latest()->paginate(30);

        return view('admin.verification_requests.index', compact('requests'));
    }

    public function show(VerificationRequest $verificationRequest)
    {
        return view('admin.verification_requests.show', ['request' => $verificationRequest]);
    }

    public function approve(Request $request, VerificationRequest $verificationRequest)
    {
        if ($verificationRequest->status !== VerificationRequest::STATUS_PENDING) {
            return back()->with('error', 'Заявка уже обработана.');
        }

        $verificationRequest->update([
            'status' => VerificationRequest::STATUS_APPROVED,
            'admin_id' => $request->user()->id,
            'processed_at' => now(),
        ]);

        if ($verificationRequest->company) {
            $verificationRequest->company->is_verified = true;
            $verificationRequest->company->save();
        }

        $verificationRequest->user->notify(new VerificationRequestStatusChangedNotification($verificationRequest));

        return redirect()->route('admin.verification_requests.index')->with('success', 'Заявка подтверждена.');
    }

    public function reject(Request $request, VerificationRequest $verificationRequest)
    {
        $request->validate(['rejection_reason' => 'nullable|string|max:2000']);

        if ($verificationRequest->status !== VerificationRequest::STATUS_PENDING) {
            return back()->with('error', 'Заявка уже обработана.');
        }

        $verificationRequest->update([
            'status' => VerificationRequest::STATUS_REJECTED,
            'admin_id' => $request->user()->id,
            'rejection_reason' => $request->rejection_reason,
            'processed_at' => now(),
        ]);

        $verificationRequest->user->notify(new VerificationRequestStatusChangedNotification($verificationRequest));

        return redirect()->route('admin.verification_requests.index')->with('success', 'Заявка отклонена.');
    }
}

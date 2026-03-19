<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\VerificationRequest;
use App\Notifications\NewVerificationRequestNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;

class VerificationRequestController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $user = $request->user();
        // return requests created by this user
        $requests = VerificationRequest::where('user_id', $user->id)->latest()->paginate(20);

        return view('verification_requests.index', compact('requests'));
    }

    public function create()
    {
        return view('verification_requests.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'company_name' => 'required|string|max:255',
            'description' => 'required|string',
            'contact' => 'required|string|max:1000',
            'attachments.*' => 'file|mimes:pdf,jpg,jpeg,png,doc,docx|max:5120',
        ]);

        $data = $request->only(['company_name', 'description', 'contact']);
        $data['user_id'] = $request->user()->id;
        $data['company_id'] = $request->user()->company ? $request->user()->company->id : null;

        $attachments = [];
        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                $path = $file->store('verification_requests', 'public');
                $attachments[] = $path;
            }
        }

        $data['attachments'] = $attachments ?: null;

        $vr = VerificationRequest::create($data);

        // Notify all admins
        $admins = User::whereIn('role', [User::ROLE_ADMIN, User::ROLE_SUPER_ADMIN])->get();
        Notification::send($admins, new NewVerificationRequestNotification($vr));

        return redirect()->route('verification_requests.index')->with('success', 'Заявка отправлена.');
    }

    public function show(VerificationRequest $verificationRequest)
    {
        $this->authorize('view', $verificationRequest);

        return view('verification_requests.show', ['request' => $verificationRequest]);
    }
}

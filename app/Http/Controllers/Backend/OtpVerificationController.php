<?php

namespace App\Http\Controllers\Backend;

use App\Models\OtpVerification;
use App\Http\Controllers\Backend\BaseController;
use Illuminate\Http\Request;

class OtpVerificationController extends BaseController
{
    protected string $resource = 'otp_verification';
    
    protected array $additionalPermissions = ['otp_verification_management_access'];

    public function index()
    {
        $otpVerifications = OtpVerification::orderBy('created_at', 'desc')->paginate(15);
        return view('admin.otp_verifications.index', compact('otpVerifications'));
    }

    public function create()
    {
        return view('admin.otp_verifications.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email',
            'otp' => 'required|string|size:6',
            'expires_at' => 'required|date',
            'is_used' => 'boolean'
        ]);

        OtpVerification::create($validated);

        return redirect()->route('admin.otp_verifications.index')->with('success', 'OTP Verification created successfully.');
    }

    public function show(OtpVerification $otpVerification)
    {
        return view('admin.otp_verifications.show', compact('otpVerification'));
    }

    public function edit(OtpVerification $otpVerification)
    {
        return view('admin.otp_verifications.edit', compact('otpVerification'));
    }

    public function update(Request $request, OtpVerification $otpVerification)
    {
        $validated = $request->validate([
            'email' => 'required|email',
            'otp' => 'required|string|size:6',
            'expires_at' => 'required|date',
            'is_used' => 'boolean'
        ]);

        $otpVerification->update($validated);

        return redirect()->route('admin.otp_verifications.index')->with('success', 'OTP Verification updated successfully.');
    }

    public function destroy(OtpVerification $otpVerification)
    {
        $otpVerification->delete();

        return redirect()->route('admin.otp_verifications.index')->with('success', 'OTP Verification deleted successfully.');
    }
}
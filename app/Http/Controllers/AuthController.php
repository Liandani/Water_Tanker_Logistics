<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Customer;
use App\Models\Driver;
use App\Models\User;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function showLogin()
    {
        if (Auth::check()) {
            return $this->redirectBasedOnRole(Auth::user());
        }
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        $remember = $request->boolean('remember');

        if (Auth::attempt($credentials, $remember)) {
            $request->session()->regenerate();
            $user = Auth::user();

            if ($user->status === 'inactive') {
                Auth::logout();
                return back()->with('error', 'Akun Anda dinonaktifkan oleh administrator.');
            }

            ActivityLog::log('USER_LOGIN', "User {$user->name} ({$user->role}) berhasil login.", $user->id);

            return $this->redirectBasedOnRole($user);
        }

        return back()->withErrors([
            'email' => 'Email atau kata sandi yang Anda masukkan salah.',
        ])->onlyInput('email');
    }

    public function logout(Request $request)
    {
        if (Auth::check()) {
            ActivityLog::log('USER_LOGOUT', "User " . Auth::user()->name . " logout.", Auth::id());
        }

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success', 'Anda telah berhasil keluar.');
    }

    public function showCustomerRegister()
    {
        return view('auth.register-customer');
    }

    public function customerRegister(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'company_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'phone' => ['required', 'string', 'max:20'],
            'address' => ['required', 'string'],
            'business_category' => ['required', 'string'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'],
            'password' => Hash::make($validated['password']),
            'role' => 'customer',
            'status' => 'active',
        ]);

        Customer::create([
            'user_id' => $user->id,
            'company_name' => $validated['company_name'],
            'contact_person' => $validated['name'],
            'address' => $validated['address'],
            'latitude' => -6.200000, // default jakarta if not geocoded
            'longitude' => 106.816666,
            'business_category' => $validated['business_category'],
        ]);

        NotificationService::notifyAdmins(
            'Pelanggan Baru Mendaftar',
            "Pelanggan baru {$validated['company_name']} ({$validated['name']}) telah mendaftar.",
            route('admin.customers.index'),
            'info'
        );

        ActivityLog::log('CUSTOMER_REGISTER', "Pelanggan {$validated['company_name']} mendaftar.", $user->id);

        Auth::login($user);
        return redirect()->route('customer.dashboard')->with('success', 'Selamat datang! Akun pelanggan berhasil dibuat.');
    }

    public function showDriverRegister()
    {
        return view('auth.register-driver');
    }

    public function driverRegister(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'phone' => ['required', 'string', 'max:20'],
            'license_number' => ['nullable', 'string', 'max:50'],
            'identity_number' => ['nullable', 'string', 'max:50'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'],
            'password' => Hash::make($validated['password']),
            'role' => 'driver',
            'status' => 'pending', // Waiting for Admin verification
        ]);

        Driver::create([
            'user_id' => $user->id,
            'license_number' => $validated['license_number'] ?? null,
            'identity_number' => $validated['identity_number'] ?? null,
            'availability_status' => 'Libur',
            'verification_status' => 'pending',
        ]);

        NotificationService::notifyAdmins(
            'Registrasi Driver Baru',
            "Driver baru {$validated['name']} menunggu verifikasi Admin sebelum dapat bertugas.",
            route('admin.drivers.index'),
            'warning'
        );

        ActivityLog::log('DRIVER_REGISTER', "Driver baru {$validated['name']} mendaftar dan menunggu verifikasi.", $user->id);

        Auth::login($user);
        return redirect()->route('driver.pending');
    }

    public function showAdminRegister()
    {
        return view('auth.register-admin');
    }

    public function adminRegister(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'phone' => ['nullable', 'string', 'max:20'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'security_key' => ['required', 'string'],
        ]);

        // Security key check to prevent open admin registration
        if ($validated['security_key'] !== 'TIRTA-ADMIN-2026') {
            return back()->withErrors(['security_key' => 'Security Activation Key tidak valid. Hubungi Lead Administrator.'])->withInput();
        }

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'password' => Hash::make($validated['password']),
            'role' => 'admin',
            'status' => 'active',
        ]);

        ActivityLog::log('ADMIN_REGISTER', "Admin baru {$user->name} dibuat via activation key.", $user->id);

        Auth::login($user);
        return redirect()->route('admin.dashboard')->with('success', 'Akun Administrator berhasil diaktifkan.');
    }

    public function showForgotPassword()
    {
        return view('auth.forgot-password');
    }

    public function forgotPassword(Request $request)
    {
        $request->validate(['email' => ['required', 'email']]);
        return back()->with('status', 'Instruksi reset kata sandi telah dikirimkan ke email Anda jika terdaftar.');
    }

    public function driverPending()
    {
        $user = Auth::user();
        if ($user && $user->role === 'driver' && $user->driver && $user->driver->verification_status === 'verified') {
            return redirect()->route('driver.dashboard');
        }
        return view('driver.pending');
    }

    protected function redirectBasedOnRole(User $user)
    {
        return match ($user->role) {
            'admin' => redirect()->route('admin.dashboard'),
            'driver' => ($user->driver && $user->driver->verification_status === 'pending')
                ? redirect()->route('driver.pending')
                : redirect()->route('driver.dashboard'),
            'customer' => redirect()->route('customer.dashboard'),
            default => redirect('/'),
        };
    }
}


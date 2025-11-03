<?php

use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TaskController;
use App\Models\User;
use App\Models\Task;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

Route::get('/', function () {
    return view('welcome');
});

// Dashboard redirect based on role
Route::get('/dashboard', function () {
    if (Auth::check() && Auth::user()->is_admin) {
        return view('admin.dashboard');
    }
    return view('user.dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// Authentication-required routes
Route::middleware('auth')->group(function () {
    // Profile routes
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/password', [ProfileController::class, 'updatePassword'])->name('password.update');

    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::get('/register', [RegisteredUserController::class, 'create'])->name('register');

    // Task routes for all authenticated users
    Route::resource('tasks', TaskController::class);

    /**
     * ===============================
     *  ADMIN-ONLY ROUTES (Manual Check)
     * ===============================
     */
    Route::prefix('admin')->name('admin.')->group(function () {

        // View all users
        Route::get('/users', function () {
            if (!Auth::check() || !Auth::user()->is_admin) {
                abort(403, 'Admin access required.');
            }

            $users = User::withCount('tasks')->get();
            return view('admin.users', compact('users'));
        })->name('users');

        // Update user details (including role)
        Route::put('/users/{user}', function (User $user, Request $request) {
            if (!Auth::check() || !Auth::user()->is_admin) {
                abort(403, 'Admin access required.');
            }

            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email,' . $user->id,
                'is_admin' => 'required|boolean',
            ]);

            $user->update($validated);

            return redirect()->route('admin.users')->with('success', 'User updated successfully.');
        })->name('users.update');

        // Delete user (with confirmation)
        Route::delete('/users/{user}', function (User $user, Request $request) {
            if (!Auth::check() || !Auth::user()->is_admin) {
                abort(403, 'Admin access required.');
            }

            // Prevent deleting yourself
            if ($user->id === Auth::id()) {
                return redirect()->route('admin.users')->with('error', 'You cannot delete your own account.');
            }

            // Validate input
            $request->validate([
                'confirm_delete' => 'required|string',
            ]);

            // Compare safely (case-sensitive match)
            if (trim($request->confirm_delete) !== $user->name) {
                return redirect()->route('admin.users')->with('error', 'Deletion confirmation failed. Please type the name exactly.');
            }

            // Delete the user safely
            $userName = e($user->name); // Escape HTML
            $user->delete();

            return redirect()->route('admin.users')->with('success', "User '{$userName}' has been deleted successfully.");
        })->name('users.destroy');


        // Admin task management overview
        Route::get('/tasks', function (Request $request) {
            if (!Auth::check() || !Auth::user()->is_admin) {
                abort(403, 'Admin access required.');
            }

            $query = Task::with('user');

            if ($request->has('search') && $request->search != '') {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('title', 'like', "%{$search}%")
                        ->orWhere('description', 'like', "%{$search}%")
                        ->orWhereHas('user', function ($q) use ($search) {
                            $q->where('name', 'like', "%{$search}%");
                        });
                });
            }

            $tasks = $query->latest()->paginate(10);
            return view('admin.tasks', compact('tasks'));
        })->name('tasks');
    });
});

require __DIR__ . '/auth.php';

@extends('layouts.app')

@section('title', 'Profile Settings')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-4">
            <!-- Profile Summary Card -->
            <div class="card card-primary card-outline">
                <div class="card-body box-profile">
                    <div class="text-center">
                        <div class="profile-user-img img-circle bg-primary d-flex align-items-center justify-content-center mx-auto" 
                             style="width: 100px; height: 100px; border-radius: 50%; font-size: 2.5rem;">
                            <i class="fas fa-user text-white"></i>
                        </div>
                    </div>

                    <h3 class="profile-username text-center">{{ $user->name }}</h3>
                    <p class="text-muted text-center">{{ $user->email }}</p>

                    <ul class="list-group list-group-unbordered mb-3">
                        <li class="list-group-item">
                            <b>Role</b> 
                            <span class="float-right">
                                <span class="badge badge-{{ $user->is_admin ? 'success' : 'primary' }}">
                                    {{ $user->is_admin ? 'Administrator' : 'User' }}
                                </span>
                            </span>
                        </li>
                        <li class="list-group-item">
                            <b>Registered</b> 
                            <span class="float-right">{{ $user->created_at->format('M d, Y') }}</span>
                        </li>
                        <li class="list-group-item">
                            <b>Tasks</b> 
                            <span class="float-right">
                                <span class="badge badge-info">{{ $user->tasks()->count() }}</span>
                            </span>
                        </li>
                        @if($user->is_admin)
                        <li class="list-group-item">
                            <b>System Users</b> 
                            <span class="float-right">
                                <span class="badge badge-success">{{ App\Models\User::count() }}</span>
                            </span>
                        </li>
                        <li class="list-group-item">
                            <b>Total Tasks</b> 
                            <span class="float-right">
                                <span class="badge badge-warning">{{ App\Models\Task::count() }}</span>
                            </span>
                        </li>
                        @endif
                    </ul>
                </div>
            </div>

            <!-- Quick Actions Card -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Quick Actions</h3>
                </div>
                <div class="card-body">
                    <a href="{{ route('dashboard') }}" class="btn btn-primary btn-block mb-2">
                        <i class="fas fa-tachometer-alt mr-2"></i> Dashboard
                    </a>
                    <a href="{{ route('tasks.index') }}" class="btn btn-info btn-block mb-2">
                        <i class="fas fa-tasks mr-2"></i> My Tasks
                    </a>
                    @if($user->is_admin)
                    <a href="{{ route('admin.users') }}" class="btn btn-success btn-block mb-2">
                        <i class="fas fa-users mr-2"></i> Manage Users
                    </a>
                    <a href="{{ route('admin.tasks') }}" class="btn btn-warning btn-block">
                        <i class="fas fa-list-alt mr-2"></i> All Tasks
                    </a>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-md-8">
            <!-- Profile Information Update Card -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Profile Information</h3>
                </div>
                <div class="card-body">
                    @if (session('status') === 'profile-updated')
                        <div class="alert alert-success alert-dismissible">
                            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                            <i class="icon fas fa-check"></i> Profile information updated successfully!
                        </div>
                    @endif

                    <form method="POST" action="{{ route('profile.update') }}">
                        @csrf
                        @method('patch')

                        <div class="form-group">
                            <label for="name">Full Name</label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                   id="name" name="name" value="{{ old('name', $user->name) }}" 
                                   placeholder="Enter your full name" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="email">Email Address</label>
                            <input type="email" class="form-control @error('email') is-invalid @enderror" 
                                   id="email" name="email" value="{{ old('email', $user->email) }}" 
                                   placeholder="Enter your email address" required>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group mb-0">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save mr-2"></i> Save Changes
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Password Update Card -->
            <div class="card mt-4">
                <div class="card-header">
                    <h3 class="card-title">Update Password</h3>
                </div>
                <div class="card-body">
                    @if (session('status') === 'password-updated')
                        <div class="alert alert-success alert-dismissible">
                            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                            <i class="icon fas fa-check"></i> Password updated successfully!
                        </div>
                    @endif

                    <form method="POST" action="{{ route('password.update') }}">
                        @csrf
                        @method('put')

                        <div class="form-group">
                            <label for="current_password">Current Password</label>
                            <input type="password" class="form-control @error('current_password') is-invalid @enderror" 
                                   id="current_password" name="current_password" 
                                   placeholder="Enter your current password" required>
                            @error('current_password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="password">New Password</label>
                            <input type="password" class="form-control @error('password') is-invalid @enderror" 
                                   id="password" name="password" 
                                   placeholder="Enter new password" required>
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="password_confirmation">Confirm New Password</label>
                            <input type="password" class="form-control" 
                                   id="password_confirmation" name="password_confirmation" 
                                   placeholder="Confirm new password" required>
                        </div>

                        <div class="form-group mb-0">
                            <button type="submit" class="btn btn-warning">
                                <i class="fas fa-key mr-2"></i> Update Password
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Account Deletion Card -->
            <div class="card card-danger mt-4">
                <div class="card-header">
                    <h3 class="card-title">Delete Account</h3>
                </div>
                <div class="card-body">
                    <div class="alert alert-warning">
                        <h6><i class="icon fas fa-exclamation-triangle"></i> Warning!</h6>
                        Once your account is deleted, all of your resources and data will be permanently erased. 
                        This action cannot be undone.
                    </div>

                    <!-- Button trigger modal -->
                    <button type="button" class="btn btn-danger" data-toggle="modal" data-target="#deleteAccountModal">
                        <i class="fas fa-trash mr-2"></i> Delete Account
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Delete Account Modal -->
<div class="modal fade" id="deleteAccountModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title text-danger">Delete Your Account</h5>
                <button type="button" class="close" data-dismiss="modal">×</button>
            </div>
            <div class="modal-body">
                <div class="alert alert-danger">
                    <h6><i class="fas fa-exclamation-triangle"></i> This action is permanent!</h6>
                    <p class="mb-0">All your tasks and data will be permanently removed from the system.</p>
                </div>
                
                <p>Please enter your password to confirm you want to permanently delete your account.</p>

                <form method="POST" action="{{ route('profile.destroy') }}" id="deleteAccountForm">
                    @csrf
                    @method('delete')
                    
                    <div class="form-group">
                        <label for="password">Password</label>
                        <input type="password" class="form-control" id="password" name="password" 
                               placeholder="Enter your password" required>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                <button type="submit" form="deleteAccountForm" class="btn btn-danger">
                    <i class="fas fa-trash mr-2"></i> Delete Account
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        // Auto-focus on first input when modal opens
        $('#deleteAccountModal').on('shown.bs.modal', function() {
            $(this).find('input[type="password"]').focus();
        });

        // Reset form when modal closes
        $('#deleteAccountModal').on('hidden.bs.modal', function() {
            $(this).find('form')[0].reset();
        });
    });
</script>
@endsection
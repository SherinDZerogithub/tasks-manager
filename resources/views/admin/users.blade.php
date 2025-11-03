@extends('layouts.app')

@section('title', 'User Management')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">User Management</h3>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Role</th>
                                    <th>Tasks Count</th>
                                    <th>Registered</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach(App\Models\User::withCount('tasks')->get() as $user)
                                <tr>
                                    <td>{{ $user->name }}</td>
                                    <td>{{ $user->email }}</td>
                                    <td>
                                        <span class="badge badge-{{ $user->is_admin ? 'success' : 'primary' }}">
                                            {{ $user->is_admin ? 'Administrator' : 'User' }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge badge-info">{{ $user->tasks_count }}</span>
                                    </td>
                                    <td>{{ $user->created_at->format('M d, Y') }}</td>
                                    <td>
                                        <!-- View Button -->
                                        <button class="btn btn-info btn-sm" data-toggle="modal" data-target="#userModal{{ $user->id }}">
                                            <i class="fas fa-eye"></i>
                                        </button>

                                        <!-- Delete Button -->
                                        <button class="btn btn-danger btn-sm" data-toggle="modal" data-target="#deleteUserModal{{ $user->id }}">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>

                                <!-- View User Modal -->
                                <div class="modal fade" id="userModal{{ $user->id }}" tabindex="-1">
                                    <div class="modal-dialog modal-dialog-centered">
                                        <div class="modal-content">
                                            <div class="modal-header bg-info text-white">
                                                <h5 class="modal-title">User Details</h5>
                                                <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
                                            </div>
                                            <div class="modal-body">
                                                <ul class="list-group">
                                                    <li class="list-group-item"><strong>Name:</strong> {{ $user->name }}</li>
                                                    <li class="list-group-item"><strong>Email:</strong> {{ $user->email }}</li>
                                                    <li class="list-group-item"><strong>Role:</strong> {{ $user->is_admin ? 'Administrator' : 'User' }}</li>
                                                    <li class="list-group-item"><strong>Total Tasks:</strong> {{ $user->tasks_count }}</li>
                                                    <li class="list-group-item"><strong>Completed Tasks:</strong> {{ $user->tasks()->where('status', 'completed')->count() }}</li>
                                                    <li class="list-group-item"><strong>Pending Tasks:</strong> {{ $user->tasks()->where('status', 'pending')->count() }}</li>
                                                    <li class="list-group-item"><strong>In Progress:</strong> {{ $user->tasks()->where('status', 'in_progress')->count() }}</li>
                                                    <li class="list-group-item"><strong>Registered:</strong> {{ $user->created_at->format('M d, Y g:i A') }}</li>
                                                    <li class="list-group-item"><strong>Last Updated:</strong> {{ $user->updated_at->format('M d, Y g:i A') }}</li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Delete User Modal -->
                                <div class="modal fade" id="deleteUserModal{{ $user->id }}" tabindex="-1">
                                    <div class="modal-dialog modal-dialog-centered">
                                        <div class="modal-content">
                                            <form action="{{ route('admin.users.destroy', $user) }}" method="POST">
    @csrf
    @method('DELETE')
    <div class="modal-header bg-danger text-white">
        <h5 class="modal-title">Confirm Deletion</h5>
        <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
    </div>
    <div class="modal-body">
        <p>Are you sure you want to delete <strong>{{ $user->name }}</strong> ({{ $user->email }})?</p>
        <p class="text-danger mb-2"><strong>Warning:</strong> This will permanently remove the user and their tasks.</p>
        <div class="form-group">
            <label for="confirm_delete{{ $user->id }}">
                Type <strong>{{ $user->name }}</strong> to confirm:
            </label>
            <input 
                type="text" 
                name="confirm_delete" 
                id="confirm_delete{{ $user->id }}" 
                class="form-control" 
                placeholder="Enter {{ $user->name }}" 
                required>
        </div>
    </div>
    <div class="modal-footer">
        <button type="submit" class="btn btn-danger">Delete User</button>
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
    </div>
</form>

                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

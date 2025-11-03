@extends('layouts.app')

@section('title', 'Tasks Management')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Tasks</h3>
                    <div class="card-tools">
                        <a href="{{ route('tasks.create') }}" class="btn btn-success btn-sm">
                            <i class="fas fa-plus"></i> Create New Task
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Search Form -->
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <form action="{{ route('tasks.index') }}" method="GET">
                                <div class="input-group">
                                    <input type="text" name="search" class="form-control" placeholder="Search tasks..." value="{{ request('search') }}">
                                    <div class="input-group-append">
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-search"></i>
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead>
                                <tr>
                                    <th>Title</th>
                                    <th>Status</th>
                                    <th>Due Date</th>
                                    @if(auth()->user()->is_admin)
                                    <th>Assigned To</th>
                                    @endif
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($tasks as $task)
                                <tr>
                                    <td>{{ $task->title }}</td>
                                    <td>
                                        <span class="badge badge-@if($task->status == 'completed')success
                                            @elseif($task->status == 'in_progress')warning
                                            @elseif($task->status == 'pending')secondary @endif">
                                            {{ ucfirst(str_replace('_', ' ', $task->status)) }}
                                        </span>
                                    </td>
                                    <td>
                                        @if($task->due_date)
                                            @if($task->due_date->isPast() && $task->status != 'completed')
                                                <span class="text-danger">{{ $task->due_date->format('M d, Y') }}</span>
                                            @else
                                                {{ $task->due_date->format('M d, Y') }}
                                            @endif
                                        @else
                                            <span class="text-muted">Not set</span>
                                        @endif
                                    </td>
                                    @if(auth()->user()->is_admin)
                                    <td>{{ $task->user->name }}</td>
                                    @endif
                                    <td>
                                        <a href="{{ route('tasks.show', $task) }}" class="btn btn-info btn-sm" title="View">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('tasks.edit', $task) }}" class="btn btn-primary btn-sm" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('tasks.destroy', $task) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm" title="Delete" onclick="return confirm('Are you sure you want to delete this task?')">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="{{ auth()->user()->is_admin ? 5 : 4 }}" class="text-center text-muted">
                                        No tasks found. <a href="{{ route('tasks.create') }}">Create your first task!</a>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    
                   @if($tasks->hasPages())
    <div class="d-flex justify-content-between align-items-center mt-3">
        <div class="text-muted">
            Showing {{ $tasks->firstItem() ?? 0 }} to {{ $tasks->lastItem() ?? 0 }} of {{ $tasks->total() }} entries
        </div>
        <div>
            {{ $tasks->links('pagination::bootstrap-4') }}
        </div>
    </div>
@endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
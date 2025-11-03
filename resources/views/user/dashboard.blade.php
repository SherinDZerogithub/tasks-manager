@extends('layouts.app')

@section('title', 'My Dashboard')

@section('content')
<div class="container-fluid">
    <div class="row">
        
        <!-- Completed Tasks -->
        <div class="col-lg-3 col-6">
            <div class="small-box bg-success">
                <div class="inner">
                    <h3>{{ auth()->user()->tasks()->where('status', 'completed')->count() }}</h3>
                    <p>Completed Tasks</p>
                </div>
                <div class="icon">
                    <i class="fas fa-check-circle"></i>
                </div>
                <a href="{{ route('tasks.index', ['status' => 'completed']) }}" class="small-box-footer">
                    View Completed <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>

        <!-- In Progress Tasks -->
        <div class="col-lg-3 col-6">
            <div class="small-box bg-warning">
                <div class="inner">
                    <h3>{{ auth()->user()->tasks()->where('status', 'in_progress')->count() }}</h3>
                    <p>In Progress</p>
                </div>
                <div class="icon">
                    <i class="fas fa-spinner"></i>
                </div>
                <a href="{{ route('tasks.index', ['status' => 'in_progress']) }}" class="small-box-footer">
                    View In Progress <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>

        <!-- Pending Tasks -->
        <div class="col-lg-3 col-6">
            <div class="small-box bg-secondary">
                <div class="inner">
                    <h3>{{ auth()->user()->tasks()->where('status', 'pending')->count() }}</h3>
                    <p>Pending Tasks</p>
                </div>
                <div class="icon">
                    <i class="fas fa-clock"></i>
                </div>
                <a href="{{ route('tasks.index', ['status' => 'pending']) }}" class="small-box-footer">
                    View Pending <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>

        <!-- Overdue Tasks -->
        <div class="col-lg-3 col-6">
            <div class="small-box bg-danger">
                <div class="inner">
                    <h3>{{ auth()->user()->tasks()->where('due_date', '<', now())->where('status', '!=', 'completed')->count() }}</h3>
                    <p>Overdue Tasks</p>
                </div>
                <div class="icon">
                    <i class="fas fa-exclamation-circle"></i>
                </div>
                <a href="{{ route('tasks.index', ['status' => 'overdue']) }}" class="small-box-footer">
                    View Overdue <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>
    </div>

    <!-- Recent Tasks -->
   <!-- Recent Tasks -->
<!-- Recent Tasks -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h3 class="card-title mb-0">Recent Tasks</h3>
                <!-- Create Task Button -->
                <a href="{{ route('tasks.create') }}" class="btn btn-success">
                    <i class="fas fa-plus"></i> Create Task
                </a>
            </div>
            <div class="card-body">
                @php
                    $recentTasks = auth()->user()->tasks()->latest()->take(5)->get();
                @endphp
                @if($recentTasks->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead class="thead-light">
                                <tr>
                                    <th>Title</th>
                                    <th>Status</th>
                                    <th>Due Date</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recentTasks as $task)
                                <tr>
                                    <td class="font-weight-bold">{{ Str::limit($task->title, 30) }}</td>
                                    <td>
                                        <span class="badge badge-@if($task->status == 'completed')success
                                            @elseif($task->status == 'in_progress')warning
                                            @elseif($task->status == 'pending')secondary @endif">
                                            {{ ucfirst(str_replace('_', ' ', $task->status)) }}
                                        </span>
                                    </td>
                                    <td>
                                        @if($task->due_date)
                                            <span class="@if($task->due_date->isPast() && $task->status != 'completed') text-danger font-weight-bold @endif">
                                                {{ $task->due_date->format('M d, Y') }}
                                            </span>
                                        @else
                                            <span class="text-muted">Not set</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <a href="{{ route('tasks.show', $task) }}" class="btn btn-info" title="View">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('tasks.edit', $task) }}" class="btn btn-primary" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-4">
                        <i class="fas fa-tasks fa-3x text-muted mb-3"></i>
                        <p class="text-muted">No tasks found.</p>
                        <a href="{{ route('tasks.create') }}" class="btn btn-success">
                            <i class="fas fa-plus"></i> Create Your First Task
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

</div>
@endsection

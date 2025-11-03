@extends('layouts.app')

@section('title', 'Admin Dashboard')

@section('content')
<div class="container-fluid">
    <!-- Statistics Boxes -->
    <div class="row">

        <div class="col-lg-3 col-6">
            <div class="small-box bg-success">
                <div class="inner">
                    <h3>{{ App\Models\User::count() }}</h3>
                    <p>Total Users</p>
                </div>
                <div class="icon">
                    <i class="fas fa-users"></i>
                </div>
                <a href="{{ route('admin.users') }}" class="small-box-footer">Manage Users <i class="fas fa-arrow-circle-right"></i></a>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="small-box bg-warning">
                <div class="inner">
                    <h3>{{ App\Models\Task::where('status', 'completed')->count() }}</h3>
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
        <div class="col-lg-3 col-6">
            <div class="small-box bg-danger">
                <div class="inner">
                    <h3>{{ \App\Models\Task::where('due_date', '<', now())->where('status', '!=', 'completed')->count() }}</h3>
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
                <div class="col-lg-3 col-6">
            <div class="small-box bg-info">
                <div class="inner">
                    <h3>{{ App\Models\Task::where('status', 'in_progress')->count() }}</h3>
                    <p>In Progress Tasks</p>
                </div>
                <div class="icon">
                    <i class="fas fa-spinner"></i>
                </div>
                <a href="{{ route('tasks.index', ['status' => 'in_progress']) }}" class="small-box-footer">
                    View In Progress <i class="fas fa-arrow-circle-right"></i>
                </a> </div>
        </div>
    </div>

    <!-- Recent Tasks Section -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title mb-0">Recent Tasks</h3>
                    <a href="{{ route('tasks.create') }}" class="btn btn-success">
                        <i class="fas fa-plus"></i> Create Task
                    </a>
                </div>
                <div class="card-body">
                    @php
                        $recentTasks = App\Models\Task::with('user')->latest()->take(10)->get();
                    @endphp
                    @if($recentTasks->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover">
                                <thead class="thead-light">
                                    <tr>
                                        <th>Title</th>
                                        <th>Assigned To</th>
                                        <th>Status</th>
                                        <th>Due Date</th>
                                        <th>Created</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($recentTasks as $task)
                                    <tr>
                                        <td class="font-weight-bold">{{ Str::limit($task->title, 30) }}</td>
                                        <td>
                                            <span class="badge badge-light">{{ $task->user->name }}</span>
                                        </td>
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
                                            <span class="text-muted">{{ $task->created_at->diffForHumans() }}</span>
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
                        <div class="text-center mt-3">
                            <a href="{{ route('admin.tasks') }}" class="btn btn-outline-primary">
                                View All Tasks <i class="fas fa-arrow-right ml-1"></i>
                            </a>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-tasks fa-3x text-muted mb-3"></i>
                            <p class="text-muted">No tasks found in the system.</p>
                            <a href="{{ route('tasks.create') }}" class="btn btn-success">
                                <i class="fas fa-plus"></i> Create First Task
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

  
</div>

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Simple task status chart
        const ctx = document.getElementById('taskStatusChart').getContext('2d');
        const taskStatusChart = new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: ['Pending', 'In Progress', 'Completed'],
                datasets: [{
                    data: [
                        {{ App\Models\Task::where('status', 'pending')->count() }},
                        {{ App\Models\Task::where('status', 'in_progress')->count() }},
                        {{ App\Models\Task::where('status', 'completed')->count() }}
                    ],
                    backgroundColor: [
                        '#6c757d',
                        '#ffc107',
                        '#28a745'
                    ],
                    borderWidth: 2,
                    borderColor: '#fff'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            padding: 20,
                            usePointStyle: true
                        }
                    }
                }
            }
        });
    });
</script>
@endsection
@endsection
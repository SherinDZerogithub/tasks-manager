@extends('layouts.app')

@section('title', 'View Task')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Task Details</h3>
                    <div class="card-tools">
                        <a href="{{ route('tasks.edit', $task) }}" class="btn btn-primary btn-sm">
                            <i class="fas fa-edit"></i> Edit
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h4>{{ $task->title }}</h4>
                            <p class="text-muted">{{ $task->description ?? 'No description provided.' }}</p>
                        </div>
                        <div class="col-md-6">
                            <div class="info-box">
                                <span class="info-box-icon bg-@if($task->status == 'completed')success
                                    @elseif($task->status == 'in_progress')warning
                                    @elseif($task->status == 'pending')secondary @endif">
                                    <i class="fas fa-tasks"></i>
                                </span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Status</span>
                                    <span class="info-box-number">
                                        {{ ucfirst(str_replace('_', ' ', $task->status)) }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-3">
                        <div class="col-md-6">
                            <table class="table table-bordered">
                                <tr>
                                    <th width="30%">Due Date</th>
                                    <td>
                                        @if($task->due_date)
                                            @if($task->due_date->isPast() && $task->status != 'completed')
                                                <span class="text-danger"><strong>{{ $task->due_date->format('M d, Y') }}</strong> (Overdue)</span>
                                            @else
                                                {{ $task->due_date->format('M d, Y') }}
                                            @endif
                                        @else
                                            <span class="text-muted">Not set</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>Assigned To</th>
                                    <td>{{ $task->user->name }}</td>
                                </tr>
                                <tr>
                                    <th>Created</th>
                                    <td>{{ $task->created_at->format('M d, Y g:i A') }}</td>
                                </tr>
                                <tr>
                                    <th>Last Updated</th>
                                    <td>{{ $task->updated_at->format('M d, Y g:i A') }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <a href="{{ route('tasks.index') }}" class="btn btn-default">
                        <i class="fas fa-arrow-left"></i> Back to Tasks
                    </a>
                    @can('delete', $task)
                    <form action="{{ route('tasks.destroy', $task) }}" method="POST" class="d-inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this task?')">
                            <i class="fas fa-trash"></i> Delete Task
                        </button>
                    </form>
                    @endcan
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
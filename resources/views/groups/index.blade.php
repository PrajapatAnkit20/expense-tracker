<head>
    <link rel="stylesheet" href="{{ asset('css/groupindex.css') }}">
</head>
@extends('layouts.app')

@section('title', 'All Groups')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col">
                <!-- Added Group Expenses header -->
                <div class="header-section">
                    <h2 class="main-title">Group Expenses</h2>
                    <p class="subtitle">Split expenses with friends and family</p>
                </div>

                <div class="page-header">
                    <h1 class="page-title">
                        <i class="fas fa-users icon-space"></i>
                        All Groups
                    </h1>
                    <a href="{{ route('groups.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus icon-space"></i>
                        Create New Group
                    </a>
                </div>
            </div>
        </div>

        @if ($groups->isEmpty())
            <div class="row">
                <div class="col">
                    <div class="empty-state">
                        <i class="fas fa-users empty-icon"></i>
                        <h3 class="empty-title">No Groups Yet</h3>
                        <p class="empty-text">Create your first group to start managing expenses with friends!</p>
                        <a href="{{ route('groups.create') }}" class="btn btn-primary btn-large">
                            <i class="fas fa-plus icon-space"></i>
                            Create Your First Group
                        </a>
                    </div>
                </div>
            </div>
        @else
            <div class="groups-grid">
                @foreach ($groups as $group)
                    <div class="group-card">
                        <div class="card-body">
                            <div class="group-header">
                                @if ($group->image)
                                    <img src="{{ asset('storage/' . $group->image) }}" alt="{{ $group->name }}"
                                        class="group-avatar">
                                @else
                                    <div class="group-avatar-placeholder">
                                        <i class="fas fa-users"></i>
                                    </div>
                                @endif
                                <div class="group-info">
                                    <h5 class="group-name">{{ $group->name }}</h5>
                                    <small class="member-count">{{ $group->member_count }} members</small>
                                </div>
                            </div>

                            @if ($group->description)
                                <p class="group-description">{{ Str::limit($group->description, 100) }}</p>
                            @endif

                            <div class="stats-section">
                                <div class="stat-item">
                                    <h6 class="stat-value primary">₹{{ number_format($group->total_expenses, 2) }}</h6>
                                    <small class="stat-label">Total Expenses</small>
                                </div>
                                <div class="stat-item">
                                    <h6 class="stat-value success">{{ $group->expenses->count() }}</h6>
                                    <small class="stat-label">Total Entries</small>
                                </div>
                            </div>

                            <div class="card-footer">
                                <small class="created-date">
                                    <i class="fas fa-calendar icon-space"></i>
                                    {{ $group->created_at->format('M d, Y') }}
                                </small>
                                <div class="action-buttons">
                                    <a href="{{ route('groups.show', $group) }}" class="btn btn-primary btn-small">
                                        <i class="fas fa-eye icon-space"></i>
                                        View
                                    </a>
                                    <div class="dropdown">
                                        <button type="button" class="btn btn-secondary btn-small dropdown-toggle"
                                            data-bs-toggle="dropdown">
                                            <span class="visually-hidden">Toggle Dropdown</span>
                                        </button>
                                        <ul class="dropdown-menu">
                                            <li><a class="dropdown-item" href="{{ route('groups.edit', $group) }}">
                                                    <i class="fas fa-edit icon-space"></i>Edit
                                                </a></li>
                                            <li><a class="dropdown-item" href="{{ route('groups.export', $group) }}">
                                                    <i class="fas fa-download icon-space"></i>Export
                                                </a></li>
                                            <li>
                                                <hr class="dropdown-divider">
                                            </li>
                                            <li>
                                                <form action="{{ route('groups.destroy', $group) }}" method="POST"
                                                    class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="dropdown-item danger"
                                                        onclick="return confirm('Are you sure you want to delete this group?')">
                                                        <i class="fas fa-trash icon-space"></i>Delete
                                                    </button>
                                                </form>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
@endsection

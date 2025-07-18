@extends('layouts.app')

@section('title', $group->name)

@section('content')
    <div class="container-fluid">
        <!-- Group Header -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-md-8">
                                <div class="d-flex align-items-center">
                                    @if ($group->image)
                                        <img src="{{ asset('storage/' . $group->image) }}" alt="{{ $group->name }}"
                                            class="rounded-circle me-3" width="60" height="60">
                                    @else
                                        <div class="bg-primary rounded-circle d-flex align-items-center justify-content-center me-3"
                                            style="width: 60px; height: 60px;">
                                            <i class="fas fa-users text-white fa-lg"></i>
                                        </div>
                                    @endif
                                    <div>
                                        <h2 class="mb-1">{{ $group->name }}</h2>
                                        <p class="text-muted mb-0">{{ $group->description }}</p>
                                        <small class="text-muted">Created on
                                            {{ $group->created_at->format('M d, Y') }}</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4 text-end">
                                <div class="btn-group">
                                    <button type="button" class="btn btn-primary" data-bs-toggle="modal"
                                        data-bs-target="#addExpenseModal">
                                        <i class="fas fa-plus me-2"></i>
                                        Add Expense
                                    </button>
                                    <button type="button"
                                        class="btn btn-outline-primary dropdown-toggle dropdown-toggle-split"
                                        data-bs-toggle="dropdown">
                                        <span class="visually-hidden">Toggle Dropdown</span>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end">
                                        <li><a class="dropdown-item" href="{{ route('groups.edit', $group) }}">
                                                <i class="fas fa-edit me-2"></i>Edit Group
                                            </a></li>
                                        <li><a class="dropdown-item" data-bs-toggle="modal"
                                                data-bs-target="#addMemberModal">
                                                <i class="fas fa-user-plus me-2"></i>Add Member
                                            </a></li>
                                        {{-- <li><a class="dropdown-item" href="{{ route('groups.statistics', $group) }}"> --}}
                                                {{-- <i class="fas fa-chart-bar me-2"></i>Statistics --}}
                                            {{-- </a></li> --}}
                                        <li><a class="dropdown-item" href="{{ route('groups.export', $group) }}">
                                                <i class="fas fa-download me-2"></i>Export Data
                                            </a></li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Stats Overview -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card text-center">
                    <div class="card-body">
                        <i class="fas fa-rupee-sign fa-2x text-primary mb-2"></i>
                        <h4 class="mb-1">₹{{ number_format($totalExpenses, 2) }}</h4>
                        <small class="text-muted">Total Expenses</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-center">
                    <div class="card-body">
                        <i class="fas fa-users fa-2x text-success mb-2"></i>
                        <h4 class="mb-1">{{ $memberCount }}</h4>
                        <small class="text-muted">Members</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-center">
                    <div class="card-body">
                        <i class="fas fa-receipt fa-2x text-info mb-2"></i>
                        <h4 class="mb-1">{{ $recentExpenses->count() }}</h4>
                        <small class="text-muted">Total Entries</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-center">
                    <div class="card-body">
                        <i class="fas fa-calculator fa-2x text-warning mb-2"></i>
                        <h4 class="mb-1">
                            ₹{{ $memberCount > 0 ? number_format($totalExpenses / $memberCount, 2) : '0.00' }}</h4>
                        <small class="text-muted">Per Member</small>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Recent Expenses -->
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">
                            <i class="fas fa-receipt me-2"></i>
                            Recent Expenses
                        </h5>
                        <button class="btn btn-outline-primary btn-sm" data-bs-toggle="modal"
                            data-bs-target="#addExpenseModal">
                            <i class="fas fa-plus me-1"></i>
                            Add Expense
                        </button>
                    </div>
                    <div class="card-body">
                        @if ($recentExpenses->isEmpty())
                            <div class="text-center py-4">
                                <i class="fas fa-receipt fa-3x text-muted mb-3"></i>
                                <h5 class="text-muted">No expenses yet</h5>
                                <p class="text-muted">Start by adding your first expense to the group!</p>
                            </div>
                        @else
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Date</th>
                                            <th>Description</th>
                                            <th>Category</th>
                                            <th>Paid By</th>
                                            <th>Amount</th>
                                            <th>Status</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($recentExpenses as $expense)
                                            <tr>
                                                <td>{{ $expense->expense_date->format('M d') }}</td>
                                                <td>
                                                    <strong>{{ $expense->title }}</strong>
                                                    @if ($expense->description)
                                                        <br><small
                                                            class="text-muted">{{ Str::limit($expense->description, 50) }}</small>
                                                    @endif
                                                </td>
                                                <td>
                                                    <span class="category-icon"
                                                        style="background-color: {{ $expense->category_color }};">
                                                        <i class="{{ $expense->category_icon }}"></i>
                                                    </span>
                                                    <small class="ms-2">{{ $expense->category }}</small>
                                                </td>
                                                <td>{{ $expense->paidBy->name }}</td>
                                                <td><strong>{{ $expense->formatted_amount }}</strong></td>
                                                <td>
                                                    @if ($expense->isSettled())
                                                        <span class="badge bg-success">Settled</span>
                                                    @else
                                                        <span class="badge bg-warning">Pending</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <div class="btn-group">
                                                        <a href="{{ route('groups.show-expense', [$group, $expense]) }}"
                                                            class="btn btn-sm btn-outline-primary">
                                                            <i class="fas fa-eye"></i>
                                                        </a>
                                                        <form
                                                            action="{{ route('groups.delete-expense', [$group, $expense]) }}"
                                                            method="POST" class="d-inline">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-sm btn-outline-danger"
                                                                onclick="return confirm('Are you sure?')">
                                                                <i class="fas fa-trash"></i>
                                                            </button>
                                                        </form>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Members & Balances -->
            <div class="col-lg-4">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">
                            <i class="fas fa-users me-2"></i>
                            Members & Balances
                        </h5>
                        <button class="btn btn-outline-primary btn-sm" data-bs-toggle="modal"
                            data-bs-target="#addMemberModal">
                            <i class="fas fa-user-plus me-1"></i>
                            Add Member
                        </button>
                    </div>
                    <div class="card-body">
                        @foreach ($balances as $balance)
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <div class="d-flex align-items-center">
                                    <img src="{{ $balance['member']->avatar }}" alt="{{ $balance['member']->name }}"
                                        class="rounded-circle me-3" width="40" height="40">
                                    <div>
                                        <h6 class="mb-0">{{ $balance['member']->name }}</h6>
                                        <small class="text-muted">{{ $balance['member']->role }}</small>
                                    </div>
                                </div>
                                <div class="text-end">
                                    <div
                                        class="
                                    @if ($balance['balance'] > 0) balance-positive
                                    @elseif($balance['balance'] < 0) balance-negative
                                    @else balance-zero @endif
                                ">
                                        <strong>₹{{ number_format($balance['balance'], 2) }}</strong>
                                    </div>
                                    <small class="text-muted">
                                        Paid: ₹{{ number_format($balance['paid'], 2) }} |
                                        Owes: ₹{{ number_format($balance['owes'], 2) }}
                                    </small>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Expense Modal -->
    <div class="modal fade" id="addExpenseModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add New Expense</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="{{ route('groups.store-expense', $group) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="title" class="form-label">Title *</label>
                                <input type="text" class="form-control" id="title" name="title" required>
                            </div>
                            <div class="col-md-6">
                                <label for="amount" class="form-label">Amount *</label>
                                <input type="number" class="form-control" id="amount" name="amount" step="0.01"
                                    min="0.01" required>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="category" class="form-label">Category *</label>
                                <select class="form-select" id="category" name="category" required>
                                    @foreach ($categories as $category)
                                        <option value="{{ $category }}">{{ $category }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="paid_by" class="form-label">Paid By *</label>
                                <select class="form-select" id="paid_by" name="paid_by" required>
                                    @foreach ($group->activeMembers as $member)
                                        <option value="{{ $member->id }}">{{ $member->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="expense_date" class="form-label">Date *</label>
                                <input type="date" class="form-control" id="expense_date" name="expense_date"
                                    value="{{ date('Y-m-d') }}" required>
                            </div>
                            <div class="col-md-6">
                                <label for="payment_method" class="form-label">Payment Method</label>
                                <select class="form-select" id="payment_method" name="payment_method">
                                    <option value="">Select Method</option>
                                    <option value="Cash">Cash</option>
                                    <option value="Card">Card</option>
                                    <option value="UPI">UPI</option>
                                    <option value="Net Banking">Net Banking</option>
                                </select>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control" id="description" name="description" rows="2"></textarea>
                        </div>

                        <div class="mb-3">
                            <label for="receipt_image" class="form-label">Receipt Image</label>
                            <input type="file" class="form-control" id="receipt_image" name="receipt_image"
                                accept="image/*">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Split Type *</label>
                            <div class="btn-group w-100" role="group">
                                <input type="radio" class="btn-check" name="split_type" id="split_equal"
                                    value="equal" checked>
                                <label class="btn btn-outline-primary" for="split_equal">Equal Split</label>

                                <input type="radio" class="btn-check" name="split_type" id="split_exact"
                                    value="exact">
                                <label class="btn btn-outline-primary" for="split_exact">Exact Amount</label>

                                <input type="radio" class="btn-check" name="split_type" id="split_percentage"
                                    value="percentage">
                                <label class="btn btn-outline-primary" for="split_percentage">Percentage</label>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Split Between *</label>
                            <div id="splitContainer">
                                @foreach ($group->activeMembers as $member)
                                    <div class="form-check">
                                        <input class="form-check-input split-member" type="checkbox" name="splits[]"
                                            value="{{ $member->id }}" id="member_{{ $member->id }}">
                                        <label class="form-check-label" for="member_{{ $member->id }}">
                                            {{ $member->name }}
                                        </label>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Add Expense</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Add Member Modal -->
    <div class="modal fade" id="addMemberModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add New Member</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="{{ route('groups.add-member', $group) }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="member_name" class="form-label">Name *</label>
                            <input type="text" class="form-control" id="member_name" name="name" required>
                        </div>
                        <div class="mb-3">
                            <label for="member_email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="member_email" name="email">
                        </div>
                        <div class="mb-3">
                            <label for="member_phone" class="form-label">Phone</label>
                            <input type="text" class="form-control" id="member_phone" name="phone">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Add Member</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        // Split calculation logic
        document.addEventListener('DOMContentLoaded', function() {
            const splitTypeRadios = document.querySelectorAll('input[name="split_type"]');
            const splitContainer = document.getElementById('splitContainer');

            splitTypeRadios.forEach(radio => {
                radio.addEventListener('change', function() {
                    updateSplitInterface(this.value);
                });
            });

            function updateSplitInterface(splitType) {
                const members = @json($group->activeMembers);
                let html = '';

                if (splitType === 'equal') {
                    members.forEach(member => {
                        html += `
                        <div class="form-check">
                            <input class="form-check-input split-member" type="checkbox" name="splits[]" value="${member.id}" id="member_${member.id}">
                            <label class="form-check-label" for="member_${member.id}">
                                ${member.name}
                            </label>
                        </div>
                    `;
                    });
                } else if (splitType === 'exact') {
                    members.forEach(member => {
                        html += `
                        <div class="row mb-2">
                            <div class="col-6">
                                <div class="form-check">
                                    <input class="form-check-input split-member" type="checkbox" name="splits[${member.id}][member_id]" value="${member.id}" id="member_${member.id}">
                                    <label class="form-check-label" for="member_${member.id}">
                                        ${member.name}
                                    </label>
                                </div>
                            </div>
                            <div class="col-6">
                                <input type="number" class="form-control form-control-sm" name="splits[${member.id}][amount]" placeholder="Amount" step="0.01" min="0">
                            </div>
                        </div>
                    `;
                    });
                } else if (splitType === 'percentage') {
                    members.forEach(member => {
                        html += `
                        <div class="row mb-2">
                            <div class="col-6">
                                <div class="form-check">
                                    <input class="form-check-input split-member" type="checkbox" name="splits[${member.id}][member_id]" value="${member.id}" id="member_${member.id}">
                                    <label class="form-check-label" for="member_${member.id}">
                                        ${member.name}
                                    </label>
                                </div>
                            </div>
                            <div class="col-6">
                                <input type="number" class="form-control form-control-sm" name="splits[${member.id}][percentage]" placeholder="Percentage" step="0.01" min="0" max="100">
                            </div>
                        </div>
                    `;
                    });
                }

                splitContainer.innerHTML = html;
            }
        });
    </script>
@endsection

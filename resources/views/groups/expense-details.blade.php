@extends('layouts.app')

@section('title', 'Expense Details - ' . $expense->title)

@section('content')
    <div class="container-fluid">
        <!-- Back Button -->
        <div class="row mb-3">
            <div class="col-12">
                <a href="{{ route('groups.show', $group) }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-2"></i>Back to {{ $group->name }}
                </a>
            </div>
        </div>

        <!-- Expense Header -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-md-8">
                                <div class="d-flex align-items-center">
                                    <span class="category-icon me-3"
                                        style="background-color: {{ $expense->category_color }}; width: 60px; height: 60px; border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                                        <i class="{{ $expense->category_icon }} text-white fa-lg"></i>
                                    </span>
                                    <div>
                                        <h2 class="mb-1">{{ $expense->title }}</h2>
                                        <p class="text-muted mb-0">{{ $expense->description ?: 'No description provided' }}
                                        </p>
                                        <small class="text-muted">
                                            <i
                                                class="fas fa-calendar me-1"></i>{{ $expense->expense_date->format('M d, Y') }}
                                            @if ($expense->payment_method)
                                                | <i class="fas fa-credit-card me-1"></i>{{ $expense->payment_method }}
                                            @endif
                                        </small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4 text-end">
                                <h3 class="text-primary mb-0">{{ $expense->formatted_amount }}</h3>
                                @if ($expense->isSettled())
                                    <span class="badge bg-success">Settled</span>
                                @else
                                    <span class="badge bg-warning">Pending</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Expense Details -->
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fas fa-info-circle me-2"></i>Expense Details
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <p><strong>Category:</strong> {{ $expense->category }}</p>
                                <p><strong>Paid By:</strong> {{ $expense->paidBy->name }}</p>
                                <p><strong>Amount:</strong> {{ $expense->formatted_amount }}</p>
                            </div>
                            <div class="col-md-6">
                                <p><strong>Date:</strong> {{ $expense->expense_date->format('M d, Y') }}</p>
                                <p><strong>Payment Method:</strong> {{ $expense->payment_method ?: 'Not specified' }}</p>
                                <p><strong>Created:</strong> {{ $expense->created_at->format('M d, Y H:i') }}</p>
                            </div>
                        </div>

                        @if ($expense->description)
                            <div class="mt-3">
                                <strong>Description:</strong>
                                <p class="mt-2">{{ $expense->description }}</p>
                            </div>
                        @endif

                        @if ($expense->receipt_image)
                            <div class="mt-3">
                                <strong>Receipt:</strong>
                                <div class="mt-2">
                                    <img src="{{ asset('storage/' . $expense->receipt_image) }}" alt="Receipt"
                                        class="img-thumbnail" style="max-width: 300px; cursor: pointer;"
                                        onclick="openImageModal(this.src)">
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Split Details -->
                <div class="card mt-4">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fas fa-users me-2"></i>Split Details
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Member</th>
                                        <th>Amount</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($expense->splits as $split)
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <img src="{{ $split->member->avatar }}"
                                                        alt="{{ $split->member->name }}" class="rounded-circle me-3"
                                                        width="30" height="30">
                                                    {{ $split->member->name }}
                                                </div>
                                            </td>
                                            <td>
                                                <strong>₹{{ number_format($split->amount, 2) }}</strong>
                                            </td>
                                            <td>
                                                @if ($split->is_settled)
                                                    <span class="badge bg-success">Settled</span>
                                                @else
                                                    <span class="badge bg-warning">Pending</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if (!$split->is_settled)
                                                    <form action="{{ route('groups.settle-split', [$group, $split]) }}"
                                                        method="POST" class="d-inline">
                                                        @csrf
                                                        <button type="submit" class="btn btn-sm btn-outline-success">
                                                            <i class="fas fa-check me-1"></i>Settle
                                                        </button>
                                                    </form>
                                                @else
                                                    <small class="text-muted">
                                                        Settled on {{ $split->settled_at->format('M d, Y') }}
                                                    </small>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Actions Sidebar -->
            <div class="col-lg-4">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fas fa-cog me-2"></i>Actions
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            <a href="{{ route('groups.show', $group) }}" class="btn btn-outline-primary">
                                <i class="fas fa-arrow-left me-2"></i>Back to Group
                            </a>

                            <button class="btn btn-outline-secondary" onclick="window.print()">
                                <i class="fas fa-print me-2"></i>Print Details
                            </button>

                            @if (!$expense->isSettled())
                                <form action="{{ route('groups.delete-expense', [$group, $expense]) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-outline-danger w-100"
                                        onclick="return confirm('Are you sure you want to delete this expense?')">
                                        <i class="fas fa-trash me-2"></i>Delete Expense
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Summary Card -->
                <div class="card mt-4">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fas fa-chart-pie me-2"></i>Summary
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row text-center">
                            <div class="col-6">
                                <h6 class="text-muted">Total Amount</h6>
                                <h5 class="text-primary">{{ $expense->formatted_amount }}</h5>
                            </div>
                            <div class="col-6">
                                <h6 class="text-muted">Split Among</h6>
                                <h5 class="text-info">{{ $expense->splits->count() }} people</h5>
                            </div>
                        </div>
                        <hr>
                        <div class="text-center">
                            <h6 class="text-muted">Per Person Average</h6>
                            <h5 class="text-success">₹{{ number_format($expense->amount / $expense->splits->count(), 2) }}
                            </h5>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Image Modal -->
    <div class="modal fade" id="imageModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Receipt Image</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body text-center">
                    <img id="modalImage" src="" alt="Receipt" class="img-fluid">
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        function openImageModal(src) {
            document.getElementById('modalImage').src = src;
            new bootstrap.Modal(document.getElementById('imageModal')).show();
        }
    </script>
@endsection

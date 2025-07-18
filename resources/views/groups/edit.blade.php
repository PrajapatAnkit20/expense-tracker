@extends('layouts.app')

@section('title', 'Edit Group - ' . $group->name)

@section('content')
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header">
                        <h4 class="mb-0">
                            <i class="fas fa-edit me-2"></i>
                            Edit Group: {{ $group->name }}
                        </h4>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('groups.update', $group) }}" method="POST" enctype="multipart/form-data"
                            id="editGroupForm">
                            @csrf
                            @method('PUT')

                            <!-- Group Basic Info -->
                            <div class="row mb-4">
                                <div class="col-md-6">
                                    <label for="name" class="form-label">Group Name *</label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror"
                                        id="name" name="name" value="{{ old('name', $group->name) }}" required>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label for="image" class="form-label">Group Image</label>
                                    <input type="file" class="form-control @error('image') is-invalid @enderror"
                                        id="image" name="image" accept="image/*">
                                    @error('image')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    @if ($group->image)
                                        <div class="mt-2">
                                            <img src="{{ asset('storage/' . $group->image) }}" alt="Current Image"
                                                class="img-thumbnail" width="100">
                                            <small class="text-muted d-block">Current image</small>
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <div class="mb-4">
                                <label for="description" class="form-label">Description</label>
                                <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description"
                                    rows="3" placeholder="What's this group for?">{{ old('description', $group->description) }}</textarea>
                                @error('description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Group Members Management -->
                            <div class="mb-4">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <label class="form-label mb-0">Group Members</label>
                                    <button type="button" class="btn btn-outline-primary btn-sm" onclick="addMember()">
                                        <i class="fas fa-plus me-1"></i>
                                        Add Member
                                    </button>
                                </div>

                                <div id="membersContainer">
                                    @if ($group->members && $group->members->count() > 0)
                                        @foreach ($group->members as $index => $member)
                                            <div class="member-input mb-3">
                                                <div class="card">
                                                    <div class="card-body">
                                                        <div class="row">
                                                            <div class="col-md-4">
                                                                <input type="hidden"
                                                                    name="members[{{ $index }}][id]"
                                                                    value="{{ $member->id }}">
                                                                <input type="text" class="form-control"
                                                                    name="members[{{ $index }}][name]"
                                                                    value="{{ $member->name }}" placeholder="Member Name"
                                                                    required>
                                                            </div>
                                                            <div class="col-md-4">
                                                                <input type="email" class="form-control"
                                                                    name="members[{{ $index }}][email]"
                                                                    value="{{ $member->email }}"
                                                                    placeholder="Email (optional)">
                                                            </div>
                                                            <div class="col-md-3">
                                                                <input type="text" class="form-control"
                                                                    name="members[{{ $index }}][phone]"
                                                                    value="{{ $member->phone }}"
                                                                    placeholder="Phone (optional)">
                                                            </div>
                                                            <div class="col-md-1">
                                                                @if ($group->members->count() > 1)
                                                                    <button type="button"
                                                                        class="btn btn-outline-danger btn-sm"
                                                                        onclick="removeMember(this)">
                                                                        <i class="fas fa-times"></i>
                                                                    </button>
                                                                @else
                                                                    <button type="button"
                                                                        class="btn btn-outline-secondary btn-sm" disabled>
                                                                        <i class="fas fa-lock"></i>
                                                                    </button>
                                                                @endif
                                                            </div>
                                                        </div>
                                                        @if ($member->expenses && $member->expenses->count() > 0)
                                                            <div class="row mt-2">
                                                                <div class="col-12">
                                                                    <small class="text-info">
                                                                        <i class="fas fa-info-circle me-1"></i>
                                                                        This member has {{ $member->expenses->count() }}
                                                                        expense(s) associated
                                                                    </small>
                                                                </div>
                                                            </div>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    @else
                                        <div class="alert alert-info">
                                            <i class="fas fa-info-circle me-2"></i>
                                            No members found. Click "Add Member" to add members to this group.
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <!-- Group Statistics (Read Only) -->
                            <div class="mb-4">
                                <label class="form-label">Group Statistics</label>
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="card bg-light">
                                            <div class="card-body text-center">
                                                <h6 class="card-title">Total Expenses</h6>
                                                <h4 class="text-primary">
                                                    ₹{{ number_format($group->total_expenses ?? 0, 2) }}</h4>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="card bg-light">
                                            <div class="card-body text-center">
                                                <h6 class="card-title">Total Members</h6>
                                                <h4 class="text-success">
                                                    {{ $group->members ? $group->members->count() : 0 }}</h4>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="card bg-light">
                                            <div class="card-body text-center">
                                                <h6 class="card-title">Total Entries</h6>
                                                <h4 class="text-info">
                                                    {{ $group->expenses ? $group->expenses->count() : 0 }}</h4>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="card bg-light">
                                            <div class="card-body text-center">
                                                <h6 class="card-title">Created</h6>
                                                <h6 class="text-muted">{{ $group->created_at->format('M d, Y') }}</h6>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Submit Buttons -->
                            <div class="d-flex justify-content-between">
                                <a href="{{ route('groups.show', $group) }}" class="btn btn-secondary">
                                    <i class="fas fa-arrow-left me-2"></i>
                                    Back to Group
                                </a>
                                <div>
                                    <button type="submit" class="btn btn-primary me-2">
                                        <i class="fas fa-save me-2"></i>
                                        Update Group
                                    </button>
                                    <button type="button" class="btn btn-danger" onclick="confirmDelete()">
                                        <i class="fas fa-trash me-2"></i>
                                        Delete Group
                                    </button>
                                </div>
                            </div>
                        </form>

                        <!-- Delete Form (Hidden) -->
                        <form id="deleteForm" action="{{ route('groups.destroy', $group) }}" method="POST"
                            style="display: none;">
                            @csrf
                            @method('DELETE')
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        let memberCount = {{ $group->members ? $group->members->count() : 0 }};

        function addMember() {
            const container = document.getElementById('membersContainer');
            const memberHtml = `
                <div class="member-input mb-3">
                    <div class="card">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-4">
                                    <input type="text" class="form-control" 
                                           name="members[${memberCount}][name]" 
                                           placeholder="Member Name" required>
                                </div>
                                <div class="col-md-4">
                                    <input type="email" class="form-control" 
                                           name="members[${memberCount}][email]" 
                                           placeholder="Email (optional)">
                                </div>
                                <div class="col-md-3">
                                    <input type="text" class="form-control" 
                                           name="members[${memberCount}][phone]" 
                                           placeholder="Phone (optional)">
                                </div>
                                <div class="col-md-1">
                                    <button type="button" class="btn btn-outline-danger btn-sm" onclick="removeMember(this)">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            `;
            container.insertAdjacentHTML('beforeend', memberHtml);
            memberCount++;
        }

        function removeMember(button) {
            const memberInputs = document.querySelectorAll('.member-input');
            if (memberInputs.length > 1) {
                const memberCard = button.closest('.member-input');
                const hasExpenses = memberCard.querySelector('.text-info');

                if (hasExpenses) {
                    if (confirm(
                            'This member has expenses associated. Are you sure you want to remove them? This may affect expense calculations.'
                        )) {
                        memberCard.remove();
                    }
                } else {
                    memberCard.remove();
                }
            } else {
                alert('At least one member is required!');
            }
        }

        function confirmDelete() {
            if (confirm(
                    'Are you sure you want to delete this group? This action cannot be undone and will delete all associated expenses.'
                )) {
                document.getElementById('deleteForm').submit();
            }
        }

        // Image preview
        document.getElementById('image').addEventListener('change', function(event) {
            const file = event.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    // You can add image preview logic here if needed
                    console.log('Image selected:', file.name);
                };
                reader.readAsDataURL(file);
            }
        });

        // Form validation
        document.getElementById('editGroupForm').addEventListener('submit', function(event) {
            const memberInputs = document.querySelectorAll('input[name$="[name]"]');
            let hasValidMember = false;

            memberInputs.forEach(function(input) {
                if (input.value.trim() !== '') {
                    hasValidMember = true;
                }
            });

            if (!hasValidMember) {
                event.preventDefault();
                alert('At least one member with a name is required!');
            }
        });
    </script>
@endsection

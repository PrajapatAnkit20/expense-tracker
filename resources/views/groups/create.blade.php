@extends('layouts.app')

@section('title', 'Create New Group')

@section('content')
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header">
                        <h4 class="mb-0">
                            <i class="fas fa-plus me-2"></i>
                            Create New Group
                        </h4>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('groups.store') }}" method="POST" enctype="multipart/form-data"
                            id="createGroupForm">
                            @csrf

                            <!-- Group Basic Info -->
                            <div class="row mb-4">
                                <div class="col-md-6">
                                    <label for="name" class="form-label">Group Name *</label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror"
                                        id="name" name="name" value="{{ old('name') }}" required>
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
                                </div>
                            </div>

                            <div class="mb-4">
                                <label for="description" class="form-label">Description</label>
                                <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description"
                                    rows="3" placeholder="What's this group for?">{{ old('description') }}</textarea>
                                @error('description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Group Members -->
                            <div class="mb-4">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <label class="form-label mb-0">Group Members *</label>
                                    <button type="button" class="btn btn-outline-primary btn-sm" onclick="addMember()">
                                        <i class="fas fa-plus me-1"></i>
                                        Add Member
                                    </button>
                                </div>

                                <div id="membersContainer">
                                    <!-- Default member input -->
                                    <div class="member-input mb-3">
                                        <div class="card">
                                            <div class="card-body">
                                                <div class="row">
                                                    <div class="col-md-4">
                                                        <input type="text" class="form-control" name="members[0][name]"
                                                            placeholder="Member Name" required>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <input type="email" class="form-control" name="members[0][email]"
                                                            placeholder="Email (optional)">
                                                    </div>
                                                    <div class="col-md-3">
                                                        <input type="text" class="form-control" name="members[0][phone]"
                                                            placeholder="Phone (optional)">
                                                    </div>
                                                    <div class="col-md-1">
                                                        <button type="button" class="btn btn-outline-danger btn-sm"
                                                            onclick="removeMember(this)">
                                                            <i class="fas fa-times"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Submit Buttons -->
                            <div class="d-flex justify-content-between">
                                <a href="{{ route('groups.index') }}" class="btn btn-secondary">
                                    <i class="fas fa-arrow-left me-2"></i>
                                    Back to Groups
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save me-2"></i>
                                    Create Group
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        let memberCount = 1;

        function addMember() {
            const container = document.getElementById('membersContainer');
            const memberHtml = `
            <div class="member-input mb-3">
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4">
                                <input type="text" class="form-control" 
                                       name="members[${memberCount}][name]" placeholder="Member Name" required>
                            </div>
                            <div class="col-md-4">
                                <input type="email" class="form-control" 
                                       name="members[${memberCount}][email]" placeholder="Email (optional)">
                            </div>
                            <div class="col-md-3">
                                <input type="text" class="form-control" 
                                       name="members[${memberCount}][phone]" placeholder="Phone (optional)">
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
                button.closest('.member-input').remove();
            } else {
                alert('At least one member is required!');
            }
        }

        // Image preview
        document.getElementById('image').addEventListener('change', function(event) {
            const file = event.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    // You can add image preview logic here if needed
                };
                reader.readAsDataURL(file);
            }
        });
    </script>
@endsection

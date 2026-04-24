@extends('layout.dashboard-layout')

@section('css')
<link rel="stylesheet" href="{{asset('assets/bundles/datatables/datatables.min.css')}}">
<link rel="stylesheet" href="{{asset('assets/bundles/datatables/DataTables-1.10.16/css/dataTables.bootstrap4.min.css')}}">
@endsection

@section('content')

{{-- Delete Modal Component --}}
<x-delete-modal id="deleteModal" name-id="name" form-id="deleteForm" title="Deleted" />
<div class="main-content">
    <section class="section">

        {{-- <div class="d-flex justify-content-end pb-3">
            <a href="{{ route('users.create') }}" class="btn btn-primary text-white">+ Add New</a>
        </div> --}}

        <div class="section-body">

            <x-sweet-alert />

            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h4>Users List</h4>
                        </div>

                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-striped" id="table-1">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Username</th>
                                            <th>Email</th>
                                            <th>City</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>

                                    <tbody>
                                        @forelse($users as $data)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $data->profile->username ?? '--' }}</td>
                                            <td>{{ $data->email }}</td>
                                            <td>{{ $data->profile->current_city ?? '--'}} </td>
                                            <td>
                                                {{-- <a href="{{ route('users.edit', $data->id) }}" class="btn btn-sm btn-primary">
                                                    <i data-feather="edit-2" width="14" height="14"></i>
                                                </a> --}}

                                                {{-- Delete Button triggers modal --}}
                                                <form id="deleteForm-{{ $data->id }}" action="{{ route('users.destroy', $data->id) }}" method="POST" style="display:inline-block;">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="button" class="btn btn-sm btn-danger" onclick="confirmDelete('deleteForm-{{ $data->id }}', '{{ $data->name }}')">
                                                        <i data-feather="trash-2" width="14" height="14"></i>
                                                    </button>
                                                </form>

                                            </td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="5" class="text-center">No volunteers found</td>
                                        </tr>
                                        @endforelse
                                    </tbody>

                                </table>
                            </div>
                        </div>

                    </div>
                </div>
            </div>

        </div>
    </section>
</div>
@endsection

@section('js')
<script src="{{asset('assets/bundles/datatables/datatables.min.js')}}"></script>
<script src="{{asset('assets/bundles/datatables/DataTables-1.10.16/js/dataTables.bootstrap4.min.js')}}"></script>
<script src="{{asset('assets/bundles/jquery-ui/jquery-ui.min.js')}}"></script>
<script src="{{asset('assets/js/page/datatables.js')}}"></script>

<script>
function confirmDelete(formId, itemName) {
    Swal.fire({
        title: `Are you sure you want to delete ${itemName}?`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Yes, delete!',
        cancelButtonText: 'Cancel',
        reverseButtons: true
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById(formId).submit();
        }
    });
}
</script>
@endsection

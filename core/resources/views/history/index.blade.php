@extends('dash.layouts.app')

@section('title', 'History')

@section('content')
<div class="mt-4">
    <div class="card">
        <div class="card-datatable table-responsive pt-0">
            <table class="datatables-basic table">
                <thead>
                    <tr>
                        <th></th>
                        <th></th>
                        <th>Receiver</th>
                        <th>Status</th>
                        <th>Created at</th>
                        <th>Action</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
</div>
@endsection

@push('js')
    <script>
        (function() {
            'use strict';
            var ilsya = new velixs()

            var dbs = ilsya.datatables({
                url: "{{ route('history') }}",
                url_delete: "{{ route('history.delete') }}",
                header: `History Message`,
                columns: [{
                        data: 'responsive_id'
                    },
                    {
                        data: 'responsive_id'
                    },
                    {
                        data: 'receiver'
                    },
                    {
                        data: 'status'
                    },
                    {
                        data: 'created_at'
                    },
                    {
                        data: 'action'
                    }
                ],
                btn: [
                    {
                        text: '<i class="ti ti-trash me-sm-1"></i> <span class="d-none d-sm-inline-block">Delete</span>',
                        className: 'is-button-delete btn me-2 btn-label-danger'
                    }
                ],
            })
        })()
    </script>
@endpush

@push('cssvendor')
    <link rel="stylesheet" href="{!! asset('assets') !!}/vendor/libs/datatables-bs5/datatables.bootstrap5.css" />
    <link rel="stylesheet" href="{!! asset('assets') !!}/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.css" />
    <link rel="stylesheet" href="{!! asset('assets') !!}/vendor/libs/datatables-checkboxes-jquery/datatables.checkboxes.css" />
    <link rel="stylesheet" href="{!! asset('assets') !!}/vendor/libs/datatables-buttons-bs5/buttons.bootstrap5.css" />
@endpush

@push('jsvendor')
    <script src="{!! asset('assets') !!}/vendor/libs/datatables-bs5/datatables-bootstrap5.js"></script>
@endpush

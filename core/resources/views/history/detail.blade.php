@extends('dash.layouts.app')

@section('title', 'History Detail')


@section('content')
    <div class="nav-align-top mb-4">
        <div class="tab-content">
            <div class="tab-pane fade show active" id="tab-info" role="tabpanel">

                <div class="alert alert-dark alert-dismissible d-flex align-items-baseline" role="alert">
                    <span class="alert-icon alert-icon-lg text-dark me-2">
                        <i class="ti ti-message ti-sm"></i>
                    </span>
                    <div class="d-flex flex-column ps-1">
                        <h5 class="alert-heading mb-2">Message Detail</h5>
                        <p class="mb-">
                        <table>
                            <tbody>
                                <tr>
                                    <td class="fw-bold">Receiver </td>
                                    <td>: {{ $row->receiver }}</td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">Created at </td>
                                    <td>: {{ \Carbon\Carbon::parse($row->creted_at)->format('d M Y H:i') }}</td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">Status </td>
                                    <td>: {{ \Str::ucfirst($row->status) }}</td>
                                </tr>
                            </tbody>
                        </table>
                        </p>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12">
                        <div class="mb-3">
                            <label class="form-label">Message Type</label>
                            <select class="form-select" disabled>
                                @if ($row->message_type == 'text')
                                    <option selected>Text Message</option>
                                @elseif ($row->message_type == 'media')
                                    <option selected>Media Message</option>
                                @elseif ($row->message_type == 'button')
                                    <option selected>Button Message</option>
                                @endif
                            </select>
                        </div>
                    </div>
                    @if ($row->message_type == 'text')
                        <div class="col-12">
                            <div class="mb-3"><label class="form-label">Message</label>
                                <textarea name="message" rows="6" class="form-control" required disabled>{!! $data->message !!}</textarea>
                            </div>
                        </div>
                    @elseif ($row->message_type == 'media')
                        <div class="col-12 col-xl-6 col-lg-6">
                            <div class="mb-3"><label class="form-label">Media</label>
                                <div class="input-group">
                                    <input disabled type="text" class="form-control" value="{{ $data->url }}" name="media" required>
                                    <button class="btn btn-primary waves-effect is-button-preview" type="button">Preview</button>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-xl-6 col-lg-6">
                            <div class="mb-3">
                                <label class="form-label">Media Mime</label><select disabled name="media_type" required class="form-select">
                                    <option {{ $data->media_type == 'image' ? 'selected' : '' }} value="image">Image</option>
                                    <option {{ $data->media_type == 'video' ? 'selected' : '' }} value="video">Video</option>
                                    <option {{ $data->media_type == 'audio' ? 'selected' : '' }} value="audio">Audio</option>
                                    <option {{ $data->media_type == 'file' ? 'selected' : '' }} value="file">File</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="mb-3"><label class="form-label">Message</label>
                                <textarea name="message" rows="6" class="form-control" disabled>{!! $data->caption !!}</textarea>
                            </div>
                        </div>
                    @elseif ($row->message_type == 'button')
                        <div class="col-12">
                            <div class="mb-3">
                                <label class="form-label">Message</label>
                                <textarea name="message" rows="6" class="form-control" disabled required>{{ $data->message }}</textarea>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="mb-3">
                                <label class="form-label">Footer</label>
                                <input name="footer" class="form-control" disabled value="{{ $data->footer }}" required autocomplete="off">
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="table_button_message">
                                <div class="table-responsive text-nowrap">
                                    <table class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th>Display Text</th>
                                                <th>Respond</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody class="table-border-bottom-0 is-content">
                                            @foreach ($data->buttons as $btn)
                                                <tr>
                                                    <td><input type="text" disabled class="form-control" required value="{{ $btn->display ?? '' }}"></td>
                                                    <td><input type="text" disabled class="form-control" required value="{{ $btn->id ?? '' }}"></td>
                                                    <td><button class="btn btn-label-danger" disabled type="button"><i class="ti ti-trash-x"></i></button></td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    @elseif ($row->message_type == 'list')
                        <div class="col-12">
                            <div class="mb-3">
                                <label class="form-label">Title</label>
                                <input name="title" class="form-control" value="{{ $data->title }}" required autocomplete="off">
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="mb-3">
                                <label class="form-label">Message</label>
                                <textarea name="message" rows="6" class="form-control" required>{{ $data->message }}</textarea>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="mb-3">
                                <label class="form-label">Footer</label>
                                <input name="footer" class="form-control" value="{{ $data->footer }}" required autocomplete="off">
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="mb-3"><label class="form-label">Button Text</label>
                                <input name="button_text" class="form-control" value="{{ $data->buttonText }}" required placeholder="Click Here" autocomplete="off">
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="table_button_message table-list-btn">
                                <div class="table-responsive text-nowrap">
                                    <table class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th>Display Text</th>
                                                <th>Respond</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody class="table-border-bottom-0 is-content">
                                            @foreach ($data->sections as $btn)
                                                @isset($btn->title)
                                                    <tr>
                                                        <td>
                                                            <select disabled class="form-select" required="">
                                                                <option value="option">Option</option>
                                                                <option selected value="section">Section</option>
                                                            </select>
                                                        </td>
                                                        <td><input type="text" disabled class="form-control" value="{{ $btn->title ?? '' }}" placeholder="Ex: Menu Click Me" required=""></td>
                                                        <td data-input-btn-id>
                                                            <input style="display: none" type="text" disabled value="display" placeholder="Ex: !menu" class="form-control" required="">
                                                            <div>-</div>
                                                        </td>
                                                        <td><button class="btn btn-label-danger" disabled type="button"><i class="ti ti-trash-x"></i></button></td>
                                                    </tr>
                                                @endisset
                                                @foreach ($btn->rows as $resbtn)
                                                    <tr>
                                                        <td>
                                                            <select disabled class="form-select" required="">
                                                                <option selected value="option">Option</option>
                                                                <option value="section">Section</option>
                                                            </select>
                                                        </td>
                                                        <td><input type="text" class="form-control" disabled value="{{ $resbtn->title ?? '' }}" placeholder="Ex: Menu Click Me" required=""></td>
                                                        <td data-input-btn-id><input type="text" disabled value="{{ $resbtn->rowId ?? '' }}" placeholder="Ex: !menu" class="form-control" required=""></td>
                                                        <td><button disabled class="btn btn-label-danger" type="button"><i class="ti ti-trash-x"></i></button></td>
                                                    </tr>
                                                @endforeach
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>

            </div>
        </div>
    </div>


    <div class="modal fade" id="modal-preview" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                </div>
            </div>
        </div>
    </div>
@endsection

@push('js')
    <script>
        'use strict';
        $(".is-button-preview").on("click", function() {
            var url = $("input[name='media']").val()
            var message_type = $("select[name='media_type']").val()
            $("#modal-preview").modal("show")
            switch (message_type) {
                case 'image':
                    $("#modal-preview .modal-body").html(`<img src="${url}" class="img-fluid" alt="">`)
                    break
                case 'video':
                    $("#modal-preview .modal-body").html(`<div class="text-center"><video style="width: 100%;" controls><source  src="${url}" type="video/mp4">Your browser does not support the video tag.</video></div>`)
                    break
                case 'audio':
                    $("#modal-preview .modal-body").html(`<div class="text-center"><audio controls><source src="${url}" type="audio/mpeg">Your browser does not support the audio element.</audio></div>`)
                    break
                case 'file':
                    $("#modal-preview .modal-body").html(`<div class="text-center"><a href="${url}" class="btn btn-primary">Download</a></div>`)
                    break
            }
        })

        $("#modal-preview").on("hidden.bs.modal", function() {
            $("#modal-preview .modal-body").html("")
        })
    </script>
@endpush

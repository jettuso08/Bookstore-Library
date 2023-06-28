@extends('master')

@section('styles')
{{-- Datatable --}}
<link href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css" rel="stylesheet">
<link href="https://cdn.datatables.net/buttons/2.3.6/css/buttons.dataTables.min.css" rel="stylesheet">

{{-- Dropify --}}
<link href="https://cdn.jsdelivr.net/npm/dropify@0.2.2/dist/css/dropify.min.css" rel="stylesheet">

<style>
    .dropify-wrapper .dropify-message p {
        font-size: 18px;
    }
</style>
@stop

@section('content')
    <div class="card">
        <h5 class="card-header">Books</h5>
        <div class="card-body">
            <div class="text-end mb-3">
                <button class="btn btn-success import-btn" type="button" role="button">Import</button>
                <a class="btn btn-primary" href="{{ route('books.create') }}" role="button">Add</a>
            </div>

            @if (\Session::has('success'))
            <div class="alert alert-success">
                {!! \Session::get('success') !!}
            </div>
            @endif

            @if($errors->any())
            <div class="alert alert-danger" role="alert">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            <table class="table" id="booksTable">
                <thead>
                    <tr>
                        <th scope="col">#</th>
                        <th scope="col">Name</th>
                        <th scope="col">Author</th>
                        <th scope="col">Cover</th>
                        <th scope="col">Actions</th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>
    </div>

    <div class="modal fade" id="bookDetailsModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Book Details</h5>
                    <button type="button" class="btn-close close-book-modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label"><b>Book Name:</b> <span id="bookName"></span></label>
                    </div>
                    <div class="mb-3">
                        <label class="form-label"><b>Book Author:</b> <span id="bookAuthor"></span></label>
                    </div>
                    <div>
                        <label class="form-label"><b>Book Cover:</b> <span id="bookCover"></span></label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary close-book-modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="importModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Import Books</h5>
                    <button type="button" class="btn-close close-import-modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    <input type="file" class="dropify" name="books_file" accept=".csv, application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel"/>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary close-import-modal">Close</button>
                    <button type="button" class="btn btn-primary import-book">Import</button>
                </div>
            </div>
        </div>
    </div>
@stop

@section('scripts')
{{-- Datatable --}}
<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.3.6/js/dataTables.buttons.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/buttons/2.3.6/js/buttons.html5.min.js"></script>

{{-- SweetAlert --}}
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

{{-- Dropify --}}
<script src="https://cdn.jsdelivr.net/npm/dropify@0.2.2/dist/js/dropify.min.js"></script>

<script>
    $(function () {
        const Toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true,
            didOpen: (toast) => {
                toast.addEventListener('mouseenter', Swal.stopTimer)
                toast.addEventListener('mouseleave', Swal.resumeTimer)
            }
        });

        $('.dropify').dropify();

        var bookDetailsModal = new bootstrap.Modal(document.getElementById('bookDetailsModal'))
        var importModal = new bootstrap.Modal(document.getElementById('importModal'))

        let booksTable = $('#booksTable').DataTable({
            dom: 'Bfrtip',
            processing: true,
            serverSide: true,
            ajax: {
                url: '{{ route("books.table") }}',
                type: 'POST',
                data: {
                    '_token': $('meta[name="csrf-token"]').attr('content')
                }
            },
            columns: [{
                    data: 'DT_RowIndex',
                    name: 'DT_RowIndex'
                },
                {
                    data: 'name',
                    name: 'name'
                },
                {
                    data: 'author',
                    name: 'author'
                },
                {
                    data: 'cover',
                    name: 'cover'
                },
                {
                    data: 'action',
                    name: 'action',
                    orderable: false,
                    searchable: false
                },
            ],
            order: [1, 'asc'],
            buttons: [
                {
                    extend: 'copyHtml5',
                    exportOptions: {
                        columns: [ 1, 2, 3 ]
                    }
                },
                {
                    extend: 'excelHtml5',
                    exportOptions: {
                        columns: [ 1, 2, 3 ]
                    }
                },
                {
                    extend: 'csvHtml5',
                    exportOptions: {
                        columns: [ 1, 2, 3 ]
                    }
                },
                {
                    extend: 'pdfHtml5',
                    exportOptions: {
                        columns: [ 1, 2, 3 ]
                    }
                }
            ]
        });

        $(document).on('click', '.view-book', function (e) {
            var button = $(this);
            var buttonHtml = button.html();
            var id = button.attr('data-id');

            if (id && !button.is(':disabled')) {
                button.attr('disabled', 'disabled');
                button.html('<i class="fa fa-spinner fa-spin"></i>');

                var url = '{{ route("books.detail", [":id"]) }}';
                url = url.replace(':id', id);

                $.ajax({
                    url: url,
                    success: function (result) {
                        if (result.success) {
                            $('#bookDetailsModal #bookName').text(result.book.name);
                            $('#bookDetailsModal #bookAuthor').text(result.book.author);
                            $('#bookDetailsModal #bookCover').text(result.book.cover);
                            bookDetailsModal.show();
                        } else {
                            Toast.fire({
                                icon: 'error',
                                title: result.msg
                            });
                        }

                        button.removeAttr('disabled');
                        button.html(buttonHtml);
                    }
                });
            }
        });

        $(document).on('click', '.close-book-modal', function (e) {
            bookDetailsModal.toggle('hide');
        });

        $(document).on('click', '.delete-book', function (e) {
            var button = $(this);
            var buttonHtml = button.html();
            var id = button.attr('data-id');

            if (id && !button.is(':disabled')) {
                button.attr('disabled', 'disabled');
                button.html('<i class="fa fa-spinner fa-spin"></i>');

                Swal.fire({
                    title: 'Are you sure you want to delete this book?',
                    showCancelButton: true,
                    confirmButtonText: 'Yes'
                }).then((result) => {
                    if (result.isConfirmed) {
                        var url = '{{ route("books.destroy", [":id"]) }}';
                        url = url.replace(':id', id);

                        $.ajax({
                            url: url,
                            type: 'DELETE',
                            data: {
                                '_token': $('meta[name="csrf-token"]').attr('content')
                            },
                            success: function (result) {
                                if (result.success) {
                                    Toast.fire({
                                        icon: 'success',
                                        title: result.msg
                                    });

                                    booksTable.ajax.reload();
                                } else {
                                    Toast.fire({
                                        icon: 'error',
                                        title: result.msg
                                    });
                                }

                                button.removeAttr('disabled');
                                button.html(buttonHtml);
                            }
                        });
                    } else {
                        button.removeAttr('disabled');
                        button.html(buttonHtml);
                    }
                })

            }
        });

        $(document).on('click', '.import-btn', function (e) {
            importModal.show();
        });

        $(document).on('click', '.close-import-modal', function (e) {
            importModal.toggle('hide');
        });

        $(document).on('click', '#importModal .import-book', function (e) {
            var button      = $(this);
            var buttonHtml  = button.html();
            var formData    = new FormData();
            var file        = $('#importModal input[name="books_file"]').val();

            if (file) {
                if (!button.is(':disabled')) {
                    button.attr('disabled', 'disabled');
                    button.html('<i class="fa fa-spinner fa-spin"></i>');

                    var url = '{{ route("books.import") }}';

                    formData.append('file', $('#importModal input[name="books_file"]')[0].files[0]);
                    formData.append('_token', $('meta[name="csrf-token"]').attr('content'));

                    $.ajax({
                        url: url,
                        type: 'POST',
                        data : formData,
                        processData: false,
                        contentType: false,
                        success: function (result) {
                            if (result.success) {
                                Toast.fire({
                                    icon: 'success',
                                    title: result.msg
                                });

                                $('.dropify-clear').click();

                                booksTable.ajax.reload();
                                importModal.toggle('hide');
                            }
                            else {
                                Toast.fire({
                                    icon: 'error',
                                    title: result.msg
                                });
                            }

                            button.removeAttr('disabled');
                            button.html(buttonHtml);
                        }
                    });
                }
            }
            else {
                Toast.fire({
                    icon: 'error',
                    title: 'Please upload a file'
                });
            }
        });
        
    });

</script>
@stop

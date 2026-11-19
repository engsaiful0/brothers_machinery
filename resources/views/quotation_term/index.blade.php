@extends('layouts.app')
@section('title', 'Commercial Terms')

@section('content')

    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1 class="tw-text-xl md:tw-text-3xl tw-font-bold tw-text-black">Commercial Terms</h1>
    </section>

    <!-- Main content -->
    <section class="content">
        @component('components.widget', ['class' => 'box-primary', 'title' => 'Terms & conditions you can pick on a quotation'])
            @slot('tool')
                <div class="box-tools">
                    <a class="tw-dw-btn tw-bg-gradient-to-r tw-from-indigo-600 tw-to-blue-500 tw-font-bold tw-text-white tw-border-none tw-rounded-full btn-modal"
                        data-href="{{ action([\App\Http\Controllers\QuotationTermController::class, 'create']) }}"
                        data-container=".quotation_terms_modal">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                            class="icon icon-tabler icons-tabler-outline icon-tabler-plus">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                            <path d="M12 5l0 14" />
                            <path d="M5 12l14 0" />
                        </svg> @lang('messages.add')
                    </a>
                </div>
            @endslot
            <div class="table-responsive">
                <table class="table table-bordered table-striped" id="quotation_terms_table">
                    <thead>
                        <tr>
                            <th>Order</th>
                            <th>Title</th>
                            <th>Description</th>
                            <th class="not-export">@lang('messages.action')</th>
                        </tr>
                    </thead>
                </table>
            </div>
        @endcomponent

        <div class="modal fade quotation_terms_modal" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel">
        </div>

    </section>
    <!-- /.content -->
@stop
@section('javascript')
    <script type="text/javascript">
        $(function() {
            var quotation_terms_table = $('#quotation_terms_table').DataTable({
                processing: true,
                serverSide: true,
                ajax: @json(action([\App\Http\Controllers\QuotationTermController::class, 'index'])),
                order: [[0, 'asc']],
                columnDefs: [{ targets: 3, orderable: false, searchable: false }],
            });

            $(document).on('click', 'button.edit_quotation_term_button', function() {
                $('div.quotation_terms_modal').load($(this).data('href'), function() {
                    $(this).modal('show');
                });
            });

            // Shared by the add and edit forms (both load into the same modal).
            $(document).on('submit', 'form#quotation_term_form', function(e) {
                e.preventDefault();
                var form = $(this);
                var errors = form.find('.quotation-term-errors').hide().empty();
                form.find('button[type="submit"]').attr('disabled', true);
                $.ajax({
                    method: 'POST',
                    url: form.attr('action'),
                    dataType: 'json',
                    data: form.serialize(),
                    success: function(result) {
                        $('div.quotation_terms_modal').modal('hide');
                        toastr.success(result.msg);
                        quotation_terms_table.ajax.reload();
                    },
                    error: function(xhr) {
                        var messages = xhr.responseJSON && xhr.responseJSON.errors
                            ? [].concat.apply([], Object.values(xhr.responseJSON.errors))
                            : [LANG.something_went_wrong];
                        errors.html(messages.map(function(m) { return $('<div>').text(m).html(); }).join('<br>')).show();
                    },
                    complete: function() {
                        form.find('button[type="submit"]').attr('disabled', false);
                    },
                });
            });

            $(document).on('click', 'button.delete_quotation_term_button', function() {
                var href = $(this).data('href');
                swal({
                    title: LANG.sure,
                    text: 'This term will no longer be available for new quotations. Existing quotations keep their terms.',
                    icon: 'warning',
                    buttons: true,
                    dangerMode: true,
                }).then(function(willDelete) {
                    if (!willDelete) return;
                    $.ajax({
                        method: 'DELETE',
                        url: href,
                        dataType: 'json',
                        data: { _token: $('meta[name="csrf-token"]').attr('content') },
                        success: function(result) {
                            toastr.success(result.msg);
                            quotation_terms_table.ajax.reload();
                        },
                        error: function() {
                            toastr.error(LANG.something_went_wrong);
                        },
                    });
                });
            });
        });
    </script>
@endsection

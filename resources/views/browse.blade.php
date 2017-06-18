@extends('voyager::master')

@section('page_header')
    <h1 class="page-title">
        <i class="voyager-hook"></i> Hooks
        <a class="btn btn-success refresh" href="{{ route('voyager.hooks.cache.refresh') }}">
            <i class="voyager-refresh"></i> Update cache
        </a>
    </h1>
@stop

@section('page_header_actions')

@stop

@section('content')
    <div class="page-content container-fluid">
        @if($daysSinceLastCheck >= 10)
            <div class="row">
                <div class="col-md-12">
                    <div class="alert alert-warning">
                        <p>You have not checked for any updates for the last {{ $daysSinceLastCheck }} days.</p>
                    </div>
                </div>
            </div>
        @endif
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-bordered">
                    <div class="panel-body">
                        <table id="dataTable" class="table table-hover">
                            <thead>
                            <tr>
                                <th>Name</th>
                                <th>Enabled</th>
                                <th class="actions">Actions</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($hooks as $hook)
                                <tr>
                                    <td>
                                        <i class="voyager-{{ $hook->type }}"></i> {{ $hook->name }}
                                    </td>
                                    <td>
                                        <?= ($hook->enabled ? '<i class="voyager-check"></i> ENABLED' : '<i class="voyager-x"></i> DISABLED') ?>
                                    </td>
                                    <td class="no-sort no-click">
                                        @if ($hook->installed)
                                            <a href="javascript:;" class="btn-sm btn-danger pull-right uninstall"
                                                data-id="{{ $hook->name }}" id="uninstall-{{ $hook->name }}">
                                                <i class="voyager-trash"></i> Uninstall
                                            </a>
                                            <a href="{{ route('voyager.hooks.'.($hook->enabled ? 'disable' : 'enable'), $hook->name) }}"
                                                class="btn-sm btn-{{ $hook->enabled ? 'danger' : 'primary' }} pull-right edit">
                                                <i class="voyager-edit"></i> {{ $hook->enabled ? 'Disable' : 'Enable' }}
                                            </a>
                                        @else
                                            <a href="javascript:;" class="btn-sm btn-primary pull-right install"
                                                data-id="{{ $hook->name }}" id="install-{{ $hook->name }}">
                                                <i class="voyager-plus"></i> Install
                                            </a>
                                        @endif

                                        @if ($hook->hasUpdateAvailable())
                                            <a href="{{ route('voyager.hooks.update', $hook->name) }}"
                                                class="btn-sm btn-warning pull-right update">
                                                <i class="voyager-edit"></i> Update
                                            </a>
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
    </div>

    <div class="modal modal-danger fade" tabindex="-1" id="uninstall_modal" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">
                        <i class="voyager-trash"></i>
                        Are you sure you want to uninstall this hook?
                    </h4>
                </div>
                <div class="modal-footer">
                    <form action="{{ route('voyager.hooks') }}" id="uninstall_form" method="POST">
                        {{ method_field("DELETE") }}
                        {{ csrf_field() }}
                        <input type="submit" class="btn btn-danger pull-right uninstall-confirm"
                               value="Yes, Uninstall This Hook">
                    </form>
                    <button type="button" class="btn btn-default pull-right" data-dismiss="modal">Cancel</button>
                </div>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->

    <div class="modal modal-success fade" tabindex="-1" id="install_modal" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title"><i class="voyager-plus"></i> Installing hook</h4>
                </div>
                <div class="modal-body">
                    <p>Please be patience, installation in process...</p>
                </div>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->
@stop

@section('javascript')
    <!-- DataTables -->
    <script>
        $(document).ready(function () {
            $('#dataTable').DataTable({ "order": [] });
        });

        /**
         * Install Hook
         */
        $('td').on('click', '.install', function (e) {
            $('#install_modal').modal('show');
        });

        /**
         * Install Hook
         */
        $('td').on('click', '.uninstall', function (e) {
            var form = $('#uninstall_form')[0];

            form.action = parseActionUrl(form.action, $(this).data('id'));

            $('#uninstall_modal').modal('show');
        });

        function parseActionUrl(action, id) {
            return action.match(/\/[0-9]+$/)
                    ? action.replace(/([0-9]+$)/, id)
                    : action + '/' + id;
        }
    </script>
@stop

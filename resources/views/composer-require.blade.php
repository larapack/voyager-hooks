@extends('voyager::master')

@section('page_header')
    <div class="page-title">
        <i class="voyager-skull"></i> Error
    </div>
@stop

@section('page_header_actions')

@stop

@section('content')
    <div class="page-content container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-bordered">
                    <div class="panel-body">

                        <div style="border-radius:4px; padding:20px; margin:0; text-align:center; color: black;">
                            <p>To view this page you'll need to add the composer package.</p>
                            <p><code>composer require {{ $package }}</code></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop

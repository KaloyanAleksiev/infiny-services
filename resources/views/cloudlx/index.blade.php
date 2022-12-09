@extends('master')

@section('body')
    <div class="container">
        <div class="d-flex justify-content-center">
            <div class="col-md-12">
                <div class="h1 text-center">{{config('app.name')}}</div>
                @isset($services)
                    @if(!empty($services))
                        <cloud-l-x-service :services="{{ json_encode($services['services']) }}"></cloud-l-x-service>
                    @else
                        <div class="h3 text-center">There are no services to load</div>
                    @endif
                @else
                    <div class="h3 text-center">Unable to load the list of services</div>
                @endisset
            </div>
        </div>
    </div>
@endsection

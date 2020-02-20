@extends('site.layouts.base')
@section('title',$item->title . ' | '. $site->title)

@section('content')
    <section id="pageTop" class="w-100 bg-img" style="background-image: url('images/pageTop.jpg');">
        <div class="primary-overlay"></div>
        <div class="container">
            <h1>{{ title_case($item->title) }}</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('homeView') }}">{{ __('lang.home') }}</a></li>
                    <li class="breadcrumb-item active" aria-current="page">{{ $item->title }}</li>
                </ol>
            </nav>
        </div>
    </section>

    <section class="page-content">
        <div class="container pt-4 pb-4">
            <h4>{{ $item->title }}</h4>
            {!! $item->desc !!}
        </div>
    </section>
@endsection


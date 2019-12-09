@extends('layouts.app')

@section('content')
<div class="container">
    <lists v-bind:user="{{$user}}"/>
</div>
@endsection

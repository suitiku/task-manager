@extends('layouts.app')

@section('content')
<div class="container">
    <home v-bind:user="{{$user}}"/>
</div>
@endsection

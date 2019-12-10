@extends('layouts.app')

@section('content')
<div class="container">
    <content-wrapper v-bind:user="{{$user}}"/>
</div>
@endsection

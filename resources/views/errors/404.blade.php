{{-- @extends('errors::minimal') --}}
@extends('errors.minimal')

@section('title', __('Not Found'))
@section('code', '404')
@section('message', $message ?? __('Not Found'))

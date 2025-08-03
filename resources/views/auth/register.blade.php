<x-guest-layout>
    <form method="POST" action="{{  route('register') }}">
        @csrf
        <div class="box register-box" style="width"
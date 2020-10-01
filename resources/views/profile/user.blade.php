<x-app-layout>
    <x-slot name="header">
        <dashboard-header title="{{$user->name}}: user" csrf="{{ csrf_token() }}"></dashboard-header>
    </x-slot>
    <user :statuses="{{ $statuses }}" :user="{{ $user }}"></user>
</x-app-layout>

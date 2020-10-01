<x-app-layout>
    <x-slot name="header">
        <dashboard-header title="{{$user->name}}: manager" csrf="{{ csrf_token() }}"></dashboard-header>
    </x-slot>
    <manager :users="{{ $users }}"></manager>
</x-app-layout>

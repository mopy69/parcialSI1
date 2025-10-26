@extends('layouts.admin')

@section('content')
<div class="mb-6">
    <h1 class="text-2xl font-semibold text-gray-900">Admin Dashboard</h1>
</div>

<div class="grid grid-cols-1 md:grid-cols-3 gap-6">
    <div class="bg-white overflow-hidden shadow rounded-lg">
        <div class="px-4 py-5 sm:p-6">
            <dt class="text-sm font-medium text-gray-500 truncate">Total Users</dt>
            <dd class="mt-1 text-3xl font-semibold text-gray-900">{{ $usersCount }}</dd>
        </div>
    </div>

    <div class="bg-white overflow-hidden shadow rounded-lg">
        <div class="px-4 py-5 sm:p-6">
            <dt class="text-sm font-medium text-gray-500 truncate">Total Roles</dt>
            <dd class="mt-1 text-3xl font-semibold text-gray-900">{{ $rolesCount }}</dd>
        </div>
    </div>

    <div class="bg-white overflow-hidden shadow rounded-lg">
        <div class="px-4 py-5 sm:p-6">
            <dt class="text-sm font-medium text-gray-500 truncate">Total Permissions</dt>
            <dd class="mt-1 text-3xl font-semibold text-gray-900">{{ $permissionsCount }}</dd>
        </div>
    </div>
</div>
@endsection

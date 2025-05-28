<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Laravel</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />

        <!-- Toastr CSS -->
        <link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" rel="stylesheet" />

        <!-- Styles / Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <!-- jQuery + Toastr JS -->
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    </head>
    <body class="bg-gray-100 text-gray-800">
        <header class="bg-white shadow">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-center h-16 items-center">
                    <div class="flex-shrink-0">
                        <a href="/" class="text-2xl font-bold text-indigo-600">
                            Healthify
                        </a>
                    </div>
                </div>
            </div>
        </header>

        <div class="flex justify-center mt-12">
            <form method="POST" action="{{ route('activities.recalculate') }}">
                @csrf
                <button type="submit" class="bg-green-600 text-white px-6 py-2 rounded hover:bg-green-700 font-semibold" id="recalculate-btn">
                Recalculate
                </button>
            </form>
        </div>
        <main class="mt-8 flex justify-center">
            <div class="bg-white shadow rounded-lg p-8 w-full max-w-4xl">
            <div class="flex flex-wrap gap-4 mb-6 justify-between items-center">
                <form method="GET" action="{{ route('activities.filter') }}" class="w-full max-w-5xl mx-auto mt-6">
                    <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4">
                        
                        <!-- Select Group -->
                        <div class="flex flex-wrap justify-center gap-2 w-full sm:w-auto">
                            <select name="month" class="border rounded px-3 py-2" id="filter-month">
                                <option value="">Month</option>
                                @foreach(range(1,12) as $m)
                                    <option value="{{ $m }}" {{ request('month') == $m ? 'selected' : '' }}>
                                        {{ DateTime::createFromFormat('!m', $m)->format('F') }}
                                    </option>
                                @endforeach
                            </select>

                            <select name="year" class="border rounded px-3 py-2" id="filter-year">
                                <option value="">Year</option>
                                @for($y = now()->year; $y >= 2020; $y--)
                                    <option value="{{ $y }}" {{ request('year') == $y ? 'selected' : '' }}>{{ $y }}</option>
                                @endfor
                            </select>

                            <select name="day" class="border rounded px-3 py-2" id="filter-day">
                                <option value="">Day</option>
                                @foreach(range(1,31) as $d)
                                    <option value="{{ $d }}" {{ request('day') == $d ? 'selected' : '' }}>{{ $d }}</option>
                                @endforeach
                            </select>

                            <select name="user_id" id="filter-user" class="border rounded px-3 py-2">
                                <option value="">Select user</option>
                                @foreach($users as $id => $name)
                                    <option value="{{ $id }}" {{ request('user_id') == $id ? 'selected' : '' }}>{{ $name }}</option>
                                @endforeach
                            </select>
                            {{-- <input type="text" name="user_name" value="{{ request('user_name') }}" placeholder="Search name" class="border rounded px-3 py-2" id="filter-search-name"> --}}
                        </div>

                        <!-- Buttons Group -->
                        <div class="flex gap-2 justify-center sm:justify-end">
                            <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700" id="filter-btn">Filter</button>

                            @if(request()->filled(['month', 'year', 'day', 'user_id', 'user_name']))
                                <a href="{{ route('activities.filter') }}" class="bg-gray-300 text-gray-800 px-4 py-2 rounded hover:bg-gray-400 inline-flex items-center" id="clear-filter-btn">
                                    Clear Filter
                                </a>
                            @elseif(request()->filled('month') || request()->filled('year') || request()->filled('day') || request()->filled('user_id') || request()->filled('user_name'))
                                <a href="{{ route('activities.index') }}" class="bg-gray-300 text-gray-800 px-4 py-2 rounded hover:bg-gray-400 inline-flex items-center" id="clear-filter-btn">
                                    Clear Filter
                                </a>
                            @endif
                        </div>

                    </div>
                </form>

            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full bg-white border rounded">
                <thead>
                    <tr>
                    <th class="px-4 py-2 border-b text-center">ID</th>
                    <th class="px-4 py-2 border-b text-center">User Name</th>
                    <th class="px-4 py-2 border-b text-center">Rank</th>
                    <th class="px-4 py-2 border-b text-center">Points</th>
                    <th class="px-4 py-2 border-b text-center">Date</th>
                    </tr>
                </thead>
                <tbody id="activity-table-body">
                    @forelse ($activities as $key => $activity)
                    @php
                        // dd($activity);
                    @endphp
                    <tr>
                        <td class="px-4 py-2 border-b text-center">{{ $activities->firstItem() + $key }}</td>
                        <td class="px-4 py-2 border-b text-center">{{ $activity->user->name }}</td>
                        <td class="px-4 py-2 border-b text-center">{{ $activity->rank }}</td>
                        <td class="px-4 py-2 border-b text-center">{{ $activity->total_points }}</td>
                        <td class="px-4 py-2 border-b text-center">
                                {{ \Carbon\Carbon::parse($activity->last_activity_date)->format('Y-m-d') }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-4 py-2 border-b text-center text-gray-500">No activities found</td>
                    </tr>
                    @endforelse
                </tbody>
                </table>
                <div class="mt-4">
                {{ $activities->onEachSide(5)->links() }}
                </div>
            </div>
            </div>
        </main>
        <script>
            toastr.options = {
                "closeButton": true,
                "progressBar": true,
                "timeOut": "5000",
                "positionClass": "toast-top-right"
            };
            @if (session('success'))
                toastr.success("{{ session('success') }}");
            @elseif (session('error'))
                toastr.error("{{ session('error') }}");
            @elseif (session('warning'))
                toastr.warning("{{ session('warning') }}");
            @elseif (session('info'))
                toastr.info("{{ session('info') }}");
            @endif
        </script>

    </body>
</html>

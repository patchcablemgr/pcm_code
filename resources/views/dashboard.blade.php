<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

	<div class="m-2 grid gap-2 grid-rows-1">
		<div class="grid gap-2 grid-cols-1">
			<div class="p-2 bg-white">
				<p class="text-lg font-bold">Cable Utilization</p>
				<div class="grid gap-2 grid-cols-{{ $media->count() }}">
				@foreach ($media as $mediaEntry)
					<div>
					<canvas class="chartCblUtil" data-media-id="{{$mediaEntry->id}}" data-media-name="{{$mediaEntry->name}}"></canvas>
					</div>
				@endforeach
				</div>
			</div>
		</div>
	</div>
	
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    You're logged in!
                </div>
            </div>
        </div>
    </div>
	
	<script type="text/javascript" src="{{ asset('js/page.dashboard.js') }}"></script>
	<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</x-app-layout>

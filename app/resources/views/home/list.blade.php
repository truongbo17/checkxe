<div class="relative bg-gray-50 pt-8 mt-6 pb-10 px-4 sm:px-6 lg:pt-16 lg:pb-14 lg:px-8">
    <div class="absolute inset-0">
        <div class="h-1/3 sm:h-2/3"></div>
    </div>
    <div class="relative max-w-7xl mx-auto">
        <div class="text-center">
            <h2 class="text-3xl tracking-tight font-extrabold text-gray-900 sm:text-4xl">
                Dữ liệu mới nhất
            </h2>
        </div>
        <div class="mt-12 max-w-lg mx-auto grid gap-5 lg:grid-cols-4 lg:max-w-none">
            @foreach($car_news as $car_new)
                <div class="flex flex-col rounded-lg shadow-lg overflow-hidden">
                <div class="flex-shrink-0">
                    <img class="h-48 w-full object-cover" src="{{ \Illuminate\Support\Arr::first($car_new->public_url) }}" alt="{{ $car_new->license_plates }}">
                </div>
                <div class="flex-1 bg-white p-6 flex flex-col justify-between">
                    <div class="flex-1">
                        <p class="text-sm font-medium text-indigo-600">
                            <a href="#" class="hover:underline">
                                {{ $car_new->license_plates }}
                            </a>
                        </p>
                        <a href="#" class="block mt-2">
                            <p class="text-xl font-semibold text-gray-900">
                                {{ $car_new->description }}
                            </p>
                        </a>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>

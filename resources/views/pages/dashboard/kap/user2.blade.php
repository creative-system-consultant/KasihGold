<div class="col-span-12 sm:col-span-12 md:col-span-12 lg:col-span-4 xxl:col-span-4">
    <x-stacked.stacked-list title="Announcements" class="mb-6">
        @forelse ($announcement as $item)
            <li class="px-2 py-5 bg-white hover:bg-gray-100"  x-data="{ modalOpen: false}">
                <div class="relative focus-within:ring-2 focus-within:ring-indigo-500" @click="modalOpen = true">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-sm font-semibold text-gray-800" >
                                <a href="#" class="hover:underline focus:outline-none">
                                    <span class="absolute inset-0" aria-hidden="true"></span>
                                    {{ strtoupper($item->title) }}
                                </a>
                            </h3>
                        </div>
                        @if ($item->created_at->format('Y-m-d') == now()->format('Y-m-d'))
                            <div>
                                <span class="inline-flex items-center px-3 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">NEW</span>
                            </div>
                        @endif
                    </div>
                    <p class="mt-3 text-sm text-gray-600 truncate line-clamp-2">
                        {{ $item->description }}
                    </p>
                </div>
                <!-- Start modal details -->
                <x-general.modal2 modalActive="modalOpen" title="{{ strtoupper($item->title) }}" modalSize="xl" headerbg="blue-500" closeBtn="yes">
                    <x-slot name="icon">
                        <x-heroicon-s-information-circle class="w-8 h-8 mr-1" />
                    </x-slot>
                    
                    <div class="p-6">
                        <div class="pb-2 text-base font-semibold">
                            <p class="flex items-center text-blue-500">
                                <x-heroicon-o-calendar class="w-6 h-6 mr-2" />
                                {{ $item->created_at->format('d F Y') }}
                            </p>
                        </div>
                        @if($item->announcement_img)
                            <div class="mb-4">
                                <img src="{{ asset( $item->announcement_img) }}" alt="{{ $item->title }}" class="max-w-full h-auto rounded-lg shadow-md">
                            </div>
                        @endif
                        <p class="text-base text-gray-600">{{ $item->description }}</p>
                    </div>
                </x-general.modal2>
                <!-- End modal details -->
            </li>
        @empty

        @endforelse

        <x-slot name="button">
            {{-- <div class="mt-6">
                <a href="{{route('all-news')}}"
                    class="flex items-center justify-center w-full px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm focus:outline-none hover:bg-blue-500 hover:text-white">
                    View all
                </a>
            </div> --}}
        </x-slot>
    </x-stacked.stacked-list>
</div>
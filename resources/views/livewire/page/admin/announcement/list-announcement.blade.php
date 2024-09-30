<div>
    <div>
        <div class="flex flex-col items-center my-8 intro-y sm:flex-row">
            <h2 class="mr-auto text-lg font-medium">
                Anouncement
            </h2>
            @if (session('error'))
                <x-toaster.error title="{{ session('title') }}" message="{{ session('message') }}"/>
            @elseif (session('info'))
                <x-toaster.info title="{{ session('title') }}" message="{{ session('message') }}"/>
            @elseif (session('success'))
                <x-toaster.success title="{{ session('title') }}" message="{{ session('message') }}"/>
            @elseif (session('warning'))
                <x-toaster.warning title="{{ session('title') }}" message="{{ session('message') }}"/>
            @endif
        </div>
        <div class="mb-20 bg-white sm:mb-0">
            <div class="col-span-12 lg:col-span-12 xxl:col-span-12">
                <div class="grid grid-cols-12 gap-6">
                    <div class="col-span-12 lg:col-span-12 xxl:col-span-12">
                        <div class="flex flex-col items-start justify-between mt-8 mb-3 sm:items-center intro-y sm:flex-row">
                            <div class="mt-3">
                                <a href="{{ route('admin.create-announcements') }}"
                                    class="flex items-center px-4 py-2 font-semibold text-white bg-green-500 rounded-lg hover:bg-green-600 focus:outline-none">
                                    <x-heroicon-o-plus-circle class="w-6 h-6 mr-2" />
                                    <p>Create Announcement</p>
                                </a>
                            </div>
                            <div wire:loading>
                                <div class="absolute flex items-center justify-center p-4 text-white bg-yellow-400 rounded"
                                    style="left: 50%; top:50%">
                                    <x-heroicon-o-cog class="-ml-0.5 mr-2 h-8 w-8 animate-spin" />
                                    <p class="text-sm">Waiting<span class="animate-ping">...</span></p>
                                </div>
                            </div>
                            <div class="mt-3">
                                <x-form.search-input placeholder="Search by title" wire:model="search"/>
                            </div>
                        </div>
                        <x-table.table>
                            <x-slot name="thead">
                                <x-table.table-header class="text-left" value="No" sort="" />
                                <x-table.table-header class="text-left" value="Title" sort="" />
                                <x-table.table-header class="text-left" value="Anouncement detail" sort="" />
                                <x-table.table-header class="text-left" value="Created At" sort="" />
                                <x-table.table-header class="text-left" value="Action" sort="" />
                            </x-slot>
                            <x-slot name="tbody">
                                @php $i = 1 + $list->currentPage() * $list->perPage() - $list->perPage(); @endphp
                                @forelse ($list as $index => $item)
                                    <tr>
                                        <x-table.table-body colspan="" class="text-xs font-medium text-gray-700 ">
                                            <p>{{ $i++ }}</p>
                                        </x-table.table-body>
                                        <x-table.table-body colspan="" class="text-xs font-medium text-gray-700 ">
                                            <p>{{ strtoupper($item->title) }}</p>
                                        </x-table.table-body>
                                        <x-table.table-body colspan="" class="text-xs font-medium text-gray-700 ">
                                            <p class="w-40 truncate">{{ $item->description }}</p>
                                        </x-table.table-body>
                                        <x-table.table-body colspan="" class="text-xs font-medium text-gray-700 ">
                                            <p>{{ $item->created_at->format('d F Y') }}</p>
                                        </x-table.table-body>
                                        <x-table.table-body colspan="" class="text-xs font-medium text-gray-700 ">
                                            <div class="flex space-x-2" x-data="{ modalOpen: false}">
                                                <x-heroicon-o-eye class="w-5 h-5 mr-1 text-indigo-500 cursor-pointer tooltipbtn" data-title="View" data-placement="top" @click="modalOpen = true"/>
                                                <x-heroicon-o-trash class="w-5 h-5 mr-1 text-red-600 cursor-pointer tooltipbtn" data-id="{{ $item->id }}" onclick="deleteConfirmation({{ $item->id }})" data-title="Delete Announcement" data-placement="top"/>
                                                <x-popup.delete-announcement name="deleteConfirmation" variable="id" posturl="{{ url('/admin/list-announcements') }}/"  />

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
                                                        <p class="text-base text-gray-600 whitespace-pre-line ">
                                                            {{ $item->description }}
                                                        </p>
                                                    </div>
                                                </x-general.modal2>
                                                <!-- End modal details -->
                                            </div>
                                        </x-table.table-body>
                                    </tr>
                                @empty
                                    <tr>
                                        <x-table.table-body colspan="4" class="text-xs font-medium text-gray-700 ">
                                            <p>NO DATA</p>
                                        </x-table.table-body>
                                    </tr>
                                @endforelse
                            </x-slot>
                            <div class="px-2 py-2">
                                {{ $list->links('pagination-links') }}
                            </div>
                        </x-table.table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

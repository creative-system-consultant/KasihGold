<x-general.card class="px-4 pb-8 bg-white" x-data="{OpenScreening:false}">

    <div class="grid grid-cols-12 gap-6 mt-2 mb-20 sm:mb-0">
        <div class="flex flex-wrap items-center justify-between col-span-12 mt-2 intro-y sm:flex-no-wrap">
            {{-- <div class="flex items-center">
                <div class="relative dropdown" x-data="{open: false}">
                    <button class="flex px-4 py-1 text-sm font-bold text-white bg-yellow-400 rounded cursor-pointer focus:outline-none" @click="open = !open">Actions</button>
                    <div class="absolute z-10 w-40 bg-white rounded-lg shadow-lg" x-show="open" style="display: none; top: -17px; left: 90px;">
                        <div class="py-4">
                            <a href="" class="flex items-center p-2 transition duration-300 ease-in-out bg-white rounded-md hover:bg-gray-200">
                            <x-heroicon-o-document-text class="w-5 h-5 mr-1"/> Export to Excel
                            </a>
                            <a href="" class="flex items-center p-2 transition duration-300 ease-in-out bg-white rounded-md hover:bg-gray-200">
                            <x-heroicon-o-document-text class="w-5 h-5 mr-1"/> Export to PDF
                            </a>
                        </div>
                    </div>
                </div>
            </div> --}}
            <div class="w-full mt-3 sm:w-auto sm:mt-0 sm:ml-auto md:ml-0">
                <x-form.search-input />
            </div>
        </div>

        {{-- Start Screening List --}}
        @forelse ($list as $lists)
        <div class="col-span-12 intro-y md:col-span-6">
            <div class="bg-gray-100 border-l-4 border-yellow-400 shadow-lg rounded-xl">
                <div class="flex flex-col items-center px-4 py-4 lg:flex-row lg:p-5">
                    <div class="w-16 lg:h-16 image-fit lg:mr-1">
                        <img alt="avatar" class="border-4 border-white rounded-full"
                            src="https://image.flaticon.com/icons/png/512/149/149071.png">
                    </div>
                    <div class="flex justify-between w-full">
                        <div class="mt-3 ml-2 text-sm text-left lg:mr-auto lg:mt-0">
                            <p class="text-base font-semibold">{{ $lists->name }}</p>
                            <p>{{ $lists->email }}</p>
                            <p>{{ $lists->created_at->format('d F Y') }}</p>
                        </div>
                        <div class="mt-4">
                            <button
                                class="flex px-4 py-1 text-sm font-bold text-white bg-yellow-400 rounded cursor-pointer hover:bg-yellow-300"
                                wire:click="" x-on:click="OpenScreening = true">
                                Screening
                            </button>
                            {{-- Start modal Screening --}}
                            <x-general.modal modalActive="OpenScreening" title="Screening" modalSize="4xl"
                                closeBtn="yes">
                                <table class="w-full border border-gray-300 table-auto ">
                                    <tbody>
                                        @if ($lists->screening->count() == 11)
                                            <div class="flex justify-center space-x-3">
                                                <div class="px-4 mt-6 border-l-2 border-r-2">
                                                    <span class="text-base font-semibold ">Currently Screening:</span>
                                                    <p class="text-base font-semibold text-gray-500 ">11 Screenings</p>
                                                </div>
                                                <div class="px-4 mt-6 border-r-2">
                                                    <span class="text-base font-semibold ">Approved:</span>
                                                    <p class="text-base font-semibold text-gray-500 ">
                                                        {{ $lists->screening->where('status',1)->count() }} Screenings</p>
                                                </div>
                                                <div class="px-4 mt-6 border-r-2">
                                                    <span class="text-base font-semibold ">Declined:</span>
                                                    <p class="text-base font-semibold text-gray-500 ">
                                                        {{ $lists->screening->where('status',0)->count() }} Screenings</p>
                                                    @php
                                                    $failed = $lists->screening->where('status',0);
                                                    @endphp
                                                    @foreach ($failed as $fail)
                                                    <p class="text-base font-semibold text-gray-500 ">(
                                                        {{ $fail->sanction->name }} )</p>
                                                    @endforeach
                                                </div>
                                            </div>

                                            <div class="flex items-center justify-center mt-6">
                                                <button type="button"
                                                    class="flex items-center px-4 py-2 mx-2 text-sm text-white bg-green-500 rounded-lg hover:bg-green-600"
                                                    wire:click=finalResult({{ $lists->id }},'terima')>
                                                    <x-heroicon-s-check class="-ml-0.5 mr-2 h-4 w-4" />
                                                    Approve this user
                                                </button>

                                                <button type="button"
                                                    class="flex items-center px-4 py-2 mx-2 text-sm text-white bg-red-500 rounded-lg hover:bg-red-600"
                                                    wire:click=finalResult({{ $lists->id }},'tolak')>
                                                    <x-heroicon-s-trash class="-ml-0.5 mr-2 h-4 w-4" />
                                                    Decline this user
                                                </button>
                                            </div>
                                        @else
                                            <table class="w-full mt-5 table-fixed">
                                                <thead>
                                                    <tr>
                                                        <th class="px-4 py-2 border">Institution</th>
                                                        <th class="px-4 py-2 border">LInk</th>
                                                        <th class="px-4 py-2 border">Status</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach ($sanctionList as $item)
                                                    <tr>
                                                        <td class="px-4 py-2 text-left border">
                                                            <p class="break-words whitespace-normal">{{ $item->name }}</p>
                                                        </td>
                                                        <td class="px-4 py-2 text-left border">
                                                            <a href="{{ $item->website }}" target="_blank"
                                                                class="text-teal-600 break-words whitespace-normal transition duration-150 ease-in-out hover:text-teal-500 focus:outline-none focus:underline">{{ $item->website }}</a>
                                                        </td>
                                                        <td class="px-2 py-2 border lg:px-4">
                                                            @php
                                                            $status = $item->screening->where('user_id',
                                                            $lists->id)->first();
                                                            @endphp

                                                            @if ($status != null)
                                                            @if ($status->status == 1)
                                                            <div class="flex items-center justify-center">
                                                                <x-heroicon-o-check-circle
                                                                    class="w-5 h-5 mr-2 text-green-600 cursor-pointer" />
                                                                Approve
                                                            </div>
                                                            @else
                                                            <div class="flex items-center justify-center">
                                                                <x-heroicon-o-x-circle
                                                                    class="w-5 h-5 mr-2 text-red-600 cursor-pointer" />
                                                                Decline
                                                            </div>
                                                            @endif
                                                            @else
                                                            <button type="button"
                                                                class="px-4 py-2 mx-0 my-2 text-sm text-white bg-green-500 rounded-lg lg:my-0 lg:mx-2 hover:bg-green-600"
                                                                wire:click="screenResult({{ $lists->id }},{{ $item->id }},'pass')">
                                                                Approve
                                                            </button>

                                                            <button type="button"
                                                                class="px-4 py-2 mx-0 text-sm text-white bg-red-500 rounded-lg lg:mx-2 hover:bg-red-600 "
                                                                wire:click="screenResult({{ $lists->id }},{{ $item->id }},'fail')">
                                                                Decline
                                                            </button>
                                                            @endif
                                                        </td>
                                                    </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        @endif
                                    </tbody>
                                </table>
                                <div wire:loading>
                                    @include('misc.loading')
                                </div>
                            </x-general.modal>
                            {{-- End Modal Screening --}}
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @empty
        <div class="col-span-12 px-2 py-4 border-2 border-gray-200 intro-y md:col-span-12">
            <div class="flex justify-center">
                <p class="font-semibold">No data at the moment</p>
            </div>
        </div>
        @endforelse
    </div>
    {{-- End Screening List --}}
</x-general.card>
<div>
    <div>
        <div class="flex flex-col items-center mt-8 intro-y sm:flex-row">
            <h2 class="mr-auto text-lg font-medium">
                My Agents Listing
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

        <div class="p-4 mt-8 mb-20 bg-white sm:mb-0">
            <div class="flex justify-between my-4">
                {{-- <div wire:loading>
                    <div class="absolute flex items-center justify-center p-4 text-white bg-yellow-400 rounded"
                        style="left: 50%; top:50%">
                        <x-heroicon-o-cog class="-ml-0.5 mr-2 h-8 w-8 animate-spin" />
                        <p class="text-sm">Waiting<span class="animate-ping">...</span></p>
                    </div>
                </div> --}}
                {{-- <div class="flex items-center">
                    <x-form.search-input />
                </div> --}}
            </div>
            <x-table.table>
                <x-slot name="thead">
                    <x-table.table-header class="text-left" value="No." sort="" />
                    <x-table.table-header class="text-left" value="Name" sort="" />
                    <x-table.table-header class="text-left" value="Email" sort="" />
                    <x-table.table-header class="text-left" value="Contact No." sort="" />
                    @if (auth()->user()->role != 1)
                        <x-table.table-header class="text-left" value="Membership ID" sort="" />
                    @else
                        <x-table.table-header class="text-left" value="Referral Code" sort="" />
                    @endif
                    @if (auth()->user()->financing_role == 1)
                        <x-table.table-header class="text-left" value="Gold Summary" sort="" />
                        <x-table.table-header class="text-left" value="Actions" sort="" />
                    @endif
                </x-slot>
                <x-slot name="tbody">
                    @forelse ($activeUser as $index => $lists)
                        @if($lists->user != NULL)
                            <tr wire:key="lists-{{ $lists->id }}">
                                <x-table.table-body colspan="" class="text-xs font-medium text-gray-700 ">
                                    <p>{{ $activeUser->firstItem() + $index }}</p>
                                </x-table.table-body>
                                <x-table.table-body colspan="" class="text-xs font-medium text-gray-700 ">
                                    <p>{{ $lists->user->name }}</p>
                                </x-table.table-body>
                                <x-table.table-body colspan="" class="text-xs font-medium text-gray-700 ">
                                    <p>{{ $lists->user->email }}</p>
                                </x-table.table-body>
                                <x-table.table-body colspan="" class="text-xs font-medium text-gray-700 ">
                                    @if($lists->user->role == 3)
                                        <p>{{ $lists->user->profile->phone1 }}</p>
                                    @elseif ($lists->user->role == 4)
                                        <p>{{ $lists->user->phone_no }}</p>
                                    @endif
                                </x-table.table-body>
                                <x-table.table-body colspan="" class="text-xs font-medium text-gray-700 ">
                                    @if (auth()->user()->role != 1)
                                        <p>{{ $lists->user->profile->membership_id }}</p>
                                    @else
                                        @if ($lists->user->referralCode == NULL)
                                            <div class="flex">
                                                <button wire:click="generate({{ $lists->user->id }})" class="flex items-center justify-center px-2 py-2 text-white bg-green-400 rounded-lg hover:bg-green-300">
                                                    <x-heroicon-o-key class="w-6 h-6 "/>
                                                    <p class="ml-2 font-bold">Generate</p>
                                                </button>
                                            </div>
                                        @else
                                            <p>{{ $lists->user->referralCode->referral_code }}</p>
                                        @endif
                                    @endif
                                </x-table.table-body>
                                @if (auth()->user()->financing_role == 1)
                                    <x-table.table-body colspan="" class="text-xs font-medium text-gray-700 ">
                                        <div class="flex">
                                            <a href="{{ route('purchase-history', ['userId' => $lists->user->id]) }}" class="flex items-center justify-center px-2 py-2 text-white bg-yellow-600 rounded-lg hover:bg-yellow-500">
                                                <x-heroicon-o-currency-dollar class="w-6 h-6"/>
                                                <p class="ml-2 font-bold">Detailed Gold Ownership</p>
                                            </a>
                                        </div>
                                    </x-table.table-body>
                                    <x-table.table-body colspan="" class="text-xs font-medium text-gray-700 ">
                                        <div class="flex">
                                            <button wire:click="buyForCustomer({{ $lists->user->id }})" class="flex items-center justify-center px-2 py-2 text-white bg-green-400 rounded-lg hover:bg-green-300">
                                                <x-heroicon-o-shopping-cart class="w-6 h-6 "/>
                                                <p class="ml-2 font-bold">Buy for this customer</p>
                                            </button>
                                        </div>
                                    </x-table.table-body>
                                @endif
                            </tr>
                        @endif
                    @empty
                        <tr>
                            <x-table.table-body colspan="4" class="text-center text-gray-500">
                                No New Request
                            </x-table.table-body>
                        </tr>
                    @endforelse
                </x-slot>
                <div class="px-2 py-2">
                    {{ $activeUser->links('pagination-links') }}
                </div>
            </x-table.table>
        </div>
    </div>
</div>
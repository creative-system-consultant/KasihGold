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
            </div>
            <x-table.table>
                <x-slot name="thead">
                    <x-table.table-header class="text-left" value="No" sort="" />
                    <x-table.table-header class="text-left" value="Name" sort="" />
                    <x-table.table-header class="text-left" value="Email" sort="" />
                    <x-table.table-header class="text-left" value="Contact No." sort="" />
                    @if (auth()->user()->role != 1)
                        <x-table.table-header class="text-left" value="Membership ID" sort="" />
                    @else
                        <x-table.table-header class="text-left" value="Referral Code" sort="" />
                    @endif
                    <x-table.table-header class="text-left" value="Financing Role" sort="" />
                    @if (auth()->user()->financing_role == 1)
                        <x-table.table-header class="text-left" value="Gold Summary" sort="" />
                        <x-table.table-header class="text-left" value="Actions" sort="" />
                    @endif
                </x-slot>
                <x-slot name="tbody">
                    @forelse ($activeUser as $index => $user)
                        <tr wire:key="user-{{ $user->id }}">
                            <x-table.table-body colspan="" class="text-xs font-medium text-gray-700 ">
                                    <p>{{ $activeUser->firstItem() + $index }}</p>
                            </x-table.table-body>
                            <x-table.table-body colspan="" class="text-xs font-medium text-gray-700 ">
                                <p>{{ $user->name }}</p>
                            </x-table.table-body>
                            <x-table.table-body colspan="" class="text-xs font-medium text-gray-700 ">
                                <p>{{ $user->email }}</p>
                            </x-table.table-body>
                            <x-table.table-body colspan="" class="text-xs font-medium text-gray-700 ">
                                @if($user->role == 3)
                                    <p>{{ $user->profile->phone1 ?? 'N/A' }}</p>
                                @elseif ($user->role == 4)
                                    <p>{{ $user->phone_no }}</p>
                                @endif
                            </x-table.table-body>
                            <x-table.table-body colspan="" class="text-xs font-medium text-gray-700 ">
                                @if (auth()->user()->role != 1)
                                    <p>{{ $user->profile->membership_id ?? 'N/A' }}</p>
                                @else
                                    <p>{{ $user->referralCode->referral_code ?? 'N/A' }}</p>
                                @endif
                            </x-table.table-body>
                            <x-table.table-body colspan="" class="text-xs font-medium text-gray-700 ">
                                <button 
                                    wire:click="confirmToggleFinancingRole({{ $user->id }})"
                                    class="px-2 py-1 text-white rounded-lg {{ $user->financing_role == 1 ? 'bg-green-500 hover:bg-green-600' : 'bg-gray-400 hover:bg-gray-500' }}"
                                >
                                    {{ $user->financing_role == 1 ? 'Active' : 'Inactive' }}
                                </button>
                            </x-table.table-body>
                            @if (auth()->user()->financing_role == 1)
                                <x-table.table-body colspan="" class="text-xs font-medium text-gray-700 ">
                                    <div class="flex"></div>
                                        <a href="{{ route('purchase-history', ['userId' => $user->id]) }}" class="flex items-center justify-center px-2 py-2 text-white bg-yellow-600 rounded-lg hover:bg-yellow-500">
                                            <x-heroicon-o-currency-dollar class="w-6 h-6"/>
                                            <p class="ml-2 font-bold">Detailed Gold Ownership</p>
                                        </a>
                                    </div>
                                </x-table.table-body>
                                <x-table.table-body colspan="" class="text-xs font-medium text-gray-700 ">
                                    <div class="flex">
                                        <button wire:click="buyForCustomer({{ $user->id }})" class="flex items-center justify-center px-2 py-2 text-white bg-green-400 rounded-lg hover:bg-green-300">
                                            <x-heroicon-o-shopping-cart class="w-6 h-6 "/>
                                            <p class="ml-2 font-bold">Buy for this customer</p>
                                        </button>
                                    </div>
                                </x-table.table-body>
                            @endif
                        </tr>
                    @empty
                        <tr>
                            <x-table.table-body colspan="7" class="text-center text-gray-500">
                                No Active Users Found
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

    <!-- Modal -->
    <div
        x-data="{ show: @entangle('confirmingUserId') }"
        x-show="show"
        x-cloak
        class="fixed inset-0 z-50 overflow-y-auto"
        aria-labelledby="modal-title"
        role="dialog"
        aria-modal="true"
    >
        <div class="flex items-end justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <div x-show="show" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75" aria-hidden="true"></div>

            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            <div x-show="show" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" class="inline-block px-4 pt-5 pb-4 overflow-hidden text-left align-bottom transition-all transform bg-white rounded-lg shadow-xl sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:p-6">
                <div class="sm:flex sm:items-start">
                    <div class="flex items-center justify-center flex-shrink-0 w-12 h-12 mx-auto bg-red-100 rounded-full sm:mx-0 sm:h-10 sm:w-10">
                        <svg class="w-6 h-6 text-red-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                    </div>
                    <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                        <h3 class="text-lg font-medium leading-6 text-gray-900" id="modal-title">
                            Toggle Financing Role
                        </h3>
                        <div class="mt-2">
                            <p class="text-sm text-gray-500">
                                Are you sure you want to change the financing role for this user? 
                            </p>
                        </div>
                    </div>
                </div>
                <div class="mt-5 sm:mt-4 sm:flex sm:flex-row-reverse">
                    <button wire:click="toggleFinancingRole" type="button" class="inline-flex justify-center w-full px-4 py-2 text-base font-medium text-white bg-red-600 border border-transparent rounded-md shadow-sm hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:ml-3 sm:w-auto sm:text-sm">
                        Confirm
                    </button>
                    <button @click="show = false" type="button" class="inline-flex justify-center w-full px-4 py-2 mt-3 text-base font-medium text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:w-auto sm:text-sm">
                        Cancel
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    [x-cloak] { display: none !important; }
</style>
@endpush
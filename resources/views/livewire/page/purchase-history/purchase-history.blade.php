<div>
    <div>
        <div class="flex flex-col items-center mt-8 intro-y sm:flex-row">
            <h2 class="mr-auto text-lg font-medium">
                Purchase History for {{ $user->name }}
            </h2>
        </div>

        <div class="p-4 mt-8 bg-white mb-20 sm:mb-0">
            @if($list->isEmpty())
                <p class="text-center text-gray-500">No purchase history found for this user.</p>
            @else
                <x-table.table>
                    <x-slot name="thead">
                        <x-table.table-header class="text-left" value="Reference Number" sort="" />
                        <x-table.table-header class="text-left" value="Total Weight (g)" sort="" />
                        <x-table.table-header class="text-left" value="Total Price (RM)" sort="" />
                        <x-table.table-header class="text-left" value="Purchase Date" sort="" />
                        <x-table.table-header class="text-left" value="Action" sort="" />
                    </x-slot>
                    <x-slot name="tbody">
                        @foreach ($list as $item)
                            <tr>
                                <x-table.table-body colspan="" class="text-xs font-medium text-gray-700">
                                    <p>
                                        <a href="https://dev.toyyibpay.com/{{$item->referenceNumber}}" 
                                           class="text-blue-600 hover:text-blue-800 hover:underline">
                                            Kasih Digital Gold (Total Gold {{ number_format($item->total_weight,2) }}g)
                                        </a>
                                    </p>
                                </x-table.table-body>
                                <x-table.table-body colspan="" class="text-xs font-medium text-gray-700">
                                    <p>{{ number_format($item->total_weight, 2) }}</p>
                                </x-table.table-body>
                                <x-table.table-body colspan="" class="text-xs font-medium text-gray-700">
                                    <p>{{ number_format($item->total_price, 2) }}</p>
                                </x-table.table-body>
                                <x-table.table-body colspan="" class="text-xs font-medium text-gray-700">
                                    <p>{{ \Carbon\Carbon::parse($item->purchase_date)->format('d F Y') }}</p>
                                </x-table.table-body>
                                <x-table.table-body colspan="" class="text-xs font-medium text-gray-700">
                                    @if($item->financing_flag == 1)
                                        <button wire:click="confirmFinancingChange('{{ $item->referenceNumber }}')" 
                                                class="px-3 py-1 text-sm font-medium text-blue-600 bg-blue-100 border border-blue-300 rounded-md hover:bg-blue-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition duration-150 ease-in-out">
                                            Settle Financing
                                        </button>
                                        <button wire:click="confirmDefault('{{ $item->referenceNumber }}')" 
                                                class="ml-2 px-3 py-1 text-sm font-medium text-red-600 bg-red-100 border border-red-300 rounded-md hover:bg-red-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition duration-150 ease-in-out">
                                            Default
                                        </button>
                                    @else
                                    @if($item->financing_flag == 0)
                                        <span class="px-3 py-1 text-sm font-medium text-green-600 bg-green-100 rounded-md">Settled/Released</span>
                                    @elseif($item->financing_flag == 2)
                                        <span class="px-3 py-1 text-sm font-medium text-yellow-600 bg-yellow-100 rounded-md">Pending Default</span>
                                    @elseif($item->financing_flag == 3)
                                        <span class="px-3 py-1 text-sm font-medium text-red-600 bg-red-100 rounded-md">Defaulted</span>
                                    @endif
                                    @endif
                                </x-table.table-body>
                            </tr>
                        @endforeach
                    </x-slot>
                </x-table.table>
                <div class="px-2 py-2">
                    {{ $list->links() }}
                </div>
            @endif
        </div>
    </div>

    <!-- Confirmation Modal -->
    @if($confirmingId)
    <div class="fixed inset-0 z-9999 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75" aria-hidden="true"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div class="inline-block overflow-hidden text-left align-bottom transition-all transform bg-white rounded-lg shadow-xl sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <div class="px-4 pt-5 pb-4 bg-white sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                            <h3 class="text-lg font-medium leading-6 text-gray-900" id="modal-title">
                                Confirm Financing Settlement
                            </h3>
                            <div class="mt-2">
                                <p class="text-sm text-gray-500">
                                    You are about to finalize the settlement of financing for this purchase. Please be advised that this action is irreversible.
                                    <br><br>
                                    Do you wish to proceed with the settlement?
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="px-4 py-3 bg-gray-50 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button wire:click="changeFinancingFlag('{{ $confirmingId }}')" type="button" class="inline-flex justify-center w-full px-4 py-2 text-base font-medium text-white bg-red-600 border border-transparent rounded-md shadow-sm hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:ml-3 sm:w-auto sm:text-sm">
                        Confirm
                    </button>
                    <button wire:click="$set('confirmingId', null)" type="button" class="inline-flex justify-center w-full px-4 py-2 mt-3 text-base font-medium text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                        Cancel
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- New Default Confirmation Modal -->
    @if($confirmingDefaultId)
    <div class="fixed inset-0 z-9999 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75" aria-hidden="true"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div class="inline-block overflow-hidden text-left align-bottom transition-all transform bg-white rounded-lg shadow-xl sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <div class="px-4 pt-5 pb-4 bg-white sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                            <h3 class="text-lg font-medium leading-6 text-gray-900" id="modal-title">
                                Confirm Default
                            </h3>
                            <div class="mt-2">
                                <p class="text-sm text-gray-500">
                                    Are you sure you want to default this purchase? Here's the calculation:
                                </p>
                                <ul class="mt-2 text-sm text-gray-600">
                                    <li>Available Weight: {{ number_format($selectedWeight, 2) }} grams</li>
                                    <li>Current Outright Price: RM{{ number_format($currentOutrightPrice, 2) }} per gram</li>
                                    <li>Total Sell Amount: RM{{ number_format($totalSellAmount, 2) }}</li>
                                </ul>
                                <p class="mt-2 text-sm text-gray-500">
                                    This action cannot be undone.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="px-4 py-3 bg-gray-50 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button wire:click="defaultPurchase('{{ $confirmingDefaultId }}')" type="button" class="inline-flex justify-center w-full px-4 py-2 text-base font-medium text-white bg-red-600 border border-transparent rounded-md shadow-sm hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:ml-3 sm:w-auto sm:text-sm">
                        Confirm
                    </button>
                    <button wire:click="$set('confirmingDefaultId', null)" type="button" class="inline-flex justify-center w-full px-4 py-2 mt-3 text-base font-medium text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                        Cancel
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>


@push('js')
<script>
    window.livewire.on('message', message => {
        Swal.fire({
            icon: message.type,
            title: message.message,
            showConfirmButton: false,
            timer: 2500
        });
    })
</script>
@endpush

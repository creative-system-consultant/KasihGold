<div>
    <div>
        <div class="flex flex-col items-center mt-8 intro-y sm:flex-row">
            <h2 class="mr-auto text-lg font-medium">
                Digital Gold Details
            </h2>
        </div>

        <div class="p-4 mt-8 bg-white mb-20 sm:mb-0">
            <x-general.grid mobile="1" gap="5" sm="1" md="1" lg="3" xl="3" class="col-span-6 mb-6">
                <x-general.price-card  class="bg-yellow-400 text-white rounded-lg">
                    <div class="font-bold text-base text-white">
                            <div class="flex space-x-4 items-center">
                                <div class="rounded-full py-4 px-4 flex item-center bg-white">
                                    <x-heroicon-o-clipboard-list class="w-8 h-8 text-yellow-400" />
                                </div>
                                <div class="text-lg">
                                    <p>Total Grams</p>
                                    <p class="text-base">{{$total}} g</p>
                                </div>
                            </div>
                        </div>
                </x-general.price-card>
                <x-general.price-card  class="bg-yellow-400 text-white rounded-lg">
                    <div class="font-bold text-base text-white">
                            <div class="flex space-x-4 items-center">
                                <div class="rounded-full py-4 px-4 flex item-center bg-white">
                                    <x-heroicon-o-clipboard-list class="w-8 h-8 text-yellow-400" />
                                </div>
                                <div class="text-lg">
                                    <p>Purchased Price</p>
                                    <p class="text-base">RM {{number_format($tPrice,2)}}</p>
                                </div>
                            </div>
                        </div>
                </x-general.price-card>
                <x-general.price-card  class="bg-yellow-400 text-white rounded-lg">
                    <div class="font-bold text-base text-white">
                            <div class="flex space-x-4 items-center">
                                <div class="rounded-full py-4 px-4 flex item-center bg-white">
                                    <x-heroicon-o-clipboard-list class="w-8 h-8 text-yellow-400" />
                                </div>
                                <div class="text-lg">
                                    <p>Digital Gold Ownership</p>
                                    <p class="text-base">Paid: {{$paidCount}} | Locked: {{$lockedCount}}</p>
                                </div>
                            </div>
                        </div>
                </x-general.price-card>
            </x-general.grid>
            <x-table.table>
                <x-slot name="thead">
                    <x-table.table-header class="text-left" value="No." sort="" />
                    <x-table.table-header class="text-left" value="Product Name" sort="" />
                    <x-table.table-header class="text-left" value="Unique Ownership ID" sort="" />
                    <x-table.table-header class="text-left" value="Weight" sort="" />
                    <x-table.table-header class="text-left" value="Bought Price" sort="" />
                    <x-table.table-header class="text-left" value="Purchased Date" sort="" />
                    <x-table.table-header class="text-left" value="Status" sort="" />
                    <x-table.table-header class="text-left" value="Ownership" sort="" />
                </x-slot>
                <x-slot name="tbody">
                @forelse ($details as $index => $item)   
                    <tr>
                        <x-table.table-body colspan="" class="text-xs font-medium text-gray-700 ">
                            <p>{{ $details->firstItem() + $index }}</p>
                        </x-table.table-body>
                        <x-table.table-body colspan="" class="text-xs font-medium text-gray-700 ">
                            <p>{{$item->products->prod_name}}</p>
                        </x-table.table-body>
                        <x-table.table-body colspan="" class="text-xs font-medium text-gray-700 ">
                            <p>{{$item->ouid}}</p>
                        </x-table.table-body>
                        <x-table.table-body colspan="" class="text-xs font-medium text-gray-700 ">
                            <p>{{number_format($item->weight,2)}} g</p>
                        </x-table.table-body>
                        <x-table.table-body colspan="" class="text-xs font-medium text-gray-700 ">
                            <p>RM {{number_format($item->bought_price,2)}}</p>
                        </x-table.table-body>
                        <x-table.table-body colspan="" class="text-xs font-medium text-gray-700 ">
                            <p>{{ $item->created_at->format('d F Y') }}</p>
                        </x-table.table-body>
                        <x-table.table-body colspan="" class="text-xs font-medium text-gray-700 ">
                            <p>{{ $item->financing_flag==0 ? 'Paid' : 'Locked' }}</p>
                        </x-table.table-body>
                        <x-table.table-body colspan="" class="text-xs font-medium text-gray-700 ">
                            <p>{{ $item->active_ownership ? 'Active' : 'Inactive' }}</p>
                        </x-table.table-body>
                    </tr>
                @empty 
                    <tr>
                        <x-table.table-body colspan="8" class="text-center text-gray-500">
                            No Digital Gold as of yet.
                        </x-table.table-body>
                    </tr>
                @endforelse
                </x-slot>
                <div class="px-2 py-2">
                    {{ $details->links('pagination-links') }}
                </div>
            </x-table.table>
        </div>
    </div>
</div>


<div>
    <div class="flex flex-col items-center mt-4 intro-y sm:flex-row">
        <h2 class="px-6 mr-auto text-lg font-medium">
            Purchase History
        </h2>
    </div>
    <div class="p-4 mt-4 bg-white">
        <x-table.table>
            <x-slot name="thead">
                <x-table.table-header class="text-left" value="Items" sort="" />
                <x-table.table-header class="text-left" value="Total Bill Amount (RM)" sort="" />
                <x-table.table-header class="text-left" value="Purchase Date" sort="" />
                <x-table.table-header class="text-left" value="Payment Status" sort="" />
            </x-slot>
            <x-slot name="tbody">
                @forelse ($history as $item)
                    @php $this->totalGrammage = 0;@endphp

                    @foreach ($item->golds as $gold)
                        @php $this->totalGrammage += round($gold->weight,2);@endphp
                    @endforeach
                    <tr>
                        <x-table.table-body colspan="" class="text-xs font-medium text-gray-700 ">
                            @if($item->status == 1)
                            <p><a href="https://dev.toyyibpay.com/{{$item->bill_code}}">Kasih Digital Gold (Total Gold {{ number_format($this->totalGrammage,2) }}g)</a></p>
                            @else
                            <p>Kasih Digital Gold (Total Gold {{ number_format($this->totalGrammage,2) }}g)</p>
                            @endif
                        </x-table.table-body>
                        <x-table.table-body colspan="" class="text-xs font-medium text-gray-700 ">
                            <p>RM {{number_format($item->bill_amount,2) }}</p>
                        </x-table.table-body>
                        <x-table.table-body colspan="" class="text-xs font-medium text-gray-700 ">
                            <p>{{ $item->created_at->format('d F Y') }}</p>
                        </x-table.table-body>
                        <x-table.table-body colspan="" class="text-xs font-medium text-gray-700 ">
                            <span class="inline-flex items-center px-3 py-0.5 rounded-full text-sm font-medium bg-{{ ($item->status == 1) ? 'green' : (($item->status == 2) ? 'yellow' : 'red')}}-100 text-{{ ($item->status == 1) ? 'green' : (($item->status == 2) ? 'yellow' : 'red')}}-800">{{ ($item->status == 1) ? 'Successful': (($item->status == 2) ? 'Pending' : 'Cancelled')}}</span>
                        </x-table.table-body>
                    </tr>
                @empty 
                    <tr>
                        <x-table.table-body colspan="4" class="text-center text-gray-500">
                            No Transaction History
                        </x-table.table-body>
                    </tr>
                @endforelse
            </x-slot>
            <div class="px-2 py-2">
                {{ $history->links('pagination::tailwind') }}
            </div>
        </x-table.table>
    </div>
</div>

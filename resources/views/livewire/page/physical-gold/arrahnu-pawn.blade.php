<div>
    <div>
        <div class="flex flex-col items-center mt-8 intro-y sm:flex-row">
            <h2 class="mr-auto text-lg font-medium">
                Arrahnu Pawn
            </h2>
        </div>
        @if (session('error'))
        <x-toaster.error title="{{ session('title') }}" message="{{ session('message') }}"/>
        @endif
        <div class="p-4 mt-8 mb-20 bg-white sm:mb-0">
            <x-general.grid mobile="1" gap="5" sm="4" md="4" lg="4" xl="4" class="col-span-6 mb-4">
                <x-general.price-card  class="text-white bg-yellow-400 rounded-lg">
                    <div class="text-base font-bold text-white">
                            <div class="flex items-center space-x-4">
                                <div class="flex px-4 py-4 bg-white rounded-full item-center">
                                    <x-heroicon-o-clipboard-list class="w-8 h-8 text-yellow-400" />
                                </div>
                                <div class="text-xl">
                                    <p>Total Gold Wallet</p>
                                    <p class="text-lg">{{$totalWallet}} g</p>
                                </div>
                            </div>
                        </div>
                </x-general.price-card>
                <x-general.price-card  class="bg-{{$gold_types ? 'yellow' : 'red'}}-400 text-white rounded-lg">
                    <div class="text-base font-bold text-white">
                            <div class="flex items-center space-x-4">
                                <div class="flex px-4 py-4 bg-white rounded-full item-center">
                                    <x-heroicon-o-clipboard-list class="w-8 h-8 text-{{$gold_types ? 'yellow' : 'red'}}-400" />
                                </div>
                                <div class="text-xl">
                                    @if($gold_types)
                                    <p>Today's Gold Price</p>
                                    <p class="text-lg">RM {{$goldprice['price']}} / g</p>
                                    @else
                                    <p>The daily gold price has not been determined</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                </x-general.price-card>
            </x-general.grid>

            <!--Start desktop view-->
            <div class="hidden lg:block">
                <x-table.table>
                    <x-slot name="thead">
                        <x-table.table-header class="text-left" value="Product" sort="" />
                        <x-table.table-header class="text-left" value="Total Grammage in Account" sort="" />
                        <x-table.table-header class="text-left" value="Grammage Apply (g)" sort="" />
                    </x-slot>
                    <x-slot name="tbody">

                        <tr>
                            <x-table.table-body colspan="" class="text-xs font-medium text-gray-700 ">
                                <div class="flex items-center space-x-3">
                                    <img class="object-cover w-16 h-16 rounded"
                                    src="{{ asset('img/product/1/9/d1.png') }}" alt="">
                                    <div>
                                        <h3 class="text-sm font-semibold ">Kasih AP Flexible Gold</h3>
                                    </div>
                                </div>
                            </x-table.table-body>

                            <x-table.table-body colspan="" class="text-xs font-medium text-gray-700 ">
                                <div class="flex items-center space-x-3">
                                    <div>
                                        <h3 class="text-sm font-semibold">{{$total}} g</h3>
                                    </div>
                                </div>
                            </x-table.table-body>

                            <x-table.table-body colspan="" class="text-xs font-medium text-gray-700 ">
                                <div class="relative flex flex-row w-24 h-10 mt-1 bg-transparent rounded-lg">
                                    <input type="text"
                                        id="GoldMintGram"
                                        class="focus:outline-none text-center w-full bg-gray-300 font-semibold text-md
                                        hover:text-black focus:text-black  md:text-basecursor-default flex items-center
                                        justify-center
                                        text-gray-700
                                        outline-none
                                        @error('GoldMintGram') border-red-300 text-red-900 placeholder-red-300 focus:border-red-300 focus:shadow-outline-red @enderror"
                                        name="custom-input-number" wire:model="GoldMintGram" >

                                </div>
                            </x-table.table-body>
                        </tr>
                        <tr>
                            <x-table.table-body colspan="" class="text-xs font-medium text-gray-700 ">
                                <div class="flex items-center space-x-3">
                                    <img class="object-cover w-16 h-16 rounded"
                                    src="{{ asset('img/product/1/9/d1.png') }}" alt="">
                                    <div>
                                        <h3 class="text-sm font-semibold">Kasih AP Digital Gold</h3>
                                    </div>
                                </div>
                            </x-table.table-body>

                            <x-table.table-body colspan="" class="text-xs font-medium text-gray-700 ">
                                <div class="flex items-center space-x-3">
                                    <div>
                                        <h3 class="text-sm font-semibold">{{$totalD}} g</h3>
                                    </div>
                                </div>
                            </x-table.table-body>

                            <x-table.table-body colspan="" class="text-xs font-medium text-gray-700 ">
                                <div class="relative flex flex-row w-24 h-10 mt-1 bg-transparent rounded-lg">
                                    <input type="text"
                                        id="GoldMintGramD"
                                        class="focus:outline-none text-center w-full bg-gray-300 font-semibold text-md
                                        hover:text-black focus:text-black  md:text-basecursor-default flex items-center
                                        justify-center
                                        text-gray-700
                                        outline-none
                                        @error('GoldMintGramD') border-red-300 text-red-900 placeholder-red-300 focus:border-red-300 focus:shadow-outline-red @enderror"
                                        name="custom-input-number" wire:model="GoldMintGramD"
                                        >

                                </div>
                            </x-table.table-body>
                        </tr>


                    </x-slot>
                    <div class="px-2 py-2">
                    </div>
                </x-table.table>
            </div>
            <!--End desktop view-->

            <!--Start Mobile view-->
            <div class="block lg:hidden">
                <div class="p-4 border-2 rounded-md">
                        <div class="py-2 border-b-2">
                            <div class="flex items-center justify-between">
                                <div>
                                    <img class="object-cover w-16 h-16 rounded"
                                    src="{{ asset('img/product/1/9/d1.png') }}" alt="">
                                    <h3 class="text-sm font-semibold">Kasih AP Gold Wafer</h3>
                                </div>
                                <div class="relative flex flex-row w-24 h-10 mt-1 bg-transparent rounded-lg">
                                    <input type="text"
                                        class="flex items-center justify-center w-full font-semibold text-center text-gray-700 bg-gray-300 outline-none focus:outline-none text-md hover:text-black focus:text-black md:text-basecursor-default"
                                        name="custom-input-number" value="" wire:model="GoldMintGram">

                                </div>
                            </div>
                        </div>
                </div>
            </div>
            <!--End Mobile view-->



            <! -- Start Checkout -->
            <x-general.grid mobile="1" gap="8" sm="1" md="2" lg="2" xl="2" class="w-full col-span-12 mt-6">

            {{-- @if($gold_types && !$startDay->isEmpty() && $this->getKotak()) --}}
            @if($gold_types && $flagStartDay == 1 && $flagKotak == 1)
                <div class="py-2 bg-white">
                    <div class="border rounded-lg shadow-sm">
                        <div class="overflow-hidden text-sm">
                            <table class="w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="p-4 text-left">Produk</th>
                                        <th class="p-4 text-right">Pembiayaan Maksima (RM)</th>
                                    </tr>
                                </thead>

                                <tbody>
                                    @if (isset($financing))
                                        @foreach ($financing as $row)
                                            <tr class="border-t">

                                                <td class="p-4 font-semibold ">
                                                    <input type="radio" id="prod_code" value="{{ $row['prod_code'] }}"
                                                    class="w-6 h-6 text-yellow-400 transition duration-150 ease-in-out border-2 border-yellow-400 cursor-pointer form-radio "
                                                    name="prod_code" wire:model="prod_code">
                                                    <span class="ml-2">{{ $row['name'] }}</span>
                                                </td>
                                                <td class="p-4 font-mono text-right">{{ $row['max_financing'] }}</td>
                                            </tr>
                                        @endforeach
                                    @else
                                        <tr class="border-t">
                                            <td class="p-4 font-semibold text-center" colspan="2">
                                                <p>No active product at the moment</p>
                                            </td>
                                        </tr>
                                    @endif

                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="px-4 py-2 bg-white border-2 rounded-lg">
                    <div class="py-4 border-b-2">
                        <h1 class="text-3xl font-semibold">Ar-Rahnu Pawn Brief Summary</h1>

                    </div>

                    <div class="flex justify-between py-4 border-b-2">
                        <div class="text-lg font-semibold">
                            <p>Total Collateral Grammage</p>
                        </div>
                        <div class="text-lg font-semibold">
                            @if (is_numeric($this->GoldMintGram) && is_numeric($this->GoldMintGramD))
                                <p>{{$GoldMintGram+$GoldMintGramD}} Gram</p>
                            @else
                                <p>0 Gram</p>
                            @endif
                        </div>
                    </div>



                    <div class="flex justify-center my-6">
                        <button type="button" class="w-full flex items-center justify-center px-2 py-2 text-sm font-bold text-white bg-{{$gold_types ? 'yellow' : 'gray'}}-400 rounded focus:outline-none hover:bg-{{$gold_types ? 'yellow' : 'gray'}}-500 " wire:click="next()" {{$gold_types ? '' : 'disabled'}}>
                            <p>Proceed to checkout</p>
                        </button>
                    </div>
                </div>
                @endif
            </x-general.grid>
            <! -- End Checkout -->
        </div>
    </div>
</div>


@push('js')
    <script>
        window.livewire.on('message', message => {
            alert(message);
        })
    </script>
@endpush

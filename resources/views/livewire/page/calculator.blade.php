<div x-data="{openCalculator : false}">
    <!-- Calculator Btn -->
    <button
        x-on:click="openCalculator = true"
        class="p-2 text-white transition-colors bg-indigo-500 rounded-lg shadow-md tooltipbtn hover:bg-indigo-400 hover:text-white focus:outline-none"
        data-title="Calculator" data-placement="right">
        <x-heroicon-o-calculator class="w-6 h-6" />
    </button>


    <!-- calculator section -->
    <section x-show="openCalculator" x-cloak >
        <div class="fixed inset-0 z-40 bg-black bg-opacity-50"></div>
        <div class="fixed top-0 right-0 z-50 w-full h-screen max-w-5xl mb-20 overflow-hidden transition duration-300 transform bg-gray-100 animate__animated animate__fadeInRight">
            <div class="flex items-center justify-between p-4 text-black bg-yellow-300">
                <div>
                    <h1 class="text-lg font-semibold ">Calculator</h1>
                </div>
                <div>
                    <div
                        @click="openCalculator = false"
                        class="p-2 bg-red-500 rounded-md cursor-pointer">
                        <x-heroicon-o-x class="w-4 h-4 text-white"/>
                    </div>
                </div>
            </div>

            <div class="h-full px-2 pb-64 overflow-y-auto">
                <div class="px-6 py-4 overflow-y-auto leading-4" >
                    <x-table.table>
                        <x-slot name="thead">
                            <th class="w-10 px-6 py-3 text-xs font-medium leading-4 tracking-wider text-left text-gray-500 uppercase bg-gray-50">
                                <p>Bil</p>
                            </th>
                            <th class = "px-6 py-3 text-xs font-medium leading-4 tracking-wider text-left text-gray-500 uppercase bg-gray-50">
                                <p>Berat (gram)</p>
                            </th>
                            <th class = "px-6 py-3 text-xs font-medium leading-4 tracking-wider text-right text-gray-500 uppercase bg-gray-50">
                                <p>Karat</p>
                            </th>
                            <th class = "px-6 py-3 text-xs font-medium leading-4 tracking-wider text-right text-gray-500 uppercase bg-gray-50">
                                <p>Harga (RM)</p>
                            </th>
                            <th class = "px-6 py-3 text-xs font-medium leading-4 tracking-wider text-right text-gray-500 uppercase bg-gray-50">
                                <p>Nilai Marhun (RM)</p>
                            </th>
                        </x-slot>
                        <x-slot name="tbody">
                            @forelse ($this->gold_types as $code => $gold)
                                <tr>
                                    <x-table.table-body colspan="" class="text-sm font-medium text-gray-700">
                                        {{ $loop->iteration }}
                                    </x-table.table-body>
                                    <x-table.table-body colspan="" class="text-sm font-medium text-gray-700">
                                        <x-form.input placeholder="" wire:model.lazy="marhun.{{ trim($code) }}.weight" value="marhun.{{ trim($code) }}.weight" label=""/>
                                    </x-table.table-body>
                                    <x-table.table-body colspan="" class="font-mono text-sm font-medium text-right text-gray-700">
                                        {{ Money($gold['carat']) }}K
                                    </x-table.table-body>
                                    <x-table.table-body colspan="" class="font-mono text-sm font-medium text-right text-gray-700">
                                        RM {{ Money($gold['price']) }}
                                    </x-table.table-body>
                                    <x-table.table-body colspan="" class="font-mono text-sm font-medium text-right text-gray-700">
                                        RM {{ Money($marhun[trim($code)]['total']) }}
                                    </x-table.table-body>
                                </tr>
                            @empty
                            <tr>
                                <x-table.table-body colspan="4" class="text-sm font-medium text-gray-700">
                                    Tiada data
                                </x-table.table-body>
                            </tr>
                            @endforelse
                        </x-slot>
                    </x-table.table>

                    <div class="mt-4 mb-6">
                        <x-general.grid mobile="1" gap="2" sm="2" md="2" lg="2" xl="2" class="col-span-12">
                            <x-form.input placeholder="" wire:model="total_marhun.weight" value="total_marhun.weight" label="Jumlah Berat(gram)" disable="true"  placeholder=""/>
                            <x-form.input placeholder="" wire:model="total_marhun.price" value="total_marhun.price" label="Jumlah Marhun(RM)" disable="true"  placeholder=""/>
                        </x-general.grid>
                    </div>

                    <div class="mt-4 mb-6">
                        <x-general.grid mobile="1" gap="2" sm="3" md="3" lg="3" xl="3" class="col-span-12">
                            <x-form.input placeholder="" wire:model="duration.from" value="duration.from" label="Dari" disable="true"  placeholder=""/>
                            <x-form.input placeholder="" wire:model="duration.until" value="duration.until" label="Hingga" disable="true"  placeholder=""/>
                            <x-form.input placeholder="" wire:model="duration.day" value="duration.day" label="Bilangan Hari" disable="true"  placeholder=""/>
                        </x-general.grid>
                    </div>

                    <div class="rounded-lg shadow-sm bg-yellow-50">
                        <div class="overflow-hidden text-sm">
                            <table class="w-full divide-y divide-yellow-100">
                                <thead>
                                    <tr>
                                        <th class="p-4 text-left">Produk</th>
                                        <th class="p-4 text-right">Pembiayaan Maksima (RM)</th>
                                        <th class="p-4 text-right">Keuntungan 1 Bulan (RM)</th>
                                        <th class="p-4 text-right">Keuntungan 18 Bulan (RM)</th>
                                    </tr>
                                </thead>

                                <tbody class="divide-y divide-yellow-50">
                                    @isset($financing)
                                        @foreach ($financing as $row)
                                            <tr>
                                                <td class="p-4 font-semibold">{{ $row['name'] }}</td>
                                                <td class="p-4 font-mono text-right">{{ $row['max_financing'] }}</td>
                                                <td class="p-4 font-mono text-right">{{ $row['one_month'] }}</td>
                                                <td class="p-4 font-mono text-right">{{ $row['full_month'] }}</td>
                                            </tr>
                                        @endforeach
                                    @endisset

                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

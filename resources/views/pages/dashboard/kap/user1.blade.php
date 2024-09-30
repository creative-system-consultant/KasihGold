<div class="col-span-12 sm:col-span-12 md:col-span-12 lg:col-span-12 xl:col-span-8 xxl:col-span-8">
    <div class="grid grid-cols-1 gap-6 mt-10 xl:grid-cols-2">
        <x-dashboard.info-card-user bg="yellow-400" title="Available Digital Gold" value="{{$this->tGold}} G" iconColor='white'
            cardRoute="{{route('digital-gold')}}">
            <x-slot name="svg">
                <x-heroicon-o-desktop-computer class="w-8 h-8 text-yellow-400" />
            </x-slot>
        </x-dashboard.info-card-user>
        <x-dashboard.info-card-user bg="teal-400" title="Locked Digital Gold" value="{{$this->lGold}} G" iconColor='white'
            cardRoute="{{route('digital-gold-details')}}">
            <x-slot name="svg">
                <x-heroicon-o-user class="w-8 h-8 text-teal-400" />
            </x-slot>
        </x-dashboard.info-card-user>

        <x-dashboard.info-card-user bg="indigo-400" title="" value="Buy Product" iconColor='white'
            cardRoute="{{route('product-view')}}">
            <x-slot name="svg">
                <x-heroicon-o-shopping-bag class="w-8 h-8 text-indigo-400" />
            </x-slot>
        </x-dashboard.info-card-user>
          <x-dashboard.info-card-user bg="pink-400" title="" value="Ar-Rahnu Pawn" iconColor='white'
            cardRoute="{{route('arrahnuPawn')}}">
            <x-slot name="svg">
                <x-heroicon-o-cash class="w-8 h-8 text-pink-400" />
            </x-slot>
        </x-dashboard.info-card-user>
    </div>
    <div class="col-span-12 mt-10 sm:col-span-12 md:col-span-12 lg:col-span-6 xxl:col-span-6" >
        <div class="p-4 bg-white rounded-md shadow-md">
            <p class="mb-2 text-lg font-semibold">Kap Gold Product</p>
            <div class="grid grid-cols-1 gap-4 mt-4 sm:grid-cols-1">
                <div class="px-2 py-4 rounded-md shadow-sm bg-gray-50">
                <x-general.grid mobile="1" gap="0" sm="2" md="2" lg="2" xl="2" class="w-full col-span-12">
                <!-- Start Spot Price Digital Gold-->
                <div class="mr-4 lg:mr-0">
                    <div class="flex items-center space-x-2 ">
                        <img src="{{ asset('img/gold/gold-bars.png')}}" class="w-auto h-24" />
                        <p class="text-sm font-semibold xl:text-base">Spot Price Digital Gold</p>
                    </div>
                    <x-general.grid mobile="2" gap="0" sm="2" md="2" lg="3" xl="3" class="w-full col-span-12 ">
                        <div class="p-4 text-white bg-yellow-300 border-2 border-yellow-300 rounded-tl-lg rounded-bl-lg">
                            <p class="text-sm font-bold text-black">Buy Price</p>
                            <p class="text-base font-semibold" >RM {{round($spotPrice->marketPrice->price*(($spotPriceB->percentage+100)/100),2)}}</p>
                        </div>
                        <div class="p-4 text-white bg-gray-800 border-2 border-gray-800 rounded-tr-lg rounded-br-lg">
                            <p class="text-sm text-yellow-300">Sell Price</p>
                            <p class="text-base font-semibold ">RM {{$spotPrice->marketPrice->price}}</p>
                        </div>
                    </x-general.grid>
                </div>
                <!-- End Spot Price Digital Gold-->

                <!-- Start Digital Dinar-->
                <div class="mt-4 mr-4 lg:mr-0 lg:mt-0">
                    <div class="flex items-center mb-4 space-x-2">
                        <img src="{{ asset('img/gold/coin.png')}}"  class="w-auto h-16 lg:h-20" />
                        <p class="text-sm font-semibold xl:text-base">Digital Dinar</p>
                    </div>
                    <x-general.grid mobile="2" gap="0" sm="2" md="2" lg="3" xl="3" class="w-full col-span-12 ">
                        <div class="p-4 text-white bg-yellow-300 border-2 border-yellow-300 rounded-tl-lg rounded-bl-lg">
                            <p class="text-sm font-bold text-black">Buy Price</p>
                            <p class="text-base font-semibold" >RM {{$dinarPrice->marketPrice->price}}</p>
                        </div>
                        <div class="p-4 text-white bg-gray-800 border-2 border-gray-800 rounded-tr-lg rounded-br-lg">
                            <p class="text-sm text-yellow-300">Sell Price</p>
                            <p class="text-base font-semibold ">RM {{$dinarPrice->outrightPrice->price}}</p>
                        </div>
                    </x-general.grid>
                </div>
                <!-- End Digital Dinar-->
                </x-general.grid>

            </div>
            <div class="grid grid-cols-1 gap-0 mt-4 sm:grid-cols-1">
                <!-- Start Digital Gold-->
                <div class="px-2 py-4 rounded-md shadow-sm bg-gray-50">
                    <div class="flex items-center space-x-2">
                        <img src="{{ asset('img/gold/gold-bars.png')}}" class="w-auto h-24" />
                        <p class="text-sm font-semibold xl:text-base">Digital Gold</p>
                    </div>
                    <x-general.grid mobile="1" gap="0" sm="2" md="2" lg="2" xl="2" class="w-full col-span-12">
                        <div  class="mr-4 lg:mr-0">
                            <p class="mb-2 text-base font-semibold">Buy Price</p>
                            @foreach($digitalPrice as $item)
                                <x-general.grid mobile="2" gap="0" sm="2" md="2" lg="3" xl="3" class="w-full col-span-12">
                                    <div class="p-4 mb-4 text-white bg-yellow-300 border-2 border-yellow-300 rounded-tl-lg rounded-bl-lg">
                                        <p class="text-sm font-bold text-black">Gram</p>
                                        <p class="text-base font-semibold" >{{number_format($item->prod_weight,2)}}g</p>
                                    </div>
                                    <div class="p-4 mb-4 text-white bg-gray-800 border-2 border-gray-800 rounded-tr-lg rounded-br-lg">
                                        <p class="text-sm text-yellow-300">Price</p>
                                        <p class="text-base font-semibold ">RM {{$item->marketPrice->price}}</p>
                                    </div>
                                </x-general.grid>
                            @endforeach
                        </div>

                        <div  class="mr-4 lg:mr-0">
                            <p class="mb-2 text-base font-semibold">Sell Price</p>
                            @foreach($digitalPrice as $item)
                                <x-general.grid mobile="2" gap="0" sm="2" md="2" lg="3" xl="3" class="w-full col-span-12 ">
                                    <div class="p-4 mb-4 text-white bg-yellow-300 border-2 border-yellow-300 rounded-tl-lg rounded-bl-lg">
                                        <p class="text-sm font-bold text-black">Gram</p>
                                        <p class="text-base font-semibold" >{{number_format($item->prod_weight,2)}}g</p>
                                    </div>
                                    <div class="p-4 mb-4 text-white bg-gray-800 border-2 border-gray-800 rounded-tr-lg rounded-br-lg">
                                        <p class="text-sm text-yellow-300">Price</p>
                                        <p class="text-base font-semibold ">RM {{ ($item->prod_weight < 1) ? $digital1gOutPrice->price." (1g)" : $item->outrightPrice->price}}</p>
                                    </div>
                                </x-general.grid>
                            @endforeach
                        </div>
                    </x-general.grid>
                </div>
                <!-- End Digital Gold-->
            </div>
            </div>
        </div>

    </div>
</div>
@push('js')
{{-- pie Chart --}}

<script>
    var charts = document.querySelector('#chartpie')
    var chart_data = @json($mainchart1);
    var options = {
            series: chart_data,
            chart: {
            height: 350,
            type: 'donut',
            },
            title: {
                text: 'Kasih Digital Gold',
                align: 'left'
            },
            labels: [
                'Kasih Digital Gold 0.01G',
                'Kasih Digital Gold 0.1G',
                'Kasih Digital Gold 0.25G',
                'Kasih Digital Gold 1G',
            ],
            responsive: [{
            breakpoint: 480,
            options: {
                legend: {
                position: 'bottom'
                }
            }
            }]
        };
        var charts = new ApexCharts(charts, options);
        charts.render();
</script>
{{-- pie Chart --}}
@endpush
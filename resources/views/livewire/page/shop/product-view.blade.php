<div>
    @if (Session::has('buying_for_customer_id'))
        <div class="p-4 mb-4 text-sm text-blue-700 bg-blue-100 rounded-lg dark:bg-blue-200 dark:text-blue-800" role="alert">
            <span class="font-medium">You are buying products for customer: {{ Session::get('buying_for_customer_name') }}</span>
            <button wire:click="clearBuyingForCustomer" class="ml-2 text-blue-700 underline hover:text-blue-800">Clear</button>
        </div>
    @endif
    <div x-data="{ cartOpen: false , isOpen: false }" class="my-10 bg-white rounded-lg">
        <header>
            <div class="px-6 py-3">
                <div class="flex justify-between">
                    <div class="w-full font-semibold text-gray-700 md:text-2xl">
                        Product List
                    </div>
                </div>
            </div>
        </header>

        <!-- Start product detail View-->
        <main class="my-4">
            <!-- Start First Digital Gold View-->
            <div class="px-6">
                <div class="pb-2 border-b-2 border-gray-300">
                    <h1 class="text-lg font-semibold">Digital Gold</h1>
                </div>
                <div class="grid grid-cols-1 gap-6 mt-6 sm:grid-cols-2 lg:grid-cols-4 xl:grid-cols-4">
                    @foreach ($digitalGold as $item)
                    <a
                        href="{{ route('product-detail',['iid'=>$item->item_id]) }}"
                        class="w-full max-w-sm mx-auto overflow-hidden bg-gray-800 rounded-md shadow-xl cursor-pointer hover:shadow-2xl"
                    >
                        <div class="flex w-full h-40 bg-white bg-center bg-no-repeat bg-contain"
                            style="background-image: url('{{ asset('img/product/'.$item->prod_cat.'/'.$item->item_id.'/'.$item->prod_img1) }}')">

                        </div>
                        <div class="px-5 py-3 bg-white">
                            <h3 class="text-sm font-bold text-black uppercase">{{{$item->prod_name}}}</h3>
                            @if(auth()->user()->isAgentKAP())
                                <span class="mt-2 text-base font-bold text-yellow-400">
                                    {{-- RM {{ number_format(($item->marketPrice->price - $item->commissionKAP->agent_rate),2) }} --}}
                                </span>
                            @else
                                <span class="mt-2 text-base font-bold text-yellow-400">
                                    {{-- RM {{number_format($item->marketPrice->price,2)}} --}}
                                </span>
                            @endif
                        </div>
                    </a>
                    @endforeach
                </div>
                <div class="flex justify-center">
                    <div class="flex mt-8 mb-8 rounded-md">
                    {{-- {{ $list->links('pagination::tailwind') }} --}}
                    </div>
                </div>
            </div>
            <!-- End First Digital Gold View-->

            <!-- Start 2nd premium View-->
            <div class="px-6">
                <div class="pb-2 border-b-2 border-gray-300">
                    <h1 class="text-lg font-semibold">Digital Dinar</h1>
                </div>
                <div class="grid grid-cols-1 gap-6 mt-6 sm:grid-cols-2 lg:grid-cols-4 xl:grid-cols-4">
                    @foreach ($digitalDinar as $item)
                    <a
                        href="{{ route('product-detail',['iid'=>$item->item_id]) }}"
                        class="w-full max-w-sm mx-auto overflow-hidden bg-gray-800 rounded-md shadow-xl cursor-pointer hover:shadow-2xl"
                    >
                        <div class="flex w-full h-40 bg-white bg-center bg-no-repeat bg-contain"
                            style="background-image: url('{{ asset('img/product/'.$item->prod_cat.'/'.$item->item_id.'/'.$item->prod_img1) }}')">

                        </div>
                        <div class="px-5 py-3 bg-white">
                            <h3 class="text-sm font-bold text-black uppercase">{{{$item->prod_name}}}</h3>
                            @if(auth()->user()->isAgentKAP())
                                <span class="mt-2 text-base font-bold text-yellow-400">
                                    {{-- RM {{ number_format(($item->marketPrice->price - $item->commissionKAP->agent_rate),2) }} --}}
                                </span>
                            @else
                                <span class="mt-2 text-base font-bold text-yellow-400">
                                    {{-- RM {{number_format($item->marketPrice->price,2)}} --}}
                                </span>
                            @endif
                        </div>
                    </a>
                    @endforeach
                </div>
                <div class="flex justify-center">
                    <div class="flex mt-8 mb-8 rounded-md">
                    {{-- {{ $list->links('pagination::tailwind') }} --}}
                    </div>
                </div>
            </div>
            <!-- End 2nd premium View-->

             <!-- Start 3rd premium View-->
             <div class="px-6">
                <div class="pb-2 border-b-2 border-gray-300">
                    <h1 class="text-lg font-semibold">Premium Spot Price Digital Gold</h1>
                </div>
                <div class="grid grid-cols-1 gap-6 mt-6 sm:grid-cols-2 lg:grid-cols-4 xl:grid-cols-4">
                    @foreach ($premiumGold as $item)
                    <a
                        href="{{ route('product-detail',['iid'=>$item->item_id]) }}"
                        class="w-full max-w-sm mx-auto overflow-hidden bg-gray-800 rounded-md shadow-xl cursor-pointer hover:shadow-2xl"
                    >
                        <div class="flex w-full h-40 bg-white bg-center bg-no-repeat bg-contain"
                            style="background-image: url('{{ asset('img/product/'.$item->prod_cat.'/'.$item->item_id.'/'.$item->prod_img1) }}')">

                        </div>
                        <div class="px-5 py-3 bg-white">
                            <h3 class="text-sm font-bold text-black uppercase">{{{$item->prod_name}}}</h3>
                            @if(auth()->user()->isAgentKAP())
                                <span class="mt-2 text-base font-bold text-yellow-400">
                                    {{-- RM {{ number_format(($item->marketPrice->price - $item->commissionKAP->agent_rate),2) }} --}}
                                </span>
                            @else
                                <span class="mt-2 text-base font-bold text-yellow-400">
                                    {{-- RM {{number_format($item->marketPrice->price,2)}} --}}
                                </span>
                            @endif
                        </div>
                    </a>
                    @endforeach
                </div>
                <div class="flex justify-center">
                    <div class="flex mt-8 mb-8 rounded-md">
                    {{-- {{ $list->links('pagination::tailwind') }} --}}
                    </div>
                </div>
            </div>
            <!-- End 3rd premium View-->
        </main>
        <!-- End product detail View-->
    </div>
</div>

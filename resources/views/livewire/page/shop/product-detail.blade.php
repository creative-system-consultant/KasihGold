<div>
    @if (Session::has('buying_for_customer_id'))
        <div class="p-4 mb-4 text-sm text-blue-700 bg-blue-100 rounded-lg dark:bg-blue-200 dark:text-blue-800" role="alert">
            <span class="font-medium">You are buying this product for customer: {{ Session::get('buying_for_customer_name') }}</span>
            <button wire:click="clearBuyingForCustomer" class="ml-2 text-blue-700 underline hover:text-blue-800">Clear</button>
        </div>
    @endif
    @if (session('error'))
        <x-toaster.error title="{{ session('title') }}" message="{{ session('message') }}"/>
    @elseif (session('info'))
        <x-toaster.info title="{{ session('title') }}" message="{{ session('message') }}"/>
    @elseif (session('success'))
        <x-toaster.success title="{{ session('title') }}" message="{{ session('message') }}"/>
    @elseif (session('warning'))
        <x-toaster.warning title="{{ session('title') }}" message="{{ session('message') }}"/>
    @endif
    <div class="py-6 my-10 mb-20 bg-white sm:mb-0">
        <div class="w-full px-4 mt-1 sm:px-6 lg:px-8">
            <div class="px-1 mb-8">
                <div class="flex justify-between">
                    <div class="font-semibold text-gray-700 md:text-2xl">
                        Product Detail
                    </div>
                    <div>
                        <a href="{{route('product-view')}}" class="flex items-center px-4 py-1 text-sm font-bold text-white bg-yellow-400 rounded cursor-pointer focus:outline-none hover:bg-yellow-300">
                            <x-heroicon-o-arrow-circle-left class="w-5 h-5 mr-2 text-white" />
                            Product List
                        </a>
                    </div>
                </div>
            </div>
            <div class="container px-4 py-6 border-2 rounded-lg">
                <div class="flex flex-col -mx-4 md:flex-row">
                    <div class="px-4 md:flex-1">
                        <div x-data="{imageUrl: '{{ asset('img/product/'.$info->prod_cat.'/'.$info->item_id.'/'.$info->prod_img1) }}'}" x-cloak>
                            <div class="h-64 mb-4 bg-white rounded-lg md:h-80">
                                <div class="flex items-center justify-center h-64 mb-4 bg-white rounded-lg md:h-80">
                                    <img id="main" :src="imageUrl" class="object-contain w-full h-full bg-cover" />
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="px-4 mt-8 lg:flex-1 md:mt-0">
                        <h2 class="mb-2 text-2xl font-bold leading-tight tracking-tight text-gray-800 md:text-3xl">{{$info->prod_name}}</h2>
                        @if (auth()->user()->client != 2)
                            <p class="text-sm text-gray-500">By
                                <span class="text-yellow-400">{{$userInfo->name}}</span>
                            </p>
                        @endif

                        @php
                            $currentDate = date('Y-m-d');
                            $currentDate = date('Y-m-d', strtotime($currentDate));
                            $startDate = $info->item->promotions->start_date ?? '';
                            $endDate = $info->item->promotions->end_date ?? '';
                            
                            if (Session::has('buying_for_customer_id')) {
                                $customer = \App\Models\User::find(Session::get('buying_for_customer_id'));
                                $isAgent = $customer && $customer->isAgentKAP();
                            } else {
                                $isAgent = auth()->user()->isAgentKAP();
                            }
                        @endphp

                        <div class="flex items-center my-4 space-x-4">
                            <div class="flex">
                                @if($isAgent && $info->prod_cat!=3)
                                <div>
                                    <div class="px-3 py-2 mb-2 font-bold text-yellow-300 bg-black rounded-lg">
                                        <p>Normal Price</p>
                                    </div>
                                    <div class="px-3 py-2 bg-gray-100 rounded-lg">
                                        @if($info->item->promotions !== NULL && ($currentDate >= $startDate) && ($currentDate <= $endDate))
                                            <strike>
                                                <span class="text-xl font-bold text-yellow-400">
                                                    RM {{ number_format(($info->item->marketPrice->price - $info->item->commissionKAP->agent_rate),2) }}
                                                </span>
                                            </strike>
                                        @else
                                            <span class="text-xl font-bold text-yellow-400">
                                                RM {{ number_format(($info->item->marketPrice->price - $info->item->commissionKAP->agent_rate),2) }}
                                            </span>
                                        @endif
                                    </div>
                                </div>
                                @elseif(!$isAgent && $info->prod_cat!=3)
                                <div>
                                    <div class="px-3 py-2 mb-2 font-bold text-yellow-300 bg-black rounded-lg">
                                        <p>Normal Price</p>
                                    </div>
                                    <div class="px-3 py-2 bg-gray-100 rounded-lg">
                                        @if($info->item->promotions !== NULL && ($currentDate >= $startDate) && ($currentDate <= $endDate))
                                            <strike>
                                                <span class="text-xl font-bold text-yellow-400">RM {{ number_format($info->item->marketPrice->price,2) }}</span>
                                            </strike>
                                        @else
                                            <span class="text-xl font-bold text-yellow-400">RM {{ number_format($info->item->marketPrice->price,2) }}</span>
                                        @endif
                                    </div>
                                </div>
                                @else
                                <div>
                                    <div class="px-3 py-2 mb-2 font-bold text-yellow-300 bg-black rounded-lg">
                                        <p>Spot Price</p>
                                    </div>
                                    <div class="px-3 py-2 bg-gray-100 rounded-lg">
                                        @if($info->item->promotions !== NULL && ($currentDate >= $startDate) && ($currentDate <= $endDate))
                                            <strike>
                                                <span class="text-xl font-bold text-yellow-400">
                                                RM {{($info->item->marketPrice->price+(round($info->item->marketPrice->price*$percentage,2)))}}
                                                </span>
                                            </strike>
                                        @else
                                            <span class="text-xl font-bold text-yellow-400">
                                                RM {{($info->item->marketPrice->price+(round($info->item->marketPrice->price*$percentage,2)))}}
                                            </span>
                                        @endif
                                    </div>
                                </div>
                                @endif
                            </div>

                            @if($info->item->promotions !== NULL && ($currentDate >= $startDate) && ($currentDate <= $endDate))
                                <div class="flex">
                                    @if($isAgent)
                                    <div>
                                        <div class="px-3 py-2 mb-2 font-bold text-green-400 bg-black rounded-lg">
                                            <p>Promo Price</p>
                                        </div>
                                        <div class="px-3 py-2 bg-gray-100 rounded-lg">
                                            <span class="text-xl font-bold text-green-400">
                                                RM {{ number_format(($info->item->promotions->promo_price - $info->item->commissionKAP->agent_rate),2) }}
                                            </span>
                                        </div>
                                    </div>
                                    @else
                                    <div>
                                        <div class="px-3 py-2 mb-2 font-bold text-green-400 bg-black rounded-lg">
                                            <p>Promo Price</p>
                                        </div>
                                        <div class="px-3 py-2 bg-gray-100 rounded-lg">
                                            @if($spotGram != null && is_numeric($spotGram))
                                            <span class="text-xl font-bold text-green-400">RM {{ number_format(($info->item->promotions->promo_price+(round($info->item->promotions->promo_price*$percentage,2)))*$spotGram,2) }}</span>
                                            @else
                                            <span class="text-xl font-bold text-green-400">RM {{ number_format($info->item->promotions->promo_price,2) }}</span>
                                            @endif
                                        </div>
                                    </div>
                                    @endif
                                </div>
                            @elseif($info->prod_cat==3)
                                <div class="flex">
                                    <div>
                                        <div class="px-3 py-2 mb-2 font-bold text-green-400 bg-black rounded-lg">
                                            <p>Total Price</p>
                                        </div>
                                        <div class="px-3 py-2 bg-gray-100 rounded-lg">
                                            <span class="text-xl font-bold text-green-400">RM
                                                @if($spotGram != null && is_numeric($spotGram))
                                                    {{number_format(($info->item->marketPrice->price+(round($info->item->marketPrice->price*$percentage,2)))*$spotGram,2)}}
                                                @else
                                                    0.00
                                                @endif
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                        <p class="text-gray-500">{{$info->prod_desc}}</p>
                        <x-form.basic-form wire:submit.prevent="buy">
                            <x-slot name="content">
                                <div class="flex flex-col items-center py-4 space-x-0 lg:flex-row lg:space-x-4">
                                    <div class="relative flex flex-row w-24 h-10 my-2 mt-1 bg-transparent rounded-lg">
                                    @if($info->prod_cat!=3)
                                    <button type="button" wire:click="subQty"
                                        class="w-20 h-full text-gray-600 bg-gray-300 rounded-l cursor-pointer hover:text-gray-700 hover:bg-gray-400 focus:outline-none">
                                        <span class="m-auto text-2xl font-thin">-</span>
                                    </button>
                                    <input  type="text"
                                        class="flex items-center justify-center w-full font-semibold text-center text-gray-700 bg-gray-300 outline-none focus:outline-none text-md hover:text-black focus:text-black md:text-basecursor-default"
                                        name="custom-input-number" wire:model="prod_qty"  >
                                    </input>
                                    <button type="button" wire:click="addQty"
                                        class="w-20 h-full text-gray-600 bg-gray-300 rounded-r cursor-pointer hover:text-gray-700 hover:bg-gray-400 focus:outline-none">
                                        <span class="m-auto text-2xl font-thin">+</span>
                                    </button>
                                    @else
                                    <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                        <span class="text-gray-500 sm:text-sm">
                                            g
                                        </span>
                                    </div>
                                    <input  type="text"
                                        class="flex items-center justify-center w-full font-semibold text-center text-gray-700 bg-gray-300 outline-none focus:outline-none text-md hover:text-black focus:text-black md:text-basecursor-default"
                                        name="custom-input-number" wire:model="spotGram"  >
                                    </input>
                                    @endif
                                    </div>
                                    <div class="flex">
                                        <button type="button" wire:click="buyNow()"
                                            class="px-2 py-2 font-semibold text-white bg-green-400 h-14 rounded-xl hover:bg-green-300 focus:outline-none">
                                            Buy Now
                                        </button>
                                        @if($info->prod_cat!=3)
                                        <button type="button" wire:click="addCart()"
                                            class="px-2 py-2 ml-2 font-semibold text-white bg-yellow-400 h-14 rounded-xl hover:bg-yellow-300 focus:outline-none">
                                            Add to Cart
                                        </button>
                                        @endif
                                    </div>
                                </div>
                            </x-slot>
                        </x-form.basic-form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
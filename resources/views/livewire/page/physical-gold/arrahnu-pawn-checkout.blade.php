<div class="">
    <div class="mb-20 bg-white rounded-lg sm:mb-0">
        <main class="px-4 py-6 my-8">
            <div class="container mx-auto">
                <h3 class="text-2xl font-medium text-gray-700">Financing Information</h3>
                <div class="flex flex-col mt-2 lg:flex-row">
                    <div class="order-2 w-full lg:w-1/2">
                        <x-form.basic-form wire:submit.prevent="submit">
                            <x-slot name="content">
                                <div class="pb-8">
                                    <div class="lg:w-full">
                                        <div>
                                            <div class="mt-4">
                                                <div class="flex items-center justify-between w-full p-4 bg-white border focus:outline-none">
                                                    <label class="flex items-center">
                                                            <span class="ml-2 text-base text-gray-700">Product Information</span>
                                                    </label>
                                                </div>
                                                <div class="overflow-hidden bg-white">
                                                    <div class="px-4 py-4 border-2">
                                                        <x-form.basic-form>
                                                            <x-slot name="content">
                                                                <div class="grid gap-2 lg:grid-cols-2 sm:grid-cols-1">
                                                                    <div>
                                                                        <label class="block text-sm font-semibold leading-5 text-gray-700">
                                                                            Product Name
                                                                        </label>
                                                                        <div class="flex mt-1 mb-2 rounded-md shadow-sm">
                                                                            <input value="" wire:model="prod_name"
                                                                                class="form-input bg-gray-200 block w-full transition duration-150 ease-in-out sm:text-sm sm:leading-5 "
                                                                            disabled>
                                                                        </div>
                                                                    </div>
                                                                    
                                                                </div>
                                                                <div class="grid gap-2 lg:grid-cols-2 sm:grid-cols-1">
                                                                    <div>
                                                                        <label class="block text-sm font-semibold leading-5 text-gray-700">
                                                                            Financing Margin
                                                                        </label>
                                                                        <div class="flex mt-1 mb-2 rounded-md shadow-sm">
                                                                            <input value="" wire:model="financeMargin"
                                                                                class="form-input bg-gray-200 block w-full transition duration-150 ease-in-out sm:text-sm sm:leading-5 "
                                                                            disabled>
                                                                        </div>
                                                                    </div>
                                                                    <div>
                                                                        <label class="block text-sm font-semibold leading-5 text-gray-700">
                                                                            Financing Duration
                                                                        </label>
                                                                        <div class="flex mt-1 mb-2 rounded-md shadow-sm">
                                                                            <input value="" wire:model="financeDuration"
                                                                                class="form-input bg-gray-200 block w-full transition duration-150 ease-in-out sm:text-sm sm:leading-5 "
                                                                            disabled>
                                                                        </div>
                                                                    </div>
                                                                    <div>
                                                                        <label class="block text-sm font-semibold leading-5 text-gray-700">
                                                                            Profit Rules
                                                                        </label>
                                                                        <div class="flex mt-1 mb-2 rounded-md shadow-sm">
                                                                            <input value="" wire:model="profitRules"
                                                                                class="form-input bg-gray-200 block w-full transition duration-150 ease-in-out sm:text-sm sm:leading-5 "
                                                                            disabled>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="grid gap-2 lg:grid-cols-2 sm:grid-cols-1">
                                                                    <div>
                                                                        <label class="block text-sm font-semibold leading-5 text-gray-700">
                                                                            Minimum Financing
                                                                        </label>
                                                                        <div class="flex mt-1 mb-2 rounded-md shadow-sm">
                                                                            <input value="" wire:model="minFinancing"
                                                                                class="form-input bg-gray-200 block w-full transition duration-150 ease-in-out sm:text-sm sm:leading-5 "
                                                                            disabled>
                                                                        </div>
                                                                    </div>
                                                                    <div>
                                                                        <label class="block text-sm font-semibold leading-5 text-gray-700">
                                                                            Maximum Financing
                                                                        </label>
                                                                        <div class="flex mt-1 mb-2 rounded-md shadow-sm">
                                                                            <input value="" wire:model="maxFinancing"
                                                                                class="form-input bg-gray-200 block w-full transition duration-150 ease-in-out sm:text-sm sm:leading-5 "
                                                                            disabled>
                                                                        </div>
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
                                <div class="pb-8">
                                    <div class="lg:w-full">
                                        <div>
                                            <div class="mt-4">
                                                <div class="flex items-center justify-between w-full p-4 bg-white border focus:outline-none">
                                                    <label class="flex items-center">
                                                            <span class="ml-2 text-base text-gray-700">Summary</span>
                                                    </label>
                                                </div>
                                                <div class="overflow-hidden bg-white">
                                                    <div class="px-4 py-4 border-2">
                                                        <x-form.basic-form>
                                                            <x-slot name="content">
                                                                <div class="grid gap-2 lg:grid-cols-2 sm:grid-cols-1">
                                                                    <div>
                                                                        <label class="block text-sm font-semibold leading-5 text-gray-700">
                                                                            Maximum Allowable Financing
                                                                        </label>
                                                                        <div class="flex mt-1 mb-2 rounded-md shadow-sm">
                                                                            <input value="RM " wire:model="maximum_financing"
                                                                                class="block w-full transition duration-150 ease-in-out bg-gray-200 form-input sm:text-sm sm:leading-5 "
                                                                            disabled>
                                                                        </div>
                                                                    </div>

                                                                </div>
                                                                <div class="grid gap-2 lg:grid-cols-2 sm:grid-cols-1">
                                                                    <div>
                                                                        <label class="block text-sm font-semibold leading-5 text-gray-700">
                                                                            Financing Amount
                                                                        </label>
                                                                        <div class="flex mt-1 mb-2 rounded-md shadow-sm">
                                                                            <input value="" wire:model="financeAmt"
                                                                                class="block w-full transition duration-150 ease-in-out form-input sm:text-sm sm:leading-5 "
                                                                            >
                                                                        </div>
                                                                    </div>
                                                                    <x-form.dropdown label="Payable Type" wire:model="pay_type" default="yes" value="" >
                                                                        @foreach ($payment_type as $type)
                                                                            <option value="{{ $type->PAY_CODE }}">{{ $type->PAY_TYPE }}</option>
                                                                        @endforeach

                                                                    </x-form.dropdown>
                                                                </div>
                                                                @if($pay_type == "1")
                                                                <div class="grid gap-2 lg:grid-cols-2 sm:grid-cols-1">
                                                                    <x-form.dropdown label="Pickup Branch" wire:model="chosenBranch" default="yes" value="" >
                                                                        @foreach ($branch as $item)
                                                                            <option value="{{ $item->BRANCH_CODE }}">{{ $item->BRANCH_NAME }}</option>
                                                                        @endforeach

                                                                    </x-form.dropdown>
                                                                </div>
                                                                @endif
                                                            </x-slot>
                                                        </x-form.basic-form>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="flex items-center justify-end mt-2">
                                    <button class="flex items-center px-3 py-2 text-sm font-medium text-white bg-green-500 rounded-md hover:bg-green-600 focus:outline-none">
                                        <x-heroicon-o-clipboard-check class="w-5 h-5 mr-2" />
                                        <span>CONFIRM</span>
                                    </button>
                                </div>
                            </x-slot>
                        </x-form.basic-form>
                    </div>
                    <div class="flex-shrink-0 order-1 w-full mt-4 mb-8 lg:w-1/2 lg:mb-0 lg:order-2">
                        <div class="flex justify-center lg:justify-end">
                            <div class="w-full max-w-md px-4 py-3 border">
                                <div class="flex items-center justify-between">
                                    <h3 class="font-medium text-gray-700">Collateral Information </h3>

                                </div>
                                {{-- @dump($lists) --}}

                                        @foreach($lists as $item)
                                        @if(is_array($item))
                                            @if($item['grammage']>0)
                                                <div class="flex justify-between mt-6 border-b-2 pb-4">
                                                    <div class="flex">
                                                        <img class="object-cover w-20 h-20 rounded"
                                                        src="{{ asset('img/product/1/9/d1.png') }}" alt="">

                                                        <div class="mx-3 my-3">
                                                            <h3 class="text-sm text-gray-600">{{$item['prod_name']}}</h3>
                                                            <h6 class="text-sm text-gray-600">{{$item['grammage']}} g</h6>
                                                            <h6 class="text-sm text-gray-600">24 Karat </h6>
                                                        </div>
                                                    </div>
                                                    <span class="my-3 font-semibold text-gray-600">RM {{MoneyRound($item['tot_price'])}}</span>
                                                    </span>
                                                </div>
                                            @endif
                                        @else 
                                            @if($item->grammage>0)
                                                <div class="flex justify-between mt-6 border-b-2 pb-4">
                                                    <div class="flex">
                                                        <img class="object-cover w-20 h-20 rounded"
                                                        src="{{ asset('img/product/1/9/d1.png') }}" alt="">

                                                        <div class="mx-3 my-3">
                                                            <h3 class="text-sm text-gray-600">{{$item->prod_name}}</h3>
                                                            <h6 class="text-sm text-gray-600">{{$item->grammage}} g</h6>
                                                            <h6 class="text-sm text-gray-600">24 Karat </h6>
                                                        </div>
                                                    </div>
                                                    <span class="my-3 font-semibold text-gray-600">RM {{MoneyRound($item->tot_price)}}</span>
                                                    </span>
                                                </div>
                                            @endif
                                        @endif
                                        @endforeach

                                <div class="pb-4 mt-6 border-b-2">
                                    <div class="flex justify-between">
                                        <div class="text-gray-500">
                                            <p>Price Per Gram</p>
                                        </div>
                                        <div class="font-semibold">
                                            <p>RM {{MoneyRound($goldprice['price'],2)}}</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="pb-4 mt-6 border-b-2">
                                    <div class="flex justify-between">
                                        <div class="text-gray-500">
                                            <p>Total Collateral Weight (g)</p>
                                        </div>
                                        <div class="font-semibold">
                                            <p>{{MoneyRound($total_weight,2)}} g</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="pb-4 mt-6 border-b-2">
                                    <div class="flex justify-between">
                                        <div class="text-gray-500">
                                            <p>Pawn Value</p>
                                        </div>
                                        <div class="font-semibold">
                                        <p>RM {{MoneyRound($total_pawn,2)}}</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="flex justify-between pb-4 mt-6 border-b-2">
                                    <div class="font-semibold">
                                        <p>Maximum Allowable Pawn (80%)</p>
                                    </div>
                                    <div class="font-semibold text-lg">
                                       
                                        <p>RM {{MoneyRound($maximum_financing,2)}}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>
</div>

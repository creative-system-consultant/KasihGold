<div class="">
    <div class="mb-20 bg-white rounded-lg sm:mb-0">
        <main class="px-4 py-6 my-8">
            <div class="container mx-auto">
                <h3 class="text-2xl font-medium text-gray-700">Customer Information</h3>
                <div class="flex flex-col mt-2 lg:flex-row">
                    <div class="w-full lg:w-1/2 order-2">
                        
                        <x-form.basic-form wire:submit.prevent="submit">
                           <x-slot name="content">
                               <div class="pb-8">
                                   <div class="lg:w-full">
                                       <div>
                                           <h4 class="text-base font-medium text-gray-600">Billing Details</h4>
                                           <div class="mt-4" x-data="{ accordion: 0 }">

                                               <div class="overflow-hidden bg-white">
                                                   <div class="px-4 py-4 border-2">
                                                       <x-form.basic-form>
                                                           <x-slot name="content">
                                                               <div class="grid gap-1 lg:grid-cols-1 sm:grid-cols-1">
                                                                   <x-form.input label="Name" wire:model.defer="name" value="name" placeholder=""/>
                                                               </div>
                                                               <div class="grid gap-2 lg:grid-cols-1 sm:grid-cols-1">
                                                                   <x-form.input label="Phone Number" wire:model.defer="phone1" value="phone1" placeholder=""/>
                                                                   <div class="grid gap-2 lg:grid-cols-1 sm:grid-cols-1">
                                                                       <x-form.address class="" label="Address" value1="address1" value2="address2" value3="address3" value4="town" value5="postcode" value6="state" :state="$states" condition="state"/>
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



                               <div class="flex items-center justify-end mt-2">
                                   <button class="flex items-center px-3 py-2 text-sm font-medium text-white bg-green-500 rounded-md hover:bg-green-600 focus:outline-none">
                                       <x-heroicon-o-clipboard-check class="w-5 h-5 mr-2" />
                                       <span>COMPLETE ORDER</span>
                                   </button>
                               </div>
                           </x-slot>
                       </x-form.basic-form>
                   </div>
                    <div class="flex-shrink-0 order-1 w-full mt-4 mb-8 lg:w-1/2 lg:mb-0 lg:order-2">
                        <div class="flex justify-center lg:justify-end">
                            <div class="w-full max-w-md px-4 py-3 border">
                                <div class="flex items-center justify-between">
                                  
                                    <h3 class="font-medium text-gray-700">Minting Spot Gold </h3>
                                </div>



                                    <div class="flex justify-between pb-4 mt-6 border-b-2">
                                        <div class="flex">
                                            <img class="object-cover w-20 h-20 rounded"
                                                src="{{ asset('img/product/1/9/d1.png') }}"
                                                alt="">
                                            <div class="mx-3 my-3">
                                                <h3 class="text-sm text-gray-600">Kasih AP Gold Wafer</h3>
                                                <h4 class="text-sm text-gray-600"><b>{{$GoldMintQTY}} gram</b></h4>
                                            </div>
                                        </div>
                                        <span class="my-3 font-semibold text-gray-600">RM {{$MintingCost}}</span>
                                    </div>

                                    <table class="w-full">
                                        <tr>
                                            <th width="80%"></th>
                                            <th width="10%"></th>
                                            <th width="10%"></th>
                                        </tr>
                                        <tr>
                                            <td class="pt-6 font-semibold">Minting Cost</td>
                                            <td class="px-2 pt-6 text-lg font-semibold text-right">RM</td>
                                            <td class="pt-6 text-lg font-semibold text-right">{{$MintingCost}}</td>
                                        </tr>
                                        <tr>
                                            <td class="pt-6 font-semibold">Shipping</td>
                                            <td class="px-2 pt-6 text-lg font-semibold text-right">RM</td>
                                            <td class="pt-6 text-lg font-semibold text-right ">10</td>
                                        </tr>
                                        <tr class="border-b-2 ">
                                            <td  class="py-2 font-semibold">Misc. Charges</td>
                                            <td  class="px-2 py-2 text-lg font-semibold text-right">RM</td>
                                            <td  class="py-2 text-lg font-semibold text-right">1</td>
                                        </tr>
                                        <tr>
                                            <td class="pt-2 font-semibold">Total Cost</td>
                                            <td class="px-2 pt-2 text-lg font-semibold text-right">RM</td>
                                            <td class="pt-2 text-lg font-semibold text-right">{{$MintingCost + 10 + 1}}</td>
                                        </tr>
                                    </table>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>
</div>

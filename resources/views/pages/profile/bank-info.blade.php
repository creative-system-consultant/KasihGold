<x-general.card class="mt-2 mb-20 bg-white shadow-lg sm:mb-0">
    <x-note-card>
        1. Please take note that the information submitted will not be reflected on your account untill it has been reviewed by CSC staffs.
        <br>
        2. To change information in locked field, please contact your nearest branch.
    </x-note-card>
    <div class="flex items-center px-5 py-5 border-b border-gray-200 sm:py-3">
        <h2 class="mr-auto text-base font-medium">
            Bank Information
        </h2>
    </div>
    <div class="px-4 py-2">
        <x-form.basic-form>
            <x-slot name="content">
                <div class="mt-2 leading-4">
                    <div class="grid gap-2 lg:grid-cols-2 sm:grid-cols-1">
                        <x-form.dropdown label="Bank" wire:model="bankId" default="no" value="bankId">
                            <option value="0">Choose Bank</option>
                            @foreach ($banks as $item)
                                <option value="{{ $item->id }}">{{ $item->name }}</option>
                            @endforeach
                        </x-form.dropdown>
                        <x-form.input label="Bank Swift Code" value="swiftCode" wire:model="swiftCode"/>
                        <x-form.input label="Bank Account No" value="accNo" wire:model="accNo"/>
                        <x-form.input label="Bank Account Holder Name" value="accHolderName" wire:model="accHolderName"/>
                        <x-form.input label="Bank Account ID (Reg. No / NRIC / Army ID / Police ID / Passport or others ID registered with bank)" value="bankAccId" wire:model="bankAccId"/>
                    </div>
                    @include('pages.profile.upload.bank_statement')
                    <div class="flex justify-end py-4">
                        <button type="button" class="flex px-4 py-2 text-sm font-bold text-white bg-green-500 rounded focus:outline-none hover:bg-green-400" wire:click="saveBank">
                            Save
                        </button>
                    </div>
                </div>
            </x-slot>
        </x-form.basic-form>
    </div>
</x-general.card>
<div>
    <form method="post" enctype="multipart/form-data" wire:submit.prevent="nomineeUpload">
		@csrf
		<x-general.grid mobile="1" gap="5" sm="3" md="3" lg="3" xl="3" class="col-span-6 py-4">
			<div class="px-4 py-4 bg-white border-2 shadow-xl">
				<div class="text-lg font-bold">
					Nominee Form
				</div>
				<div class="py-3 text-base font-semibold text-gray-500 border-b-2">
					<p>Upload your nominee form.(Max size 2MB)</p><span class="font-bold text-red-500">*Required</span>
				</div>
				<div class="pt-4">
					<x-form.input type="file" label="" value="" wire:model="doc_nom"/>
					@error('doc_nom') <span class="text-xs italic tracking-wider text-red-500 font0-medium">{{ $message }}</span> @enderror
				</div>
			</div>

			<div class="px-4 py-4 bg-white border-2 shadow-xl">
				<div class="text-lg font-bold">
					Own IC Document
				</div>
				<div class="py-3 text-base font-semibold text-gray-500 border-b-2">
					<p>Upload your own IC or related document. (Max size 2MB)</p><span class="font-bold text-red-500">*Required</span>
				</div>
				<div class="pt-4">
					<x-form.input type="file" label="" value="" wire:model="doc_ic"/>
					@error('doc_ic') <span class="text-xs italic tracking-wider text-red-500 font0-medium">{{ $message }}</span> @enderror
				</div>
			</div>

			<div class="px-4 py-4 bg-white border-2 shadow-xl">
				<div class="text-lg font-bold">
					Nominee IC Document
				</div>
				<div class="py-3 text-base font-semibold text-gray-500 border-b-2">
					<p>Upload your nominee IC or related document. (Max size 2MB)</p><span class="font-bold text-red-500">*Required</span>
				</div>
				<div class="pt-4">
					<x-form.input type="file" label="" value="" wire:model="doc_nom_ic" multiple/>
					@error('doc_nom_ic.*') <span class="text-xs italic tracking-wider text-red-500 font0-medium">{{ $message }}</span> @enderror
				</div>
			</div>
		</x-general.grid>
		<div class="flex justify-center">
			<button type="submit" class="px-4 py-2 bg-blue-500 hover:bg-blue-400 rounded-lg text-sm tracking-wider font-semibold text-white transition duration-300 ease-in-out">Save</button>
		</div>
	</form>
</div>
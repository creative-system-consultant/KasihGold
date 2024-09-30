<div>
    <div class="flex flex-col items-end mt-8 sm:items-center intro-y sm:flex-row">
        <h2 class="mr-auto text-lg font-medium">
            Create Announcement
        </h2>
        <a href="{{route('admin.list-announcements')}}" class="flex items-center px-4 py-1 mt-2 text-sm font-bold text-white bg-yellow-400 rounded cursor-pointer sm:mt-0 focus:outline-none hover:bg-yellow-300">
            <x-heroicon-o-arrow-circle-left class="w-5 h-5 mr-2 text-white" />
            Announcement
        </a>
    </div>
    <div class="grid grid-cols-12 gap-5 mt-5 mb-20 pos intro-y sm:mb-0">
        <div class="col-span-12 lg:col-span-8">
            <div class="mt-1 overflow-hidden bg-white shadow-lg post intro-y">
                <form wire:submit.prevent="create">
                    <div class="px-6 py-2 border border-gray-200 rounded-md">
                        <div class="mt-5">
                            <label for="title" class="block text-sm font-medium text-gray-700">Title</label>
                            <input wire:model.lazy="title" type="text" name="title" id="title" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" placeholder="Title">
                            @error('title') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>
                        <div class="mt-5">
                            <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
                            <textarea wire:model.lazy="description" name="description" id="description" rows="8" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" placeholder="Details"></textarea>
                            @error('description') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>
                        <div class="mt-5">
                            <label for="announcement_img" class="block text-sm font-medium text-gray-700">Announcement Image</label>
                            <input wire:model="announcement_img" type="file" name="announcement_img" id="announcement_img" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                            @error('announcement_img') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            @if ($announcement_img)
                                <img src="{{ $announcement_img->temporaryUrl() }}" class="mt-2 rounded-lg max-h-48">
                            @endif
                        </div>
                        <div class="flex justify-center p-2 mt-5">
                            <button type="submit" class="flex px-4 py-2 text-sm font-bold text-white bg-green-600 rounded cursor-pointer focus:outline-none hover:bg-green-500">Submit</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

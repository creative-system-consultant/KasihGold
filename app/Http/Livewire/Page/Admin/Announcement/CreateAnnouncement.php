<?php

namespace App\Http\Livewire\Page\Admin\Announcement;

use App\Models\Announcement;
use Livewire\Component;
use Livewire\WithFileUploads;

class CreateAnnouncement extends Component
{
    use WithFileUploads;

    public $title;
    public $description;
    public $announcement_img;

    protected $rules = [
        'title' => 'required|min:5',
        'description' => 'required|min:10',
        'announcement_img' => 'required|image|max:1024', // 1MB Max
    ];

    public function create()
    {
        $this->validate();

        $imagePath = $this->announcement_img->store('public/announcementImg');

        Announcement::create([
            'title' => $this->title,
            'description' => $this->description,
            'announcement_img' => str_replace('public/', 'storage/', $imagePath),
            'created_by' => auth()->user()->id,
            'created_at' => now(),
        ]);

        session()->flash('success');
        session()->flash('title', 'Success!');
        session()->flash('message', 'Announcement successfully created.');
        return redirect()->route('admin.list-announcements');
    }

    public function render()
    {
        return view('livewire.page.admin.announcement.create-announcement')->extends('default.default');
    }
}

<?php

namespace App\Http\Livewire;

use App\Models\Tag;
use Livewire\Component;

class TagManager extends Component
{
    public $newTagName = '';

    public function render()
    {
        return view('livewire.tag-manager', [
            'tags' => Tag::withCount('machines')->orderBy('name')->get(),
        ]);
    }

    public function deleteTag($tagId)
    {
        Tag::find($tagId)->delete();
    }

    public function createTag()
    {
        $this->validate([
            'newTagName' => 'required|unique:tags,name',
        ]);

        Tag::create(['name' => $this->newTagName]);
        $this->newTagName = '';
    }
}

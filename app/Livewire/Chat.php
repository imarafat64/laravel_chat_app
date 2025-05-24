<?php

namespace App\Livewire;

use App\Models\User;
use Livewire\Component;
use App\Models\ChatMessage;
use App\Events\MessageSent;  // Change this import
use Illuminate\Support\Facades\Auth;

class Chat extends Component
{

    public $users;
    public $selectedUser;
    public $newMessage;
    public $messages;
    public $loginId;
    public function mount()
    {
        $this->users = User::whereNot('id', Auth::id())->latest()->get();
        $this->selectedUser = $this->users->first();
        $this->loadmessages();
       $this->loginId = Auth::id();
        
    }
    public function loadmessages(){
        $this->messages = ChatMessage::query()
        ->where(function ($query) {
            $query->where('sender_id', Auth::id())
                  ->where('receiver_id', $this->selectedUser->id);
        })
        ->orWhere(function ($query) {
            $query->where('sender_id', $this->selectedUser->id)
                  ->where('receiver_id', Auth::id());
        })->get();
    }

   
    
    public function submit(){
        if(!$this->newMessage) return;
        $message = ChatMessage::create([
            'sender_id' => Auth::id(),
            'receiver_id' => $this->selectedUser->id,
            'message' => $this->newMessage
        ]);
        $this->messages->push($message);
        $this->newMessage = '';
        broadcast(new MessageSent($message));
        
    }
    
    public function getListeners()
    {
        return [
            "echo-private:chat.{$this->loginId},MessageSent" => "newChatMessageNotification"
        ];
    }
    public function newChatMessageNotification($message){
        if($message['sender_id'] == $this->selectedUser->id){
         $messageObj = ChatMessage::find($message['id']);
        
         $this->messages->push($messageObj);
        }
     }
    public function selectUser($id){
      
        $this->selectedUser = User::find($id);
        $this->loadmessages();

    }
    public function render()
    {
        return view('livewire.chat');
    }
   public function updatedNewMessage($value)
   {
       $this->dispatch("userTyping", [
           'userID' => $this->loginId,
           'username' => Auth::user()->name,
           'selectedUserID' => $this->selectedUser->id
       ]);
   }
}

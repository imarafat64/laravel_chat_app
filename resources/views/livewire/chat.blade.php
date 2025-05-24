<div>
    <div class="relative mb-6 w-full">
        <flux:heading size="xl" level="1">{{ __('Chat') }}</flux:heading>
        <flux:subheading size="lg" class="mb-6">{{ __('Manage your conversations') }}</flux:subheading>
        <flux:separator variant="subtle" />
    </div>
    <div class="flex h- [550px] text-sm border rounded-xl shadow overflow-hidden bg-white">
        <! ----- left User List ----->
        <div class="w-1/4 border-r bg-gray-50">
            <div class="p-4 font-bold text-gray-700 border-b">User</div>
            <div class="devide-y">
                @foreach ($users as $user )
                 <div wire:click="selectUser({{ $user->id }})" class="p-3 cursor-pointer hover:bg-blue-100 transition {{ $selectedUser->id === $user->id ? 'bg-sky-100 font-semibold' : '' }}">
                  <div class="text-gray-800">{{$user->name}}</div>
                  <div class="text-gray-500 text-xs">{{$user->email}}</div>
                </div>
                @endforeach
            </div>
        </div>
        <! ----- right user chat ----->
        <div class="w-3/4 flex flex-col">
            <!----- chat header ----->
            <div class="p-4 border-b bg-gray-50">
               <div class="text-lg font-semibold text-gray-800">{{ $selectedUser->name }}</div>
               <div class="text-xs text-gray-500">{{$selectedUser->email}}</div>
            </div>
            <!----- chat body ----->
            <div class="flex-1 p-4 overflow-y-auto space-y-2 bg-gray-50">
                @foreach ($messages as $message)
                <div class="flex {{ $message->sender_id === auth()->id() ? 'justify-end ': 'justify-start ' }}">
                    <div class="max-w-xs px-4 py-2 rounded-2xl shadow {{ $message->sender_id === auth()->id() ? 'bg-sky-600 text-white ': 'bg-gray-200 text-gray-800' }}">{{ $message->message }}</div>
                   
                </div>
                @endforeach
               
             </div>
            <!----- chat input ----->
            <div id="typing-indicator" class="p-4 pb-1 text-xs text-gray-400 italic"></div>

            <form wire:submit="submit" class="p-4 border-t bg-white flex items-center gap-2">
                <input 
                    wire:model.live="newMessage" 
                    type="text" 
                    class="flex-1 border border-gray-300 rounded-full py-2 px-4 text-sm text-gray-800 focus:outline-none focus:ring-2 focus:ring-sky-200 focus:border-sky-200" 
                    placeholder="Type your message..."
                >
                <button type="submit" class="px-4 py-2 rounded-full bg-sky-600 text-white hover:bg-sky-700">Send</button>
            </form>

    </div>
</div>
<script>
    document.addEventListener('livewire:initialized', () => {
        Livewire.on('userTyping', (event) => {
            window.Echo.private(`chat.${event.selectedUserID}`).whisper('typing', {
                userID: event.userID,
                username: event.username
            });
        });

        window.Echo.private(`chat.{{ $loginId }}`).listenForWhisper('typing', (event) => {
            const typingIndicator = document.getElementById("typing-indicator");
            typingIndicator.innerText = `${event.username} is typing...`;
            
            setTimeout(() => {
                typingIndicator.innerText = '';
            }, 2000);
        });
    });
</script>

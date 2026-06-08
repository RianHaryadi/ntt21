<?php

namespace App\Livewire;

use App\Models\TravelChatSession;
use App\Services\TravelChatService;
use Illuminate\Support\Str;
use Livewire\Component;
use Livewire\Attributes\On;

class TravelChat extends Component
{
    public ?TravelChatSession $session = null;
    public string $input = '';
    public bool $popup = false;

    private string $welcomeMessage = "Halo! Saya **Ara**, asisten perjalanan pribadi Anda khusus untuk menemukan destinasi tersembunyi (*hidden gems*) di Nusa Tenggara Timur (NTT). 🌴✨\n\nUntuk memulai perjalanan Anda, wilayah mana di NTT yang ingin Anda kunjungi atau rencanakan untuk dijelajahi? (Misalnya: Flores, Sumba, Timor, Labuan Bajo, Rote, atau Alor? Jika masih bingung, beritahu saya!)";

    public function mount(bool $popup = false)
    {
        $this->popup = $popup;

        $token = request()->query('token');
        if ($token) {
            $this->session = TravelChatSession::where('session_token', $token)->first();
            if ($this->session && auth()->check() && empty($this->session->user_id)) {
                $this->session->update(['user_id' => auth()->id()]);
            }
            return;
        }

        // Page mode (non-popup): create session + welcome message immediately
        if (!$popup) {
            $this->createSession();
        }
        // Popup mode: session created lazily on first sendMessage()
    }

    private function createSession(): void
    {
        $data = [
            'session_token' => (string) Str::uuid(),
            'status' => 'active',
        ];

        if (auth()->check()) {
            $data['user_id'] = auth()->id();
        }

        $this->session = TravelChatSession::create($data);

        $this->session->messages()->create([
            'role' => 'assistant',
            'content' => $this->welcomeMessage,
        ]);
    }

    public function sendMessage(TravelChatService $service)
    {
        $this->validate(['input' => 'required|string|max:1000']);

        $userInput = trim($this->input);
        $this->input = '';

        // Popup mode: lazily create session on first message
        if (!$this->session) {
            $this->createSession();
        }

        $result = $service->send($this->session, $userInput);

        $this->dispatch('scroll-chat');

        if ($result['is_ready']) {
            return redirect()->route('travel.recommendation', $this->session->session_token);
        }
    }

    public function render()
    {
        $messages = $this->session
            ? $this->session->messages()->orderBy('created_at')->get()
            : collect();

        return view('livewire.travel-chat', compact('messages'));
    }
}

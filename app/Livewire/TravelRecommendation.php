<?php

namespace App\Livewire;

use App\Models\Destination;
use App\Models\Hotel;
use App\Models\TourPackage;
use App\Models\TravelChatSession;
use Livewire\Component;

class TravelRecommendation extends Component
{
    public TravelChatSession $session;

    // Data from DB
    public $hotels;
    public $tourPackages;
    public $destinations;

    // User selections
    public ?int $selectedHotelId = null;
    public array $selectedTourIds = [];

    // Notes/edit
    public string $customNotes = '';
    public bool $showSuccess = false;

    public function mount(string $token)
    {
        $this->session = TravelChatSession::where('session_token', $token)
            ->where('status', 'completed')
            ->firstOrFail();

        // Load all data
        $this->hotels       = Hotel::all();
        $this->tourPackages = TourPackage::all();

        // Destinations: match keywords from AI text first, then rest
        $raw = strtolower($this->session->recommendation_raw ?? '');
        $this->destinations = Destination::all()->sortByDesc(function ($d) use ($raw) {
            return str_contains($raw, strtolower($d->name)) ? 1 : 0;
        })->values();

        // Restore previous selections if any
        $edited = $this->session->recommendation_edited ?? [];
        $this->selectedHotelId = $edited['selected_hotel_id'] ?? $this->hotels->first()?->id;
        $this->selectedTourIds = $edited['selected_tour_ids'] ?? [];
        $this->customNotes = $edited['custom_notes'] ?? '';
    }

    public function selectHotel(int $hotelId): void
    {
        $this->selectedHotelId = $hotelId;
    }

    public function toggleTour(int $tourId): void
    {
        if (in_array($tourId, $this->selectedTourIds)) {
            $this->selectedTourIds = array_values(array_filter($this->selectedTourIds, fn($id) => $id !== $tourId));
        } else {
            $this->selectedTourIds[] = $tourId;
        }
    }

    public function saveSelection(): void
    {
        $this->session->update([
            'recommendation_edited' => [
                'selected_hotel_id' => $this->selectedHotelId,
                'selected_tour_ids' => $this->selectedTourIds,
                'custom_notes' => $this->customNotes,
            ],
        ]);

        $this->showSuccess = true;
    }

    public function dismissNotification(): void
    {
        $this->showSuccess = false;
    }

    public function render()
    {
        $raw = strtolower($this->session->recommendation_raw ?? '');

        $selectedHotel = $this->selectedHotelId
            ? $this->hotels->firstWhere('id', $this->selectedHotelId)
            : null;

        $selectedTours = $this->tourPackages->whereIn('id', $this->selectedTourIds);

        return view('livewire.travel-recommendation', compact('selectedHotel', 'selectedTours', 'raw'));
    }
}

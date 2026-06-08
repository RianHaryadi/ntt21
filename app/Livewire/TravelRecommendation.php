<?php

namespace App\Livewire;

use App\Models\Hotel;
use App\Models\TourPackage;
use App\Models\TravelChatSession;
use Livewire\Component;

class TravelRecommendation extends Component
{
    public TravelChatSession $session;

    // Hotel & tour data from DB
    public $hotels;
    public $tourPackages;

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

        // Load all hotels and tour packages
        $this->hotels = Hotel::all();
        $this->tourPackages = TourPackage::all();

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
        $selectedHotel = $this->selectedHotelId
            ? $this->hotels->firstWhere('id', $this->selectedHotelId)
            : null;

        $selectedTours = $this->tourPackages->whereIn('id', $this->selectedTourIds);

        return view('livewire.travel-recommendation', compact('selectedHotel', 'selectedTours'));
    }
}

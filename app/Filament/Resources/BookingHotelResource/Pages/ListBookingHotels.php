<?php

namespace App\Filament\Resources\BookingHotelResource\Pages;

use App\Filament\Resources\BookingHotelResource;
use App\Models\BookingHotel;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ListBookingHotels extends ListRecords
{
    protected static string $resource = BookingHotelResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('')
                ->color('success')
                ->icon('heroicon-o-plus'),
            Actions\Action::make('export')
                ->label('Export')
                ->icon('heroicon-o-printer')
                ->color('primary')
                ->action(fn () => $this->exportCsv()),
        ];
    }

    protected function exportCsv(): StreamedResponse
    {
        $bookings = BookingHotel::query()->with('hotel')->latest()->get();

        return response()->streamDownload(function () use ($bookings) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, [
                'Booking Number', 'Hotel', 'Tipe Kamar', 'Nama Tamu', 'Email', 'Telepon',
                'Check-in', 'Check-out', 'Jumlah Malam', 'Total Harga', 'Metode Pembayaran',
                'Status', 'Dibuat',
            ]);

            foreach ($bookings as $b) {
                fputcsv($handle, [
                    $b->booking_number,
                    $b->hotel->name ?? '-',
                    $b->room_type,
                    $b->customer_name,
                    $b->customer_email,
                    $b->customer_phone,
                    optional($b->check_in_date)->format('Y-m-d'),
                    optional($b->check_out_date)->format('Y-m-d'),
                    $b->night_count,
                    $b->total_price,
                    $b->payment_method,
                    $b->status,
                    $b->created_at->format('Y-m-d H:i'),
                ]);
            }

            fclose($handle);
        }, 'booking-hotel-' . now()->format('Y-m-d-His') . '.csv', [
            'Content-Type' => 'text/csv',
        ]);
    }
}

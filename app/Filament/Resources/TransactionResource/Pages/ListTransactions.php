<?php

namespace App\Filament\Resources\TransactionResource\Pages;

use App\Filament\Resources\TransactionResource;
use App\Models\Transaction;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ListTransactions extends ListRecords
{
    protected static string $resource = TransactionResource::class;

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
        $transactions = Transaction::query()->latest()->get();

        return response()->streamDownload(function () use ($transactions) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, [
                'Booking Code', 'Nama Pelanggan', 'Email', 'Telepon', 'Tanggal Kunjungan',
                'Jumlah Tiket', 'Harga Paket', 'Diskon', 'Total Harga', 'Metode Pembayaran',
                'Status', 'Dibuat',
            ]);

            foreach ($transactions as $t) {
                fputcsv($handle, [
                    $t->booking_code,
                    $t->customer_name,
                    $t->customer_email,
                    $t->customer_phone,
                    optional($t->booking_date)->format('Y-m-d'),
                    $t->number_of_tickets,
                    $t->package_price,
                    $t->discount,
                    $t->total_price,
                    $t->payment_method,
                    $t->status,
                    $t->created_at->format('Y-m-d H:i'),
                ]);
            }

            fclose($handle);
        }, 'transactions-' . now()->format('Y-m-d-His') . '.csv', [
            'Content-Type' => 'text/csv',
        ]);
    }
}

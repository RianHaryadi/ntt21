<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BookingHotelResource\Pages;
use App\Models\BookingHotel;
use App\Models\Hotel;
use App\Models\CodePromotion;
use App\Models\HotelRoom;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Forms\Get;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Carbon\Carbon;
use Filament\Forms\Set;

class BookingHotelResource extends Resource
{
    protected static ?string $model = BookingHotel::class;
    protected static ?string $navigationIcon = 'heroicon-o-book-open';
    protected static ?string $navigationLabel = 'Bookings';
    protected static ?string $navigationGroup = 'Hotel Management';

    public static function form(Forms\Form $form): Forms\Form
    {
        return $form
            ->schema([
                // Nama customer
                Forms\Components\TextInput::make('customer_name')
                    ->label('Customer Name')
                    ->required(),

                // Email customer
                Forms\Components\TextInput::make('customer_email')
                    ->label('Customer Email')
                    ->required(),

                // Nomor telepon customer
                Forms\Components\TextInput::make('customer_phone')
                    ->label('Customer Phone')
                    ->required(),

                            // Pilih hotel
            Forms\Components\Select::make('hotel_id')
                ->label('Hotel')
                ->options(Hotel::all()->pluck('name', 'id'))
                ->required()
                ->reactive()
                ->afterStateUpdated(function (callable $set) {
                    // Reset harga dan total saat hotel berganti
                    $set('room_price', null);
                    $set('tax', null);
                    $set('service_charge', null);
                    $set('total_price', null);
                    $set('room_type', null); // Reset pilihan tipe kamar juga
                }),

            // Pilihan tipe kamar
            Forms\Components\Select::make('room_type')
                ->label('Room Type')
                ->options(function ($get) {
                    $hotel = Hotel::find($get('hotel_id'));
                    if (!$hotel) {
                        return [];
                    }

                    $pendingSingle = \App\Models\BookingHotel::where('hotel_id', $hotel->id)
                        ->where('room_type', 'single')
                        ->whereIn('status', ['pending', 'checked-in'])
                        ->count();
                    $pendingDouble = \App\Models\BookingHotel::where('hotel_id', $hotel->id)
                        ->where('room_type', 'double')
                        ->whereIn('status', ['pending', 'checked-in'])
                        ->count();
                    $pendingFamily = \App\Models\BookingHotel::where('hotel_id', $hotel->id)
                        ->where('room_type', 'family')
                        ->whereIn('status', ['pending', 'checked-in'])
                        ->count();

                    $sisaSingle = $hotel->room_count_single - $pendingSingle;
                    $sisaDouble = $hotel->room_count_double - $pendingDouble;
                    $sisaFamily = $hotel->room_count_family - $pendingFamily;

                    return [
                        'single' => 'Single (' . max($sisaSingle, 0) . ' tersedia)' . ($sisaSingle <= 0 ? ' - Penuh' : ''),
                        'double' => 'Double (' . max($sisaDouble, 0) . ' tersedia)' . ($sisaDouble <= 0 ? ' - Penuh' : ''),
                        'family' => 'Family (' . max($sisaFamily, 0) . ' tersedia)' . ($sisaFamily <= 0 ? ' - Penuh' : ''),
                    ];
                })
                ->required()
                ->reactive()
                ->afterStateUpdated(function (callable $set, $state, Get $get) {
                    $hotel = Hotel::find($get('hotel_id'));
                    $roomPrice = 0;

                    if ($hotel) {
                        switch ($state) {
                            case 'single':
                                $roomPrice = $hotel->single_room_price;
                                break;
                            case 'double':
                                $roomPrice = $hotel->double_room_price;
                                break;
                            case 'family':
                                $roomPrice = $hotel->family_room_price;
                                break;
                        }

                        // Pajak & service dihitung per malam, bukan per kamar
                        $nightCount = max((int) $get('night_count'), 1);
                        $tax = $roomPrice * 0.1;
                        $serviceCharge = $roomPrice * 0.05;

                        $set('room_price', $roomPrice);
                        $set('tax', $tax);
                        $set('service_charge', $serviceCharge);
                        // Total = (harga + pajak + service) × malam
                        $set('total_price', ($roomPrice + $tax + $serviceCharge) * $nightCount);
                    }
                }),

            // Harga kamar
            Forms\Components\TextInput::make('room_price')
                ->label('Room Price')
                ->prefix('Rp')
                ->disabled()
                ->dehydrated(true)
                ->numeric(),

                Forms\Components\DatePicker::make('check_in_date')
                    ->label('Check-in Date')
                    ->required()
                    ->reactive()
                    ->afterStateUpdated(function (callable $set, $state, Get $get) {
                        $checkInDate = Carbon::parse($state);
                        $checkOutDate = $get('check_out_date') ? Carbon::parse($get('check_out_date')) : null;

                        if ($checkInDate && $checkOutDate) {
                            $nightCount = max(1, $checkInDate->diffInDays($checkOutDate));
                            $set('night_count', $nightCount);
                            $roomPrice = (float) $get('room_price');
                            $tax = $roomPrice * 0.1;
                            $serviceCharge = $roomPrice * 0.05;

                            $set('tax', $tax);
                            $set('service_charge', $serviceCharge);
                            // Total = (harga + pajak + service) × malam
                            $set('total_price', ($roomPrice + $tax + $serviceCharge) * $nightCount);
                        }
                    }),

                Forms\Components\DatePicker::make('check_out_date')
                    ->label('Check-out Date')
                    ->required()
                    ->reactive()
                    ->afterStateUpdated(function (callable $set, $state, Get $get) {
                        $checkInDate = $get('check_in_date') ? Carbon::parse($get('check_in_date')) : null;
                        $checkOutDate = Carbon::parse($state);

                        if ($checkInDate && $checkOutDate) {
                            $nightCount = max(1, $checkInDate->diffInDays($checkOutDate));
                            $set('night_count', $nightCount);
                            $roomPrice = (float) $get('room_price');
                            $tax = $roomPrice * 0.1;
                            $serviceCharge = $roomPrice * 0.05;

                            $set('tax', $tax);
                            $set('service_charge', $serviceCharge);
                            // Total = (harga + pajak + service) × malam
                            $set('total_price', ($roomPrice + $tax + $serviceCharge) * $nightCount);
                        }
                    }),

                // Jumlah malam
                Forms\Components\TextInput::make('night_count')
                    ->label('Night Count')
                    ->dehydrated(true)
                    ->disabled(),

                // Pilihan kode promo
               // Bug fix: simpan promo_code_id (FK) bukan string code
                Select::make('promo_code_id')
                ->label('Promo Code')
                ->searchable()
                ->nullable()
                ->options(function () {
                    return CodePromotion::where('active', true)
                        ->whereDate('valid_from', '<=', now())
                        ->whereDate('valid_until', '>=', now())
                        ->pluck('code', 'id'); // value = id (FK), label = code
                })
                ->reactive()
                ->afterStateUpdated(function ($state, Set $set, Get $get) {
                    $discount = 0;

                    if ($state) {
                        $promo = CodePromotion::find($state);

                        if ($promo && $promo->isValid()) {
                            // Subtotal = (harga kamar + pajak + service) × malam
                            $roomPrice  = (float) $get('room_price');
                            $nightCount = max((int) $get('night_count'), 1);
                            $subtotal   = ($roomPrice * 1.15) * $nightCount; // 10% pajak + 5% service

                            // Bug fix: gunakan > 0, bukan !is_null()
                            if (!empty($promo->discount_percent) && $promo->discount_percent > 0) {
                                $discount = $subtotal * ($promo->discount_percent / 100);
                            } elseif (!empty($promo->discount_amount) && $promo->discount_amount > 0) {
                                $discount = $promo->discount_amount;
                            }
                        }
                    }

                    $set('discount_amount', $discount);
                    $roomPrice  = (float) $get('room_price');
                    $nightCount = max((int) $get('night_count'), 1);
                    $tax        = $roomPrice * 0.1;
                    $service    = $roomPrice * 0.05;
                    $set('total_price', max((($roomPrice + $tax + $service) * $nightCount) - $discount, 0));
                }),

                // Status booking — sesuai flow: pending → confirmed → checked-in → checked-out
                Forms\Components\Select::make('status')
                    ->label('Status')
                    ->options([
                        'pending'      => '⏳ Pending (Belum Bayar)',
                        'confirmed'    => '✅ Confirmed (Sudah Bayar)',
                        'checked-in'   => '🏨 Checked-In (Sedang Menginap)',
                        'checked-out'  => '🚪 Checked-Out (Selesai)',
                        'cancelled'    => '❌ Cancelled',
                    ])
                    ->default('pending')
                    ->required(),

                // Payment Method — nullable saja, tidak required (bisa belum diisi saat admin buat manual)
                Forms\Components\Select::make('payment_method')
                    ->label('Payment Method')
                    ->options([
                        'transfer' => 'Transfer',
                        'qris'     => 'QRIS',
                        'cash'     => 'Cash',
                    ])
                    ->nullable(),

                // Pajak
                Forms\Components\TextInput::make('tax')
                    ->label('Tax')
                    ->disabled()
                    ->dehydrated(true)
                    ->numeric(),

                // Biaya Layanan
                Forms\Components\TextInput::make('service_charge')
                    ->label('Service Charge')
                    ->disabled()
                    ->dehydrated(true)
                    ->numeric(),

                // Total Price
                Forms\Components\TextInput::make('total_price')
                    ->label('Total Price')
                    ->disabled()
                    ->dehydrated(true)
                    ->numeric(),

                // Nomor booking
                Forms\Components\TextInput::make('booking_number')
                    ->label('Booking Number')
                    ->required()
                    ->disabled()
                    ->dehydrated(true)
                    ->default(function () {
                        return 'BOOK-' . now()->format('Ymd') . '-' . str_pad(BookingHotel::count() + 1, 4, '0', STR_PAD_LEFT);
                    }),
            ]);
    }

   public static function table(Tables\Table $table): Tables\Table
{
    return $table
        ->columns([
            // Menambahkan kolom customer_name
            Tables\Columns\TextColumn::make('customer_name')
                ->label('Customer Name')
                ->sortable()
                ->searchable(),

            // Menambahkan kolom customer_email
            Tables\Columns\TextColumn::make('customer_email')
                ->label('Customer Email')
                ->sortable()
                ->searchable(),

            // Menambahkan kolom customer_phone
            Tables\Columns\TextColumn::make('customer_phone')
                ->label('Customer Phone')
                ->sortable(),

            // Menambahkan kolom night_count
            Tables\Columns\TextColumn::make('night_count')
                ->label('Night Count')
                ->sortable(),

            // Menambahkan kolom booking_number
            Tables\Columns\TextColumn::make('booking_number')
                ->searchable()            
                ->label('Booking Number')
                ->sortable(),
                
            Tables\Columns\TextColumn::make('room_type')
                ->label('Room Type')
                ->sortable()
                ->searchable(),

            Tables\Columns\TextColumn::make('check_in_date')
                ->label('Check-in Date')
                ->date('d/m/Y')
                ->sortable(),

            Tables\Columns\TextColumn::make('check_out_date')
                ->label('Check-out Date')
                ->date('d/m/Y')
                ->sortable(),

            Tables\Columns\TextColumn::make('status')
                ->label('Status')
                ->formatStateUsing(fn ($state) => match ($state) {
                    'pending'     => 'Pending',
                    'confirmed'   => 'Confirmed',
                    'checked-in'  => 'Checked-in',
                    'checked-out' => 'Checked-out',
                    'cancelled'   => 'Cancelled',
                    'canceled'    => 'Cancelled',
                    'failed'      => 'Failed',
                    default       => $state,
                })
                ->badge()
                ->color(fn ($state) => match ($state) {
                    'pending'     => 'warning',
                    'confirmed'   => 'info',
                    'checked-in'  => 'success',
                    'checked-out' => 'primary',
                    'cancelled',
                    'canceled',
                    'failed'      => 'danger',
                    default       => 'gray',
                })
                ->sortable(),

            Tables\Columns\TextColumn::make('payment_method')
                ->label('Payment')
                ->formatStateUsing(fn ($state) => match ($state) {
                    'transfer' => 'Transfer',
                    'qris'     => 'QRIS',
                    'cash'     => 'Cash',
                    default    => $state ?? '-',
                })
                ->badge()
                ->color(fn ($state) => match ($state) {
                    'transfer' => 'info',
                    'qris'     => 'success',
                    'cash'     => 'primary',
                    default    => 'gray',
                })
                ->sortable(),


            Tables\Columns\TextColumn::make('total_price')
                ->label('Total Price')
                ->money('IDR') // Menggunakan IDR untuk mata uang Indonesia
        ])
        ->filters([
            Tables\Filters\SelectFilter::make('status')
                ->options([
                    'pending'     => '⏳ Pending',
                    'confirmed'   => '✅ Confirmed',
                    'checked-in'  => '🏨 Checked-In',
                    'checked-out' => '🚪 Checked-Out',
                    'cancelled'   => '❌ Cancelled',
                ]),
        ])
        ->actions([
            // 1. CONFIRM — pending → confirmed (untuk booking manual admin tanpa Midtrans)
            Action::make('confirm')
                ->label('Confirm')
                ->icon('heroicon-m-check-badge')
                ->color('info')
                ->visible(fn ($record) => $record->status === 'pending')
                ->action(function ($record) {
                    $record->update(['status' => 'confirmed']);

                    \Filament\Notifications\Notification::make()
                        ->title('Booking dikonfirmasi — menunggu check-in tamu.')
                        ->success()
                        ->send();
                })
                ->requiresConfirmation()
                ->modalHeading('Konfirmasi Booking')
                ->modalDescription('Tandai booking ini sebagai sudah dikonfirmasi/dibayar?'),

            // 2. CHECK IN — confirmed → checked-in (tamu tiba fisik)
            Action::make('checkin')
                ->label('Check In')
                ->icon('heroicon-m-arrow-left-on-rectangle')
                ->color('success')
                ->visible(fn ($record) => $record->status === 'confirmed')
                ->action(function ($record) {
                    $roomType = $record->room_type;
                    $hotel    = $record->hotel;

                    // Verifikasi ulang ketersediaan kamar saat check-in
                    if (!$hotel->isRoomAvailable($roomType, $record->check_in_date, $record->check_out_date, $record->id)) {
                        \Filament\Notifications\Notification::make()
                            ->title('Kamar tipe ini sudah penuh untuk tanggal tersebut!')
                            ->danger()
                            ->send();
                        return;
                    }

                    $record->update(['status' => 'checked-in']);

                    // Buat record HotelRoom untuk kamar yang digunakan
                    try {
                        $roomNumber = \App\Models\HotelRoom::generateRoomNumber($roomType);
                        \App\Models\HotelRoom::create([
                            'booking_number'   => $record->booking_number,
                            'customer_name'    => $record->customer_name,
                            'room_type'        => $record->room_type,
                            'room_number'      => $roomNumber,
                            'status'           => 'not available',
                            'booking_hotel_id' => $record->id,
                        ]);
                    } catch (\Exception $e) {
                        \Filament\Notifications\Notification::make()
                            ->title('Check-in berhasil, tapi nomor kamar tidak bisa di-generate: ' . $e->getMessage())
                            ->warning()
                            ->send();
                    }

                    \Filament\Notifications\Notification::make()
                        ->title('Tamu berhasil check-in! 🏨')
                        ->success()
                        ->send();
                })
                ->requiresConfirmation()
                ->modalHeading('Check-In Tamu')
                ->modalDescription('Tandai tamu ini sebagai sudah check-in? Nomor kamar akan digenerate otomatis.'),

            // 3. CHECK OUT — checked-in → checked-out (tamu pergi)
            Action::make('checkout')
                ->label('Check Out')
                ->icon('heroicon-m-arrow-right-on-rectangle')
                ->color('primary')
                ->visible(fn ($record) => $record->status === 'checked-in')
                ->action(function ($record) {
                    $record->update(['status' => 'checked-out']);

                    // Bebaskan kamar
                    $hotelRoom = \App\Models\HotelRoom::where('booking_hotel_id', $record->id)->first();
                    if ($hotelRoom) {
                        $hotelRoom->update(['status' => 'available']);
                    }

                    \Filament\Notifications\Notification::make()
                        ->title('Tamu berhasil check-out! 🚪')
                        ->success()
                        ->send();
                })
                ->requiresConfirmation()
                ->modalHeading('Check-Out Tamu')
                ->modalDescription('Tandai tamu ini sebagai sudah check-out? Kamar akan dibebaskan.'),

            // 4. CANCEL — pending atau confirmed → cancelled
            Action::make('cancel')
                ->label('Cancel')
                ->icon('heroicon-m-x-circle')
                ->color('danger')
                ->visible(fn ($record) => in_array($record->status, ['pending', 'confirmed']))
                ->action(function ($record) {
                    $record->update(['status' => 'cancelled']);

                    // Bebaskan kamar jika sudah ada yang di-assign
                    $hotelRoom = \App\Models\HotelRoom::where('booking_hotel_id', $record->id)->first();
                    if ($hotelRoom) {
                        $hotelRoom->update(['status' => 'available']);
                    }

                    \Filament\Notifications\Notification::make()
                        ->title('Booking dibatalkan.')
                        ->warning()
                        ->send();
                })
                ->requiresConfirmation()
                ->modalHeading('Batalkan Booking')
                ->modalDescription('Yakin ingin membatalkan booking ini? Tindakan ini tidak bisa dibatalkan.'),

            Tables\Actions\EditAction::make()->label('Edit'),
        ])

        ->bulkActions([
            Tables\Actions\DeleteBulkAction::make()
                ->label('Delete All')
                ->requiresConfirmation()
                ->deselectRecordsAfterCompletion()
                ->successNotificationTitle('All selected bookings deleted!'),
        ]);
}

public static function getNavigationBadge(): ?string
{
    // Bug fix: hanya hitung booking yang butuh perhatian admin (pending)
    $count = static::getModel()::where('status', 'pending')->count();
    return $count > 0 ? (string) $count : null;
}

public static function getNavigationBadgeColor(): string | array | null
{
    $count = static::getModel()::where('status', 'pending')->count();

    return match (true) {
        $count === 0 => 'gray',
        $count < 5   => 'warning',
        default      => 'danger',
    };
}


    public static function getPages(): array
    {
        return [
            'index' => Pages\ListBookingHotels::route('/'),
            'create' => Pages\CreateBookingHotel::route('/create'),
            'edit' => Pages\EditBookingHotel::route('/{record}/edit'),
        ];
    }
    
    
}
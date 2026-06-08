<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TravelChatSessionResource\Pages;
use App\Models\TravelChatSession;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class TravelChatSessionResource extends Resource
{
    protected static ?string $model = TravelChatSession::class;

    protected static ?string $navigationIcon = 'heroicon-o-chat-bubble-left-right';
    protected static ?string $navigationLabel = 'Travel Chat Sessions';
    protected static ?string $modelLabel = 'Travel Chat Session';
    protected static ?string $pluralModelLabel = 'Travel Chat Sessions';
    protected static ?string $navigationGroup = 'User Interactions';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('session_token')
                    ->label('Session Token')
                    ->disabled(),

                Forms\Components\Select::make('user_id')
                    ->relationship('user', 'name')
                    ->label('User')
                    ->placeholder('Guest (Tamu)')
                    ->disabled(),

                Forms\Components\TextInput::make('status')
                    ->label('Status')
                    ->disabled(),

                Forms\Components\DateTimePicker::make('created_at')
                    ->label('Created At')
                    ->disabled(),

                Forms\Components\KeyValue::make('preferences')
                    ->label('User Preferences')
                    ->columnSpanFull()
                    ->disabled(),

                Forms\Components\Textarea::make('recommendation_raw')
                    ->label('AI Raw Recommendation')
                    ->rows(8)
                    ->columnSpanFull()
                    ->disabled(),

                Forms\Components\KeyValue::make('recommendation_edited')
                    ->label('User Customized Itinerary')
                    ->columnSpanFull()
                    ->disabled(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('session_token')
                    ->label('Session Token')
                    ->searchable()
                    ->copyable()
                    ->limit(20),

                Tables\Columns\TextColumn::make('user.name')
                    ->label('User')
                    ->placeholder('Guest (Tamu)')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'active' => 'warning',
                        'completed' => 'success',
                        default => 'gray',
                    })
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Created At')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'active' => 'Active',
                        'completed' => 'Completed',
                    ]),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getNavigationBadge(): ?string
    {
        return (string) static::getModel()::count();
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTravelChatSessions::route('/'),
            'view' => Pages\ViewTravelChatSession::route('/{record}'),
        ];
    }
}

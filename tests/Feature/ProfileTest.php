<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ProfileTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_view_profile_page(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('profile.edit'));

        $response->assertOk();
        $response->assertSee($user->name);
    }

    public function test_guest_cannot_view_profile_page(): void
    {
        $response = $this->get(route('profile.edit'));

        $response->assertRedirect(route('login'));
    }

    public function test_user_can_update_name_and_email(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->put(route('profile.update'), [
            'name' => 'Nama Baru',
            'email' => 'baru@example.com',
        ]);

        $response->assertSessionHasNoErrors();
        $this->assertEquals('Nama Baru', $user->fresh()->name);
        $this->assertEquals('baru@example.com', $user->fresh()->email);
    }

    public function test_cannot_update_email_to_one_already_taken(): void
    {
        $user = User::factory()->create();
        $other = User::factory()->create(['email' => 'taken@example.com']);

        $response = $this->actingAs($user)->put(route('profile.update'), [
            'name' => $user->name,
            'email' => 'taken@example.com',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertNotEquals('taken@example.com', $user->fresh()->email);
    }

    public function test_user_can_change_password_with_correct_current_password(): void
    {
        $user = User::factory()->create(['password' => Hash::make('oldpassword')]);

        $response = $this->actingAs($user)->put(route('profile.password'), [
            'current_password' => 'oldpassword',
            'password' => 'newpassword123',
            'password_confirmation' => 'newpassword123',
        ]);

        $response->assertSessionHasNoErrors();
        $this->assertTrue(Hash::check('newpassword123', $user->fresh()->password));
    }

    public function test_cannot_change_password_with_wrong_current_password(): void
    {
        $user = User::factory()->create(['password' => Hash::make('oldpassword')]);

        $response = $this->actingAs($user)->put(route('profile.password'), [
            'current_password' => 'wrongpassword',
            'password' => 'newpassword123',
            'password_confirmation' => 'newpassword123',
        ]);

        $response->assertSessionHasErrors('current_password');
        $this->assertTrue(Hash::check('oldpassword', $user->fresh()->password));
    }

    public function test_google_only_user_can_set_password_without_current_password(): void
    {
        $user = User::factory()->create(['password' => null, 'google_id' => 'google-123']);

        $response = $this->actingAs($user)->put(route('profile.password'), [
            'password' => 'newpassword123',
            'password_confirmation' => 'newpassword123',
        ]);

        $response->assertSessionHasNoErrors();
        $this->assertTrue(Hash::check('newpassword123', $user->fresh()->password));
    }

    public function test_user_can_upload_avatar(): void
    {
        Storage::fake('public');
        $user = User::factory()->create();
        $file = UploadedFile::fake()->image('avatar.jpg');

        $response = $this->actingAs($user)->post(route('profile.avatar'), [
            'avatar' => $file,
        ]);

        $response->assertSessionHasNoErrors();
        $this->assertNotNull($user->fresh()->avatar);
        Storage::disk('public')->assertExists($user->fresh()->avatar);
    }

    public function test_avatar_upload_rejects_non_image_files(): void
    {
        Storage::fake('public');
        $user = User::factory()->create();
        $file = UploadedFile::fake()->create('document.pdf', 100);

        $response = $this->actingAs($user)->post(route('profile.avatar'), [
            'avatar' => $file,
        ]);

        $response->assertSessionHasErrors('avatar');
    }
}

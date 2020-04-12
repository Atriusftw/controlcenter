<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class TrainingsTest extends TestCase
{
    use WithFaker, RefreshDatabase;

    /** @test */
    public function user_can_create_a_training_request()
    {
        $this->withoutExceptionHandling();

        $user = factory(\App\User::class)->create();
        \Auth::login($user);

        $attributes = [
            'experience' => $this->faker->numberBetween(1, 5),
            'englishOnly' => (int) $this->faker->boolean,
            'motivation' => $this->faker->realText(1500,2),
            'comment' => "",
            'training_level' => \App\Rating::find($this->faker->numberBetween(1,7))->id,
            'training_country' => \App\Country::find($this->faker->numberBetween(1,5))->id
        ];

        $this->assertJson($this->post('/training/store', $attributes)->content());
        $this->assertDatabaseHas('trainings', ['motivation' => $attributes['motivation']]);
    }

    /** @test */
    public function guest_cant_create_training_request()
    {
        $attributes = [
            'experience' => $this->faker->numberBetween(1, 5),
            'englishOnly' => (int) $this->faker->boolean,
            'motivation' => $this->faker->realText(1500,2),
            'comment' => "",
            'training_level' => \App\Rating::find($this->faker->numberBetween(1,7))->id,
            'training_country' => \App\Country::find($this->faker->numberBetween(1,5))->id
        ];

        $response = $this->post('/training/store', $attributes);
        $response->assertRedirect('/login');
    }

    /** @test */
    public function moderator_can_update_training_request()
    {

        $moderator = factory(\App\User::class)->create();
        $moderator->group = 2;
        $moderator->save();

        $training = factory(\App\Training::class)->create();

        $this->assertDatabaseHas('trainings', ['id' => $training->id]);

        $this->actingAs($moderator)
            ->patch($training->path(), $attributes = ['status' => 0])
            ->assertRedirect($training->path())
            ->assertSessionHas('message', 'Training successfully updated');

        $this->assertDatabaseHas('trainings', ['id' => $training->id, 'status' => $attributes['status']]);

    }

    /** @test */
    public function a_regular_user_cant_update_a_training()
    {

        $training = factory(\App\Training::class)->create();
        $user = $training->user;

        $this->assertDatabaseHas('trainings', ['id' => $training->id]);

        $user->group = 3;
        $user->save();

        $this->actingAs($user)
            ->patch($training->path(), $attributes = ['status' => 0])
            ->assertStatus(403);

    }

    // TODO add test for changing training status

}

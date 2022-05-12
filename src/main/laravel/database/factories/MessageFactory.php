<?php

namespace Database\Factories;

use App\Models\Message;
use App\Models\MessageTopic;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class MessageFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $sender =  random_int(1, 5) == 1 ? 2 : User::patient()->inRandomOrder()->first()->id;

        $topic_id = MessageTopic::inRandomOrder()
            ->where('id', '!=', $sender)
            ->first()
            ->id;

        $readBy = random_int(0, 1)
            ? 1
            : null;

        $shouldCall =  random_int(0, 1) && $readBy;

        return [
            'topic_id' => $topic_id,
            'body' => $this->faker->paragraph(10),
            'sender_id' => $sender,
            'read_by' => $readBy,
            'read_at' => $readBy ?  now() : null,
            'require_call' => $shouldCall ?  true : false,
            'called_at' => (random_int(0, 1) && $shouldCall) ?  now() : null,
            "created_at" => now()->subDays(random_int(1, 50)),
            "attachments" => [makeTestFile("pdf"), makeTestFile("pdf")],
            "voice_message" => makeTestFile("audio")
        ];
    }
}

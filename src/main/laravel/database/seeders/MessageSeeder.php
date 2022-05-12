<?php

namespace Database\Seeders;

use App\Models\Message;
use App\Models\MessageReply;
use App\Models\MessageTopic;
use Illuminate\Database\Seeder;

class MessageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        foreach ([
            "Informazioni",
            "Documenti",
            "Appuntamenti",
            "Altro",
        ]

            as $topic) {

            MessageTopic::firstOrCreate(['title' => $topic]);
        }

        Message::factory()->count(100)
            ->afterCreating(function (Message $message) {
                if (!$message->read_at || random_int(0, 1)) return;

                $message->reply()->create(
                    MessageReply::factory()->raw([
                        'author_id' => $message->read_by
                    ])
                );

                if ($message->canBeArchived()) $message->archive();
            })
            ->create();

        Message::factory()
            ->count(5)
            ->create([
                "attachments" => [],
                "voice_message" => null,
                'sender_id' => 1,
                'receiver_id' => 2,
                'from_bo' => true,
                'read_by' => null,
                'read_at' => random_int(0, 1) ? now() : null,
                'require_call' => false,
            ]);
    }
}

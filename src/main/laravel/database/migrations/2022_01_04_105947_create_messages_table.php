<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMessagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::dropIfExists('messages');

        Schema::create('messages', function (Blueprint $table) {
            $table->id();

            $table->foreignId("topic_id")
                ->nullable()
                ->constrained("message_topics", "id")
                ->onDelete("set null");

            $table->text("body");

            $table->json('attachments');

            $table->string('voice_message')
                ->nullable();

            $table->foreignId('sender_id')->nullable()
                ->constrained('users')
                ->onDelete('set null');

            $table->foreignId('receiver_id')
                ->nullable()
                ->constrained('users')
                ->onDelete('set null');

            $table->foreignId('read_by')->nullable()
                ->constrained('users')
                ->onDelete('set null');
            $table->timestamp('read_at')->nullable();

            $table->boolean('require_call')
                ->default(false);
            $table->timestamp('called_at')->nullable();

            $table->boolean('from_bo')
                ->default(false);

            $table->boolean('is_archived')
                ->default(false)
                ->index();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('messages');
    }
}

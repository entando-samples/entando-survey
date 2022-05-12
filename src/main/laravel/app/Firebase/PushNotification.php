<?php

namespace App\Firebase;

use Illuminate\Support\Facades\Http;

class PushNotification
{
    protected $title;
    protected $body;
    protected $image;
    protected $data;
    protected $topic;

    public static function make(string $topic, string $title, string $body = null, string $image = null): self
    {
        $obj = new static;

        $obj->topic = $topic;
        $obj->title = $title;
        $obj->body = $body;
        $obj->image = $image;

        return $obj;
    }

    public function withData(array $data): self
    {
        if (empty($data)) return $this;

        $this->data = array_merge($this->data ?? [], $data);

        return $this;
    }

    public function forNotifiable(Notifiable $notifiable): self
    {
        /**
         *
         * if the push notification is for specific user, change the
         * topic to that user and use current topic as type in data
         *
         */

        $this->data = array_merge($this->data ?? [], [
            'type' => $this->topic
        ]);

        $this->topic = $notifiable->getNotifiableId();

        return $this;
    }

    /**
     * send and returns push notification id
     *
     * @throws \App\Firebase\PushNotificationFailed
     */
    public function send(): ?int
    {

        $response = Http::asJson()
            ->withHeaders(['Authorization' => 'key=' . config('services.firebase.secret')])
            ->post('https://fcm.googleapis.com/fcm/send', $this->makeBody());

        if ($response->failed() || isset($response->json()["error"])) {
            throw new PushNotificationFailed($response);
        }

        return $response->json()["message_id"] ?? null;
    }

    protected function makeBody(): array
    {
        $body = [
            "to" => '/topics/' . $this->topic,
            "notification" => [
                "title" => $this->title,
            ],
        ];

        if ($this->body) {
            $body["notification"]['body'] = $this->body;
        }

        if ($this->image) {
            $body["notification"]['image'] = $this->image;
        }

        if ($this->data) {
            $body["data"] = $this->data;
        }

        return $body;
    }
}

<?php

namespace App\Listeners;

use App\Events\NewFollower;
use App\Interfaces\UserRepositoryInterface;
use App\User;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Mail;
use Tymon\JWTAuth\JWTAuth;

class NotifyFollowed
{
    /**
     * @var User
     */
    private $user;

    /**
     * @var JWTAuth
     */
    private $JWTAuth;

    /**
     * Create the event listener.
     *
     * @param User $user
     * @param JWTAuth $JWTAut
     */
    public function __construct(User $user, JWTAuth $JWTAut)
    {
        $this->user = $user;
        $this->JWTAuth = $JWTAut;
    }

    /**
     * Handle the event.
     *
     * @param  NewFollower $event
     * @return void
     */
    public function handle(NewFollower $event)
    {

        $token = $this->JWTAuth->fromUser($event->user);

//        $this->user->sendNotificationToFollowed($token);

        $data['email'] = $event->user->email;
        $data['name'] = $event->user->name;
        $data['subject'] = 'You`ve been followed at Photogram';

        $data['level'] = 'success';
        $data['actionText'] = 'Check Who`s Following You';
        $data['greeting'] = 'Hi there, ' . $event->user->username . '!';
        $data['actionUrl'] = env('APP_URL') . '/api/followers/?token='.$token;
        $data['introLines'] = [
            'You are receiving this email because you`ve been followed at Photogram.',
        ];
        $data['outroLines'] = [
            'Your popularity is growing. Well done young master.',
        ];
        $data['style'] = [
            'paragraph' => 'color: #666;',
            'body_action' => 'action',
        ];
        Mail::send('vendor.notifications.email', $data, function ($message) use ($data)
        {
            $message->from(env('MAIL_USERNAME'));
            $message->to($data['email'], $data['name'])->subject($data['subject']);
        });
    }
}

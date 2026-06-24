<?php

namespace App\Controllers;

use App\Models\KeysModel;
use App\Models\UserModel;
use CodeIgniter\I18n\Time;

class Telegram extends BaseController
{
    // UPDATE YOUR TOKEN HERE
    private $bot_token = '8717000068:AAECpXkFgXkAK6fDjzHPPq_QT006ypbWQ0g';

    public function webhook()
    {
        $content = file_get_contents('php://input');
        $update = json_decode($content);

        if (!$update) return $this->response->setStatusCode(200);

        if (isset($update->message) && isset($update->message->text)) {
            $chat_id = $update->message->chat->id;
            $from_id = $update->message->from->id;
            $command_text = trim($update->message->text);
            $this->processCommand($chat_id, $from_id, $command_text);
        } elseif (isset($update->callback_query)) {
            $chat_id = $update->callback_query->message->chat->id;
            $from_id = $update->callback_query->from->id;
            $data = $update->callback_query->data;
            $message_id = $update->callback_query->message->message_id;
            
            $this->processCallback($chat_id, $from_id, $data, $message_id);
            $this->answerCallbackQuery($update->callback_query->id);
        }
        
        return $this->response->setStatusCode(200);
    }

    private function processCommand($chat_id, $from_id, $command_text)
    {
        $userModel = new UserModel();

        // --- 1. START & LINKING ---
        if (strpos($command_text, '/start') === 0) {
            $parts = explode(' ', $command_text);
            if (count($parts) > 1 && strpos($parts[1], 'seller_') === 0) {
                $provided_key = trim($parts[1]);
                $dbUser = $userModel->where('seller_key', $provided_key)->where('status', 1)->first();
                if ($dbUser) {
                    $userModel->update($dbUser['id_users'], ['telegram_id' => $from_id]);
                    $this->sendMessage($chat_id, "✅ **Success!** Linked to: `" . $dbUser['username'] . "`", 'Markdown');
                } else {
                    $this->sendMessage($chat_id, "❌ **Invalid Seller Key.**");
                }
            } else {
                $this->sendMessage($chat_id, "👋 **Welcome!**\nLink your account using:\n`/start your_seller_key`", 'Markdown');
            }
            return;
        }

        // --- 2. SECURITY CHECK ---
        $dbUser = $userModel->where('telegram_id', $from_id)->where('status', 1)->first();
        if (!$dbUser) {
            $this->sendMessage($chat_id, "❌ **Access Denied.** Please link your account first.");
            return;
        }

        // --- 3. MAIN COMMANDS ---
        if (strpos($command_text, '/create') === 0) {
            $this->showGameMenu($chat_id);
        } elseif (strpos($command_text, '/reset') === 0) {
            $parts = explode(' ', $command_text, 2);
            if (count($parts) > 1) {
                $this->handleResetCommand($chat_id, trim($parts[1]), $dbUser);
            } else {
                $this->sendMessage($chat_id, "⚠️ Usage: `/reset YOUR-KEY-HERE`", 'Markdown');
            }
        } else {
            $status = ($dbUser['level'] == 1) ? 'Owner' : 'Reseller';
            $resp = "🤖 **VIP TEAM STORE**\n\n👤 User: `{$dbUser['username']}`\n🏅 Rank: `$status`\n\n🔑 `/create` - Generate Key\n🔄 `/reset <key>` - Reset Device";
            $this->sendMessage($chat_id, $resp, 'Markdown');
        }
    }

    private function showGameMenu($chat_id, $message_id = null)
    {
        $text = "🎮 ━━━ **SELECT PRODUCT** ━━━ 🎮";
        $keyboard = [
            'inline_keyboard' => [
                [['text' => '🎮 07 TEAM', 'callback_data' => 'sel_game:07TEAM'], ['text' => '🎮 LK TEAM', 'callback_data' => 'sel_game:LKTEAM']],
                [['text' => '🔥 FREE FIRE', 'callback_data' => 'sel_game:game'], ['text' => '🔫 PUBG', 'callback_data' => 'sel_game:PUBG']],
                [['text' => '🛡️ NITISH MODS', 'callback_data' => 'sel_game:NITISHMODS'], ['text' => '💎 IMMORTAL', 'callback_data' => 'sel_game:IMMORTAL']],
                [['text' => '⚡ STX CORP', 'callback_data' => 'sel_game:STXCORP'], ['text' => '🛒 STRICK BR', 'callback_data' => 'sel_game:STRICKBR']],
                [['text' => '📺 WTMods YT', 'callback_data' => 'sel_game:WTModsYT'], ['text' => '🔜 NEXT MENU', 'callback_data' => 'sel_game:NEXTMENU']],
                [['text' => '🚩 BR MODS', 'callback_data' => 'sel_game:BRMODS']]
            ]
        ];

        $message_id ? $this->editMessage($chat_id, $message_id, $text, 'Markdown', $keyboard) : $this->sendMessage($chat_id, $text, 'Markdown', $keyboard);
    }

    private function showDurationMenu($chat_id, $game_id, $message_id)
    {
        $text = "⏱ **Select Duration for:** `$game_id`";
        $keyboard = [
            'inline_keyboard' => [
                [['text' => '⏳ 1 Day (24 Hours)', 'callback_data' => "sel_dur:$game_id:24"]],
                [['text' => '🗓️ 10 Days', 'callback_data' => "sel_dur:$game_id:240"]],
                [['text' => '🗓️ 20 Days', 'callback_data' => "sel_dur:$game_id:480"]],
                [['text' => '🗓️ 30 Days', 'callback_data' => "sel_dur:$game_id:720"]],
                [['text' => '🔙 Back to Games', 'callback_data' => 'back_to_games']]
            ]
        ];
        $this->editMessage($chat_id, $message_id, $text, 'Markdown', $keyboard);
    }

    private function processCallback($chat_id, $from_id, $data, $message_id)
    {
        $userModel = new UserModel();
        $dbUser = $userModel->where('telegram_id', $from_id)->first();

        if (strpos($data, 'sel_game:') === 0) {
            $game_id = str_replace('sel_game:', '', $data);
            $this->showDurationMenu($chat_id, $game_id, $message_id);
        } elseif ($data === 'back_to_games') {
            $this->showGameMenu($chat_id, $message_id);
        } elseif (strpos($data, 'sel_dur:') === 0) {
            $parts = explode(':', $data); // sel_dur:game:hours
            $this->handleCreateCommand($chat_id, $parts[1], (int)$parts[2], 1, $dbUser, $message_id);
        }
    }

    private function handleCreateCommand($chat_id, $game, $hours, $max_devices, $dbUser, $message_id = null)
    {
        $keysModel = new KeysModel();
        $keyString = 'KEY-' . strtoupper(substr(md5(uniqid()), 0, 10));
        
        $time = new Time('now');
        $expired_date = $time->addHours($hours);
        
        $data = [
            'game' => $game,
            'user_key' => $keyString,
            'duration' => $hours,
            'expired_date' => $expired_date,
            'max_devices' => $max_devices,
            'status' => 1,
            'registrator' => $dbUser['username']
        ];
        
        if ($keysModel->insert($data)) {
            $text = "✅ **Key Created Successfully!**\n\n🎮 Game: `$game`\n🔑 Key: `{$keyString}`\n⏱ Duration: $hours Hours\n👤 Creator: {$dbUser['username']}";
        } else {
            $text = "❌ **Error:** Failed to save key in Database.";
        }
        
        $message_id ? $this->editMessage($chat_id, $message_id, $text, 'Markdown') : $this->sendMessage($chat_id, $text, 'Markdown');
    }

    private function handleResetCommand($chat_id, $key, $dbUser)
    {
        $keysModel = new KeysModel();
        $keyData = $keysModel->where('user_key', $key)->first();

        if ($keyData) {
            if ($dbUser['level'] > 1 && $keyData['registrator'] !== $dbUser['username']) {
                $this->sendMessage($chat_id, "❌ Permission Denied.");
                return;
            }
            $keysModel->set('devices', NULL)->where('user_key', $key)->update();
            $this->sendMessage($chat_id, "✅ Device Reset for: `{$key}`", 'Markdown');
        } else {
            $this->sendMessage($chat_id, "❌ Key not found.");
        }
    }

    private function sendMessage($chat_id, $text, $parse_mode = null, $reply_markup = null) {
        $this->callTelegramApi('sendMessage', ['chat_id' => $chat_id, 'text' => $text, 'parse_mode' => $parse_mode, 'reply_markup' => $reply_markup ? json_encode($reply_markup) : null]);
    }

    private function editMessage($chat_id, $message_id, $text, $parse_mode = null, $reply_markup = null) {
        $this->callTelegramApi('editMessageText', ['chat_id' => $chat_id, 'message_id' => $message_id, 'text' => $text, 'parse_mode' => $parse_mode, 'reply_markup' => $reply_markup ? json_encode($reply_markup) : null]);
    }

    private function answerCallbackQuery($id) { $this->callTelegramApi('answerCallbackQuery', ['callback_query_id' => $id]); }

    private function callTelegramApi($method, $payload) {
        $client = \Config\Services::curlrequest();
        $client->post("https://api.telegram.org/bot{$this->bot_token}/{$method}", ['form_params' => array_filter($payload)]);
    }
}

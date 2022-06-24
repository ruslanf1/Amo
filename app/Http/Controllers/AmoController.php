<?php

namespace App\Http\Controllers;

use App\Models\AmoModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class AmoController extends Controller
{
    public function amoKey()
    {
        $data = [
            'client_id' => '032f5287-ec0e-4125-b2f1-88ccf0d901c4',
            'client_secret' => 'r4ioF1B2yH9IgIfnJgx7hjKXuGiTWIxK0gmCBsDzUo022buz49luj00ht1NsN08D',
            'grant_type' => 'authorization_code',
            'code' => 'def5020034582af0c75e2f70ffb71d9ffecb95efb9e6afd0ce39f17e5250dc8878759f9da9e7b5db4866babf070dfa382a159849723edb67d2d28ea7ec04c277d93051663c875283909e6d76a4f0b7c7df8ee7e189be562243cc420a52a9b6cdca0f73832b6b50c99b2a4259ebf977cfaabfd6b847197e537eb55a38049dcddfde93f5f9baa6a6d7ed59778665df610401e13c56e049209c471bec6a72a6e1cc3d63f8e5f3ea4fcb5984c0bfbe5b602177ee8a971aea646902e12a453d0be3f7bc9d89f54af9da52fbe59624f0b386c92ca4bd22143ce576749bec1275c90103e1423a8f984bb5a64f4697ed5187448e90047cd85ab5b150b9948d3dfd8e53e322ba58af2b13d5dc8a1a04640b046cbf637dd1ff1a007283b550b41bfbaf4a632bb1cf6deda084c0f2aeea1255fbae78bc0d48ab3c8a069d37a01bd623ca99cb6e2d490324b32b390aebce8673b20737384bde8532326d19aa13e5d12d708f202b59771859587628f3dfd1e486b932a5c1d5d388e59b1f446caed489229b5cb4f898858fbfb612688460e309fbfef3cffd45231131d313d7b95ce5b6c2cdc9fa9e1693cd5fa3f23e46bc6535e2ca695c2ea3458c56bc04dc1024e40828675af141b6c97da44880b10c1e7397a39f6e0fdd5559b112032b3d87b7a87cb799886edc69b165a7bb625cfe',
            'redirect_uri' => 'https://lessons.ruslanf1.keenetic.link/'
        ];
        $domain = config('app.amoDomain');
        $response = Http::post("https://{$domain}.amocrm.ru/oauth2/access_token", $data);

        if(isset($response['access_token']) && $response['access_token'] != '' &&
        isset($response['refresh_token']) && $response['refresh_token'] != '' &&
        isset($response['expires_in']) && $response['expires_in'] > 0) {
            $amoModel = new AmoModel();
            $amoModel->__set('access_token', $response['access_token']);
            $amoModel->__set('refresh_token', $response['refresh_token']);
            $amoModel->__set('expires_in', time() + $response['expires_in']);
            $amoModel->save();
            return $amoModel;
        } else {
            return 0;
        }
    }

    public function getAmo() {
        $amo = AmoModel::find(1);
        if($amo['expires_in'] <= time()) {
            $newAmo = $this->editAmo($amo['refresh_token']);
            return $newAmo['access_token'];
        } else {
            return $amo['access_token'];
        }
    }

    public function editAmo($refresh_token) {
        $data = [
            'client_id' => '82281748-6462-4b05-9497-12fbef801743',
            'client_secret' => 'rU3CK0kjMVIqNajlpRB5E1Un4NyczMT2fqH5leHQgAVdlLAxNMilliX7Q1cIyEYC',
            'grant_type' => 'refresh_token',
            'refresh_token' => $refresh_token,
            'redirect_uri' => 'https://lessons.ruslanf1.keenetic.link/'
        ];
        $domain = config('app.amoDomain');
        $response = Http::post("https://{$domain}.amocrm.ru/oauth2/access_token", $data);

        if(isset($response['access_token']) && $response['access_token'] != '' &&
            isset($response['refresh_token']) && $response['refresh_token'] != '' &&
            isset($response['expires_in']) && $response['expires_in'] > 0 ) {
            $amoModel = new AmoModel(1);
            $amoModel->__set('access_token', $response['access_token']);
            $amoModel->__set('refresh_token', $response['refresh_token']);
            $amoModel->__set('expires_in', time() + $response['expires_in']);
            $amoModel->save();
            return $amoModel;
        } else {
            return 0;
        }
    }
}

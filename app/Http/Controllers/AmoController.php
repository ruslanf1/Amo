<?php

namespace App\Http\Controllers;

use App\Models\AmoModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Mockery\Exception;

class AmoController extends Controller
{
    public function amoKey()
    {
        $data = [
            'client_id' => '032f5287-ec0e-4125-b2f1-88ccf0d901c4',
            'client_secret' => 'r4ioF1B2yH9IgIfnJgx7hjKXuGiTWIxK0gmCBsDzUo022buz49luj00ht1NsN08D',
            'grant_type' => 'authorization_code',
            'code' => 'def502007f4fa6a859dc3aeb939f99784cfde8bf8ccd48cefd52578c905a8c9d27ef83a3219fb378abd3f1d202af6185a8f5e5046a16d98e6275251143e5fbcf581ab957e2848449bfe2989afd770eed01f3fb4d5a55b0097882e433c5dd5e3900809abd677240c28cf4b51d4486a31a306036304d69735f38f279d7e8ddc758b9d43153155cfe18a5be6310c20ce801db35ad47e8168b0b9fbebc8383a72ba9e8807c130fa083fe40a5c25aace492de2e1418988c4b980790eda320027700f1f75232c490e04faf86c373ddf556e1bf893ca565112ae117b55f4fd655c28d20f6c6bdfc9d6bdad118f89b5d8052c13fc461f5fd6cf70b0444e96021fa8757b2441f29b9eb88a3e416f9d0306d935f9150f1626d56623bbc8dba07356f518b8606a1ba5aae66a8a4d364f8f91b2f931d16042eb26ceee07d2471cf73fcfa0ef9630005a16a92b7b954b9c536a30da90b5762273e9e41efe1090ea10ad28f3c8c36b7ee807a9b01e0088017d0691bf824ca9242bd8c58e3b1be9f47ea8a7538f1081a63934b671a57ef468a1617e0b2c3eb1246f383692563d1bd2466ab25f9de545d7b710fcc1814906305b774a863d840c01dfb8734036848e90a05f111d36ead8cb8b6b6f5934cc28388aab1c3bc215527f3a15505849398ed54fd2be3cbcf684b8370170032f700',
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

    public function getKey() {
        $amo = AmoModel::find(1);
        if($amo['expires_in'] <= time()) {
            $newKey = $this->editKey($amo['refresh_token']);
            return $newKey['access_token'];
        } else {
            return $amo['access_token'];
        }
    }

    public function editKey($refresh_token) {
        $data = [
            'client_id' => '032f5287-ec0e-4125-b2f1-88ccf0d901c4',
            'client_secret' => 'r4ioF1B2yH9IgIfnJgx7hjKXuGiTWIxK0gmCBsDzUo022buz49luj00ht1NsN08D',
            'grant_type' => 'refresh_token',
            'refresh_token' => $refresh_token,
            'redirect_uri' => 'https://lessons.ruslanf1.keenetic.link/'
        ];
        $domain = config('app.amoDomain');
        $response = Http::post("https://{$domain}.amocrm.ru/oauth2/access_token", $data);

        if(isset($response['access_token']) && $response['access_token'] != '' &&
            isset($response['refresh_token']) && $response['refresh_token'] != '' &&
            isset($response['expires_in']) && $response['expires_in'] > 0 ) {
            $amoModel = AmoModel::find(1);;
            $amoModel->__set('access_token', $response['access_token']);
            $amoModel->__set('refresh_token', $response['refresh_token']);
            $amoModel->__set('expires_in', time() + $response['expires_in']);
            $amoModel->save();
            return $amoModel;
        } else {
            return 0;
        }
    }

    public function test() {
        $accessToken = $this->getKey();
        $data = [
            [
                "name" => 'Название сделки 1',
                "price" => 40000,
                "_embedded" => [
                    "contacts" => [
                        [
                            "first_name" => "Руслан",
                            "custom_fields_values" => [
                                [
                                    "field_code" => "EMAIL",
                                    "values" => [
                                        [
                                            "enum_code" => "WORK",
                                            "value" => "myagkikh87@mail.ru"
                                        ]
                                    ]
                                ],
                                [
                                    "field_code" => "PHONE",
                                    "values" => [
                                        [
                                            "enum_code" => "WORK",
                                            "value" => 89996334122
                                        ]
                                    ]
                                ]
                            ]
                        ]
                    ],
                    "companies" => [
                        [
                            "name" => "Рога и копыта"
                        ]
                    ]
                ],
            ]
        ];

        $domain = config('app.amoDomain');
        $response = Http::withHeaders(["Authorization" => "Bearer " . $accessToken, "Content-Type" => "application/json"])
            ->post("https://{$domain}.amocrm.ru/api/v4/leads/complex", $data);
        return $response;
    }
}

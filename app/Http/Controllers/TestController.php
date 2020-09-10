<?php

namespace App\Http\Controllers;

use App\Enumeration\OrderStatus;
use App\Enumeration\Role;
use App\Model\Item;
use App\Model\ItemCategory;
use App\Model\ItemImages;
use App\Model\Order;
use App\Model\User;
use GuzzleHttp\Exception\BadResponseException;
use GuzzleHttp\Exception\ClientException;
use Illuminate\Http\Request;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Mail;
use Image;

class TestController extends Controller
{
    public function fgTest() {
        // Login
        /*$client = new Client();
        $res = '';

        try {
            $res = $client->post('https://vendoradmin.fashiongo.net/api/login', [
                'body' => '{"username":"34","password":"34"}'
            ]);

            $res = json_decode($res->getBody(), true);
        } catch (BadResponseException $ex) {
            $res = $ex->getResponse();
            $res = (string) $res->getBody();

            $res = json_decode($res, true);
        }

        dd($res);*/

        // Upload File
        $client = new Client();
        $res = '';
        $url = 'https://vendoradmin.fashiongo.net/api-img-upload/upload';

        $headers = [
            'Authorization' => 'Bearer ' . 'eyJhbGciOiJIUzUxMiJ9.eyJ3aG9sZXNhbGVySWQiOjcwMjgsInJvbGUiOiJXaG9sZVNhbGVyIiwiZ3VpZCI6IjI1RDYzREU5LUFDNjEtNEY1RS05QUIwLTA2Q0Q1NEZDQ0I4NSIsInJlc291cmNlcyI6IiIsInNlY3VyaXR5VXNlcklkIjpudWxsLCJ1c2VyTmFtZSI6Im0yIiwiZXhwIjoxNTMzMzg5MTA3LCJzZWN1cml0eVVzZXJSb2xlIjpudWxsfQ.XYrbws2yI8JCdfrtTZ1F-BSLB8jZyCB31NZ_ZOUYZUntGnjBfsfEoWdMTQ2HpYemC1oHhA8HJL7ORYYb0DrEWQ',
            //'Accept' => '*/*',
            //'Content-Type' => 'multipart/form-data'
        ];


        //$cFile = curl_file_create('F:\37922641_996368580540451_6685028424840380416_n.jpg');
        $cFile = new \CURLFile('F:\37922641_996368580540451_6685028424840380416_n.jpg', 'image/jpg', '37922641_996368580540451_6685028424840380416_n.jpg');

        //dd($cFile);

        $post = array('type' => 'upload', 'data' => '{"dirName":"m2","wid":7028}','file'=> $cFile);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://vendoradmin.fashiongo.net/api-img-upload/upload');
        curl_setopt($ch, CURLOPT_POST,1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Authorization: Bearer eyJhbGciOiJIUzUxMiJ9.eyJ3aG9sZXNhbGVySWQiOjcwMjgsInJvbGUiOiJXaG9sZVNhbGVyIiwiZ3VpZCI6IjI1RDYzREU5LUFDNjEtNEY1RS05QUIwLTA2Q0Q1NEZDQ0I4NSIsInJlc291cmNlcyI6IiIsInNlY3VyaXR5VXNlcklkIjpudWxsLCJ1c2VyTmFtZSI6Im0yIiwiZXhwIjoxNTMzMzg5MTA3LCJzZWN1cml0eVVzZXJSb2xlIjpudWxsfQ.XYrbws2yI8JCdfrtTZ1F-BSLB8jZyCB31NZ_ZOUYZUntGnjBfsfEoWdMTQ2HpYemC1oHhA8HJL7ORYYb0DrEWQ'
        ));
        $result=curl_exec ($ch);
        curl_close ($ch);

        $res = json_decode($result, true);

        dd($res);

        try {
            //$f = fopen('images/item/original/d5167740-9564-11e8-bdea-a986e60025bf.jpg', 'r');
            $f = file_get_contents('images/item/original/d5167740-9564-11e8-bdea-a986e60025bf.jpg');


            $res = $client->post('https://vendoradmin.fashiongo.net/api-img-upload/upload', [
                'headers' => $headers,
                'form-data' => [
                    'type' => 'upload',
                    'data' => '{"dirName":"m2","wid":7028}',
                    'file' => $f,
                ]
            ]);
            //$fields["file"] = fopen('/path/to/file', 'rb');

            //$res = $client->request("POST", $url, $headers, array(), array(), $fields);

            $res = json_decode($res->getBody(), true);
        } catch (BadResponseException $ex) {
            $res = $ex->getResponse();


            $res = (string) $res->getBody();
            dd($res);
            $res = json_decode($res, true);
        }

        dd($res);


        // Create Item
        /*$client = new Client();
        $headers = [
            'Authorization' => 'Bearer ' . 'eyJhbGciOiJIUzUxMiJ9.eyJ3aG9sZXNhbGVySWQiOjcwMjgsInJvbGUiOiJXaG9sZVNhbGVyIiwiZ3VpZCI6IjI1RDYzREU5LUFDNjEtNEY1RS05QUIwLTA2Q0Q1NEZDQ0I4NSIsInJlc291cmNlcyI6IiIsInNlY3VyaXR5VXNlcklkIjpudWxsLCJ1c2VyTmFtZSI6Im0yIiwiZXhwIjoxNTMzMzgzNDE4LCJzZWN1cml0eVVzZXJSb2xlIjpudWxsfQ.e1_qSyAa0M6d17s6--kpFlLwqPac8B6X_HzFJ9nsOfYObI8jY0h4k861r0BgISFeIRgf59rkk-aVW_SW_4SzIQ',
            'Accept' => 'application/json',
            'content-type' => 'application/json'
        ];

        try {
            $res = $client->post('https://vendoradmin.fashiongo.net/api/item/save', [
                'headers' => $headers,
                'body' => '{
  "item": {
    "productName": "test",
    "vendorCategoryId": 33754,
    "sellingPrice": 34,
    "sizeId": 35333,
    "itemName": "werer",
    "parentParentCategoryId": 1,
    "parentCategoryId": 8,
    "categoryId": 254,
    "packId": 34945,
    "fabricDescription": "100% ACRYLIC",
    "madeIn": "IMPORT",
    "inventoryStatusId": 1,
    "labelTypeId": 1,
    "prePackYN": "Y",
    "stockAvailability": "Arrives Soon / Back Order",
    "evenColorYN": false,
    "active": false,
    "fashionGoExclusive": false,
    "cateId": null,
    "inActive": "test",
    "crossSellCount": null,
    "colorCount": 1
  },
  "inventory": {
    "update": [
      {
        "active": true,
        "available": true,
        "availableQty": null,
        "inventoryId": null,
        "sizeName": null,
        "colorId": 463050,
        "productId": null
      }
    ],
    "delete": []
  },
  "image": {
    "update": [
      {
        "active": true,
        "colorName": null,
        "colorId": "463050",
        "imageName": "7028-1533297433855-37922641_996368580540451_6685028424840380420_n.jpg",
        "imageUrl": "https://www.fameaccessories.com/images/product/detail/106b02_pink_4b_1.jpg",
        "listOrder": 1,
        "productImageId": null,
        "productId": 0,
        "loaded": true
      }
    ],
    "delete": []
  },
  "crossSell": {
    "update": [],
    "delete": []
  },
  "changedInfo": {
    "oldPictureGeneral": null,
    "newPictureGeneral": "7028-1533297433855-37922641_996368580540451_6685028424840380420_n.jpg",
    "oldProductName": null,
    "packId": 34945,
    "active": false
  },
  "inventoryV2": {
    "saved": [
      {
        "productId": null,
        "inventoryPrepack": [
          {
            "active": true,
            "availableOn": null,
            "inventoryId": null,
            "sizeName": null,
            "qtyUpdated": false,
            "colorId": 463050,
            "colorName": "BLACK",
            "productId": null,
            "qty": 999,
            "status": "In Stock",
            "statusCode": 1,
            "threshold": 0,
            "invUpdated": true
          }
        ]
      }
    ],
    "deleted": []
  },
  "productId": null
}'
            ]);

            $res = json_decode($res->getBody(), true);
        } catch (BadResponseException $ex) {
            $res = $ex->getResponse();
            $res = (string) $res->getBody();

            $res = json_decode($res, true);
        }

        dd($res);*/
    }

    public function changeDC() {
        /*$items = Item::all();

        foreach ($items as $item) {
            ItemCategory::create([
                'item_id' => $item->id,
                'default_parent_category' => $item->default_parent_category,
                'default_second_category' => $item->default_second_category,
                'default_third_category' => $item->default_third_category,
            ]);
        }*/
    }

    public function mailTest() {
        try {
            Mail::send('emails.test', [], function ($message) {
                $message->subject('New Order - 3 smtp'.date('Y-m-d H:i a'));
                $message->to('shantotrs@gmail.com');
            });
        } catch(\Exception $e) {
            dd($e->getMessage());
        }

        if( count(Mail::failures()) > 0 ) {
            foreach(Mail::failures() as $email_address) {
                dd($email_address);
            }

        } else {
            dd("No errors, all sent successfully!");
        }


        dd('sdf');
    }

    public function changeImageSize() {
        $items = Item::all();

        foreach ($items as $item) {
            $images = $item->images;

            foreach ($images as $image) {
                $listSavePath = $image->list_image_path;
                if ($listSavePath != null) {
                    $img = Image::make(public_path($image->image_path))->resize(1000, 1500);
                    $img->save(public_path($listSavePath), 100);
                }
            }
        }
    }

    public function sort() {
        Item::where([])->update(['sorting' => null]);
    }

    public function orderCount() {
        $oders = Order::where('status', '!=', OrderStatus::$INIT)->distinct()->get(['user_id'])->pluck('user_id')->toArray();

        $users = User::whereIn('id', $oders)->where('role', Role::$BUYER)->where('updated_at', null)->get();

        dd($users);

        foreach ($users as $user) {
            $user->order_count = $user->orders_count;
            $user->save();
        }
    }
}

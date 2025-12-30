<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class GameController extends Controller
{
    //
    public function top()
    {
        return view('top');
    }
    public function donuts()
    {
        // ドーナツの名前（ここで変更可能）
        $donuts = [
            'ドーナツ1',
            'ドーナツ2',
            'ドーナツ3',
            'ドーナツ4',
            'ドーナツ5',
            'ドーナツ6',
            'ドーナツ7',
            'ドーナツ8',
            'ドーナツ9',
            'ドーナツ10',
        ];
        
        // プレイヤーの名前（ここで変更可能）
        $players = [
            'プレイヤー1',
            'プレイヤー2',
            'プレイヤー3',
            'プレイヤー4',
        ];
        
        return view('Donuts.index', [
            'donuts' => $donuts,
            'players' => $players,
        ]);
    }
}

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CatalogController extends Controller
{
    // おはじきの色定義
    private $colors = [
        ['name' => '赤', 'value' => 'red', 'hex' => '#ff6b6b'],
        ['name' => 'オレンジ', 'value' => 'orange', 'hex' => '#ffa500'],
        ['name' => '黄', 'value' => 'yellow', 'hex' => '#ffd93d'],
        ['name' => '緑', 'value' => 'green', 'hex' => '#6bcf7f'],
        ['name' => '青', 'value' => 'blue', 'hex' => '#4d96ff'],
        ['name' => '紫', 'value' => 'purple', 'hex' => '#9d84ff'],
        ['name' => 'ピンク', 'value' => 'pink', 'hex' => '#ff9ec5'],
    ];
    
    // シート定義（ここで変更可能）
    private $sheets = [
        [
            'id' => 'food',
            'title' => '好きな食べ物',
            'type' => 'image',
            'sheetName' => 'food',
        ],
        [
            'id' => 'sweets',
            'title' => '好きなお菓子',
            'type' => 'image',
            'sheetName' => 'sweets',
        ],
        [
            'id' => 'type',
            'title' => '好きなタイプ',
            'type' => 'text',
            'items' => [
                '優しい人',
                '面白い人',
                '真面目な人',
                '元気な人',
                '落ち着いた人',
                'アクティブな人',
                'クリエイティブな人',
                '頼れる人',
                '明るい人',
                '優雅な人',
                '個性的な人',
                '誠実な人',
                '情熱的な人',
                '冷静な人',
                '温かい人',
                'クールな人',
                '自由な人',
                '責任感のある人',
                '創造的な人',
                '協調性のある人',
            ],
        ],
    ];
    
    private function getRandomPastelColor()
    {
        $pastels = [
            '#ffb3ba', '#ffdfba', '#ffffba', '#baffc9', '#bae1ff',
            '#e0bbff', '#ffc0cb', '#ffd1dc', '#ffe4e1', '#fff0f5',
            '#f0e68c', '#dda0dd', '#98d8c8', '#f7dc6f', '#bbdefb',
        ];
        return $pastels[array_rand($pastels)];
    }
    
    public function index(Request $request)
    {
        $sheetId = $request->get('sheet', $this->sheets[0]['id']);
        $currentSheet = collect($this->sheets)->firstWhere('id', $sheetId) ?? $this->sheets[0];
        
        // 文字タイプの場合、各アイテムにパステルカラーを割り当て
        if ($currentSheet['type'] === 'text' && isset($currentSheet['items'])) {
            $currentSheet['itemsWithColors'] = array_map(function($item) {
                return [
                    'text' => $item,
                    'color' => $this->getRandomPastelColor(),
                ];
            }, $currentSheet['items']);
        }
        
        return view('Catalogs.index', [
            'colors' => $this->colors,
            'sheets' => $this->sheets,
            'currentSheet' => $currentSheet,
        ]);
    }
}

@extends('base')
@section('title', 'Donuts')
@push('styles')
<style>
    .donuts-container {
        display: flex;
        gap: 2rem;
        padding: 2rem;
        max-width: 1400px;
        margin: 0 auto;
    }
    .ranking-section {
        flex: 1;
        background: #fff;
        padding: 1.5rem;
        border-radius: 8px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }
    .control-section {
        width: 300px;
        background: #fff;
        padding: 1.5rem;
        border-radius: 8px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }
    .rank-item {
        display: flex;
        align-items: center;
        gap: 1rem;
        padding: 0.75rem;
        margin-bottom: 0.5rem;
        border: 1px solid #e0e0e0;
        border-radius: 4px;
    }
    .rank-number {
        font-weight: bold;
        color: #333;
        min-width: 40px;
    }
    .answer-select, .guess-select {
        flex: 1;
        padding: 0.5rem;
        border: 1px solid #ddd;
        border-radius: 4px;
        color: #333;
    }
    .check-btn {
        padding: 0.5rem 1rem;
        background: #ff9eae;
        color: #fff;
        border: none;
        border-radius: 4px;
        cursor: pointer;
        transition: background 0.2s;
    }
    .check-btn:hover {
        background: #ff7a8f;
    }
    .check-btn:disabled {
        background: #ccc;
        cursor: not-allowed;
    }
    .correct {
        background: #d4edda;
        border-color: #c3e6cb;
    }
    .incorrect {
        background: #f8d7da;
        border-color: #f5c6cb;
    }
    .hidden-answer {
        color: transparent !important;
        background: #f0f0f0 !important;
        pointer-events: none;
    }
    .hidden-answer option {
        display: none;
    }
    .control-section h3 {
        color: #333;
        font-size: 1.2rem;
        margin-bottom: 1rem;
    }
    .score-display {
        font-size: 1.5rem;
        color: #333;
        margin-bottom: 1.5rem;
        padding: 1rem;
        background: #f8f9fa;
        border-radius: 4px;
    }
    .control-btn {
        width: 100%;
        padding: 0.75rem;
        margin-bottom: 0.5rem;
        border: none;
        border-radius: 4px;
        cursor: pointer;
        font-size: 1rem;
        transition: background 0.2s;
    }
    .hide-btn {
        background: #ff9eae;
        color: #fff;
    }
    .hide-btn:hover {
        background: #ff7a8f;
    }
    .next-btn {
        background: #6c757d;
        color: #fff;
    }
    .next-btn:hover {
        background: #5a6268;
    }
    .end-btn {
        background: #dc3545;
        color: #fff;
    }
    .end-btn:hover {
        background: #c82333;
    }
    .current-player {
        margin-bottom: 1rem;
        padding: 0.5rem;
        background: #fff3cd;
        border-radius: 4px;
        text-align: center;
        color: #856404;
    }
</style>
@endpush
@section('content')
<div class="donuts-container">
    <div class="ranking-section">
        <h2 style="color: #333; margin-bottom: 1.5rem;">ランキング</h2>
        <div id="currentPlayer" class="current-player">現在のプレイヤー: <span id="playerName">{{ $players[0] }}</span></div>
        <div id="rankingList">
            @for($i = 1; $i <= count($donuts); $i++)
            <div class="rank-item" data-rank="{{ $i }}">
                <span class="rank-number">{{ $i }}位</span>
                <select class="answer-select" data-rank="{{ $i }}" id="answer-{{ $i }}">
                    <option value="">正解を選択</option>
                    @foreach($donuts as $index => $donut)
                    <option value="donut{{ $index + 1 }}">{{ $donut }}</option>
                    @endforeach
                </select>
                <select class="guess-select" data-rank="{{ $i }}" id="guess-{{ $i }}" disabled>
                    <option value="">回答を選択</option>
                    @foreach($donuts as $index => $donut)
                    <option value="donut{{ $index + 1 }}">{{ $donut }}</option>
                    @endforeach
                </select>
                <button class="check-btn" data-rank="{{ $i }}" id="check-{{ $i }}" disabled>確定</button>
            </div>
            @endfor
        </div>
    </div>
    <div class="control-section">
        <h3>ゲーム情報</h3>
        <div class="score-display">
            <div>総ポイント: <span id="totalScore">0</span> / <span id="maxTotalScore">{{ count($players) * count($donuts) }}</span></div>
            <div>今のゲーム: <span id="currentScore">0</span> / <span id="maxCurrentScore">{{ count($donuts) }}</span></div>
        </div>
        <button class="control-btn hide-btn" id="hideBtn">正解を隠す</button>
        <button class="control-btn next-btn" id="nextBtn" disabled>次のゲーム</button>
        <button class="control-btn end-btn" id="endBtn">ゲームを終了</button>
    </div>
</div>

@push('scripts')
<script>
// コントローラーから渡されたデータ
const donuts = @json($donuts);
const players = @json($players);
const donutCount = donuts.length;
const playerCount = players.length;
const maxTotalScore = playerCount * donutCount;
const maxCurrentScore = donutCount;

// ドーナツ名を値から取得する関数
function getDonutName(value) {
    const index = parseInt(value.replace('donut', '')) - 1;
    return donuts[index] || value;
}

let currentPlayer = 0;
let totalScore = 0;
let currentGameScore = 0;
let isHidden = false;
let answers = {};
let checkedRanks = new Set();

// 正解を隠すボタン
document.getElementById('hideBtn').addEventListener('click', function() {
    if (!validateAnswers()) {
        alert('すべての順位に正解を入力してください。');
        return;
    }
    
    // 正解を保存
    for (let i = 1; i <= donutCount; i++) {
        const answerSelect = document.getElementById(`answer-${i}`);
        answers[i] = answerSelect.value;
        // 選択肢を完全に非表示にする - 選択された値を保存してから「???」のみの選択肢に変更
        const selectedValue = answerSelect.value;
        answerSelect.setAttribute('data-original-value', selectedValue);
        // すべての選択肢を削除して「???」のみにする
        answerSelect.innerHTML = '<option value="' + selectedValue + '">???</option>';
        answerSelect.classList.add('hidden-answer');
        answerSelect.disabled = true;
        // 視覚的にも完全に隠す
        answerSelect.style.color = '#f0f0f0';
        answerSelect.style.backgroundColor = '#f0f0f0';
    }
    
    // 回答入力欄を有効化
    for (let i = 1; i <= donutCount; i++) {
        document.getElementById(`guess-${i}`).disabled = false;
        // 確定ボタンを有効化
        document.getElementById(`check-${i}`).disabled = false;
    }
    
    isHidden = true;
    this.disabled = true;
});

// 確定ボタン
document.querySelectorAll('.check-btn').forEach(btn => {
    btn.addEventListener('click', function() {
        const rank = parseInt(this.dataset.rank);
        const answerSelect = document.getElementById(`answer-${rank}`);
        const guessSelect = document.getElementById(`guess-${rank}`);
        
        if (!guessSelect.value) {
            alert('回答を選択してください。');
            return;
        }
        
        const answer = answers[rank];
        const guess = guessSelect.value;
        const rankItem = this.closest('.rank-item');
        
        // 正解を表示（「???」から実際のドーナツ名に変更）
        const answerText = getDonutName(answer);
        answerSelect.innerHTML = `<option value="${answer}">${answerText}</option>`;
        answerSelect.style.color = '#333';
        answerSelect.style.backgroundColor = '';
        
        if (answer === guess) {
            rankItem.classList.add('correct');
            currentGameScore++;
            totalScore++;
        } else {
            rankItem.classList.add('incorrect');
        }
        
        this.disabled = true;
        guessSelect.disabled = true;
        checkedRanks.add(rank);
        
        updateScores();
        
        // すべての順位を確認したら次のゲームボタンを有効化
        if (checkedRanks.size === donutCount) {
            document.getElementById('nextBtn').disabled = false;
        }
    });
});

// 次のゲームボタン
document.getElementById('nextBtn').addEventListener('click', function() {
    if (currentPlayer >= playerCount - 1) {
        alert('全プレイヤーのゲームが終了しました！');
        return;
    }
    
    currentPlayer++;
    currentGameScore = 0;
    isHidden = false;
    answers = {};
    checkedRanks.clear();
    
    // リセット
    document.querySelectorAll('.rank-item').forEach(item => {
        item.classList.remove('correct', 'incorrect');
    });
    
    document.querySelectorAll('.answer-select').forEach((select, index) => {
        const rank = index + 1;
        // 選択肢を元に戻す
        select.innerHTML = '<option value="">正解を選択</option>';
        donuts.forEach((donutName, j) => {
            const option = document.createElement('option');
            option.value = `donut${j + 1}`;
            option.textContent = donutName;
            select.appendChild(option);
        });
        select.value = '';
        select.classList.remove('hidden-answer');
        select.disabled = false;
        select.style.color = '';
        select.style.backgroundColor = '';
        select.removeAttribute('data-original-value');
    });
    
    document.querySelectorAll('.guess-select').forEach(select => {
        select.value = '';
        select.disabled = true;
    });
    
    document.querySelectorAll('.check-btn').forEach(btn => {
        btn.disabled = true;
    });
    
    document.getElementById('hideBtn').disabled = false;
    this.disabled = true;
    
    document.getElementById('playerName').textContent = players[currentPlayer];
    updateScores();
});

// ゲームを終了ボタン
document.getElementById('endBtn').addEventListener('click', function() {
    if (confirm('ゲームを終了しますか？')) {
        alert(`ゲーム終了！\n総ポイント: ${totalScore} / ${maxTotalScore}`);
        // 必要に応じてリダイレクトなど
    }
});

function validateAnswers() {
    for (let i = 1; i <= donutCount; i++) {
        const answerSelect = document.getElementById(`answer-${i}`);
        if (!answerSelect.value) {
            return false;
        }
    }
    return true;
}

function updateScores() {
    document.getElementById('currentScore').textContent = currentGameScore;
    document.getElementById('totalScore').textContent = totalScore;
}
</script>
@endpush
@endsection

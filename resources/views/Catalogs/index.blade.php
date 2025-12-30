@extends('base')
@section('title', 'Catalog')
@section('content')
<style>
    .drag-area {
        position: relative;
        width: 100%;
        height: 300px;
        border: 2px solid #ccc;
    }
    .draggable-box {
  display: flex;
  justify-content: center;
  align-items: center;
  position: absolute;
  width: 100px;
  height: 100px;
  text-align: center;
  color: #fff;
  background: #f09896;
  cursor: grab;
  touch-action: none;
}
.draggable-box.dragging {
  cursor: grabbing;
}
</style>
<div class="container">
    <div class="row">
        <div class="col-12">
            <h1>Catalog</h1>
        </div>
    </div>
    <div id="drag-area" class="drag-area">
        <div id="draggable-box" class="draggable-box">Drag me</div>
    </div>
</div>
<script>
    const box = document.getElementById('draggable-box');
const area = document.getElementById('drag-area');

let offsetX = 0;
let offsetY = 0;
let isDragging = false;

const getEventPosition = (e) => {
  if (e.touches) {
    return {
      x: e.touches[0].clientX,
      y: e.touches[0].clientY
    };
  }
  return {
    x: e.clientX,
    y: e.clientY
  };
};

const startDrag = (e) => {
  e.preventDefault();
  const pos = getEventPosition(e);
  const rect = box.getBoundingClientRect();
  offsetX = pos.x - rect.left;
  offsetY = pos.y - rect.top;
  isDragging = true;
  box.classList.add('dragging');
};

const duringDrag = (e) => {
  if (!isDragging) return;

  const pos = getEventPosition(e);
  const areaRect = area.getBoundingClientRect();

  let left = pos.x - areaRect.left - offsetX;
  let top = pos.y - areaRect.top - offsetY;

  const maxLeft = area.clientWidth - box.offsetWidth;
  const maxTop = area.clientHeight - box.offsetHeight;

  left = Math.max(0, Math.min(left, maxLeft));
  top = Math.max(0, Math.min(top, maxTop));

  box.style.left = left + 'px';
  box.style.top = top + 'px';
};

const endDrag = () => {
  isDragging = false;
  box.classList.remove('dragging');
};

const adjustPosition = () => {
  const areaRect = area.getBoundingClientRect();
  const maxLeft = area.clientWidth - box.offsetWidth;
  const maxTop = area.clientHeight - box.offsetHeight;

  const currentLeft = parseFloat(box.style.left) || 0;
  const currentTop = parseFloat(box.style.top) || 0;

  const newLeft = Math.max(0, Math.min(currentLeft, maxLeft));
  const newTop = Math.max(0, Math.min(currentTop, maxTop));

  box.style.left = newLeft + 'px';
  box.style.top = newTop + 'px';
};

// === イベント登録 ===
// マウス
box.addEventListener('mousedown', startDrag);
document.addEventListener('mousemove', duringDrag);
document.addEventListener('mouseup', endDrag);

// タッチ
box.addEventListener('touchstart', startDrag, { passive: false });
document.addEventListener('touchmove', duringDrag, { passive: false });
document.addEventListener('touchend', endDrag);

window.addEventListener('resize', adjustPosition);
</script>
@endsection

@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/profiles/edit.css') }}">
@endsection

@section('content')
<div class="profile-container">
    <h1 class="profile-title">プロフィール編集</h1>
    <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        <div class="form-group">
            <label for="img_url" class="form-label">プロフィール画像</label>
<div class="profile-image-parent">
    <div class="profile-image-container">
        @if ($profile->img_url)
            <img id="profile-image-preview" src="{{ asset('storage/' . $profile->img_url) }}" alt="Profile Image" class="profile-image">
        @else
            <img id="profile-image-preview" class="profile-image" style="display: none;">
            <div class="default-image" id="default-image"></div>
        @endif
    </div>
</div>
            <input type="file" class="form-control-file" id="img_url" name="img_url" onchange="previewImage(event)">
        </div>
        <div class="form-group">
            <label for="name" class="form-label">ユーザー名</label>
            <input type="text" class="form-control" id="name" name="name" value="{{ $profile->user->name }}">
        </div>
        <div class="form-group">
            <label for="post_code" class="form-label">郵便番号（ハイフンなし）</label>
            <input type="text" class="form-control" id="post_code" name="post_code" value="{{ $profile->post_code }}">
        </div>
        <div class="form-group">
            <label for="address" class="form-label">住所</label>
            <input type="text" class="form-control" id="address" name="address" value="{{ $profile->address }}">
        </div>
        <div class="form-group">
            <label for="building" class="form-label">建物名</label>
            <input type="text" class="form-control" id="building" name="building" value="{{ $profile->building }}">
        </div>
        <button type="submit" class="btn btn-primary">更新する</button>
    </form>
</div>
@endsection

@section('js')
<script>
function previewImage(event) {
    var reader = new FileReader();
    reader.onload = function(){
        var output = document.getElementById('profile-image-preview');
        output.src = reader.result;
        output.style.display = 'block'; // プレビュー画像を表示する
        var defaultImage = document.getElementById('default-image');
        if (defaultImage) {
            defaultImage.style.display = 'none'; // デフォルト画像を非表示にする
        }
    };
    reader.readAsDataURL(event.target.files[0]);
}
</script>
@endsection

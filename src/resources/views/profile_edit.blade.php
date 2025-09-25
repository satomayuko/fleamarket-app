@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/profile_edit.css') }}">
@endsection

@section('content')
<div class="profile-form__content">
  <div class="profile-form__heading">
    <h2>プロフィール設定</h2>
  </div>

  <form class="form" method="POST" action="{{ route('mypage.profile.update') }}" enctype="multipart/form-data">
    @csrf
    @method('PATCH')

    <div class="avatar">
      <div class="avatar__img">
        <img
          src="{{ $profile->avatar_path ? asset('storage/' . $profile->avatar_path) : asset('images/avatar-placeholder.png') }}"
          alt="avatar"
        >
      </div>

      <label for="avatar" class="avatar__button">画像を選択する</label>
      <input id="avatar" type="file" name="avatar" accept=".jpeg,.jpg,.png" class="avatar__input">
      <div class="form__error">
        @error('avatar') {{ $message }} @enderror
      </div>
    </div>

    <div class="form__group">
      <div class="form__group-title">
        <span class="form__label--item">ユーザー名</span>
      </div>
      <div class="form__group-content">
        <div class="form__input--text">
          <input type="text" name="name" value="{{ old('name', Auth::user()->name) }}">
        </div>
        <div class="form__error">
          @error('name') {{ $message }} @enderror
        </div>
      </div>
    </div>

    <div class="form__group">
      <div class="form__group-title">
        <span class="form__label--item">郵便番号</span>
      </div>
      <div class="form__group-content">
        <div class="form__input--text">
          <input type="text" name="postal" placeholder="123-4567"
                 value="{{ old('postal', $profile->postal_code) }}">
        </div>
        <div class="form__error">
          @error('postal') {{ $message }} @enderror
        </div>
      </div>
    </div>

    <div class="form__group">
      <div class="form__group-title">
        <span class="form__label--item">住所</span>
      </div>
      <div class="form__group-content">
        <div class="form__input--text">
          <input type="text" name="address"
                 value="{{ old('address', $profile->address) }}">
        </div>
        <div class="form__error">
          @error('address') {{ $message }} @enderror
        </div>
      </div>
    </div>

    <div class="form__group">
      <div class="form__group-title">
        <span class="form__label--item">建物名</span>
      </div>
      <div class="form__group-content">
        <div class="form__input--text">
          <input type="text" name="building"
                 value="{{ old('building', $profile->building) }}">
        </div>
        <div class="form__error">
          @error('building') {{ $message }} @enderror
        </div>
      </div>
    </div>

    <div class="form__button">
      <button class="form__button-submit" type="submit">更新する</button>
    </div>
  </form>
</div>
@endsection

@section('scripts')
<script>
document.getElementById('avatar').addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(event) {
            document.querySelector('.avatar__img img').src = event.target.result;
        }
        reader.readAsDataURL(file);
    }
});
</script>
@endsection
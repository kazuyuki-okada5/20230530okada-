<!DOCTYPE html>
<html lang="ja">
<head>
<meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Flea Market App</title>
    <link rel="stylesheet" href="{{ asset('css/sanitize.css') }}">
    <link rel="stylesheet" href="{{ asset('css/auth/register.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    @yield('css')
</head>
<body>
    <div class="page-wrapper">
      <header class="header">
        <div class="header__inner">
           <div class="header__logo">
              <img src="{{ asset('storage/logo (1).svg') }}" alt="Flea Market App Logo" class="logo">
            </div>
          </div>
    </header>
    </div>
    <div class="register__content">
      <div class="register-form__heading">
        <h1>会員登録</h1>
      </div>
      <form class="form" action="/register" method="post">
        @csrf
          <div class="form__group">
            <div class="form__group-title">
              <span class="form__label--item">お名前</span>
            </div>
            <div class="form__group-content">
              <div class="form__input--text">
                <input type="text" name="name" value="{{ old('name') }}" />
              </div>
              <div class="form__error">
                @error('name')
                  {{ $message }}
                @enderror
              </div>
            </div>
          </div>
          <div class="form__group">
            <div class="form__group-title">
              <span class="form__label--item">メールアドレス</span>
            </div>
            <div class="form__group-content">
              <div class="form__input--text">
                <input type="email" name="email" value="{{ old('email') }}" />
              </div>
            <div class="form__error">
              @error('email')
                {{ $message }}
              @enderror
            </div>
          </div>
        </div>
        <div class="form__group">
          <div class="form__group-title">
            <span class="form__label--item">パスワード</span>
          </div>
          <div class="form__group-content">
            <div class="form__input--text">
              <input type="password" name="password" />
            </div>
            <div class="form__error">
              @error('password')
                {{ $message }}
              @enderror
            </div>
          </div>
        </div>
        <div class="form__group">
          <div class="form__group-title">
            <span class="form__label--item">確認用パスワード</span>
          </div>
          <div class="form__group-content">
            <div class="form__input--text">
              <input type="password" name="password_confirmation" />
            </div>
          </div>
        </div>
        <div class="form__button">
          <button class="form__button-submit" type="submit">登録</button>
        </div>
      </form>
      <div class="login__link">
        <a class="login__button-submit" href="/login">ログインの方はこちら</a>
      </div>
      <div class="item__link">
        <a class="item__button-submit" href="/">トップページへ戻る</a>
      </div>
    </div>
</body>
</html>
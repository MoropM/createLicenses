@extends('layouts.app')
@section('main_login_content')
    <section class="content__login">
      <div class="content__login__form">
        <div class="content__login__form__logo"></div>
        <div class="content__login__form__inputs">
          <form action="" method="post">
            @csrf
            
          </form>
        </div>
      </div>
      <div class="content__login__descrption"></div>
    </section>
@endsection
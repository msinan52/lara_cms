<div class="widget widget-dashboard">
    <ul class="list">
        @auth()
            <li class="{{ Request::is('kullanici/hesabim') ? 'active':'' }}"><a href="{{route('kullanici.user.dashboard')}}">Hesabım</a></li>
            <li class="{{ Request::is('kullanici/profil') ? 'active':'' }}"><a href="{{route('kullanici.user.detail')}}">Profil</a></li>
            <li class="{{ Request::is('kullanici/siparisler') ? 'active':'' }}"><a href="{{route('siparisView')}}">Siparişlerim</a></li>
            <li class="{{ Request::is('kullanici/adresler') ? 'active':'' }}"><a href="{{ route('kullanici.address') }}">Adreslerim</a></li>
            <li class="{{ Request::is('kullanici/favorilerim') ? 'active':'' }}"><a href="{{ route('kullanici.favorites.list') }}">Favorilerim</a></li>
            <li class="{{ Request::is('kullanici/hata-kodlari') ? 'active':'' }}"><a href="{{route('kullanici.user.userLogErrors')}}">Hata Kodlarım</a></li>
        @elseauth()
            <li class="{{ Request::is('kullanici/favorilerim') ? 'active':'' }}"><a href="{{ route('kullanici.favorites.list') }}">Favorilerim</a></li>
        @endauth
    </ul>
</div>

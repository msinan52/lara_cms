@extends('admin.layouts.master')
@section('title','Log Listesi')
@section('content')
    <div class="box box-default">
        <div class="box-body with-border">
            <div class="row">
                <div class="col-md-10">
                    <a href="{{ route('admin.home_page') }}"> <i class="fa fa-home"></i> Anasayfa</a>
                    › Hatalar
                </div>
                <div class="col-md-2 text-right mr-3">
                    <a href="{{ route('admin.logs') }}"><i class="fa fa-refresh"></i>&nbsp;Yenile</a>
                    <a href="{{ route('admin.log.delete_all') }}"><i class="fa fa-trash"></i>&nbsp;Tümünü sil</a>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-xs-12">
            <div class="box ">
                <div class="box-header">
                    <h3 class="box-title">Log(Hata) Kayıtları</h3>

                    <div class="box-tools">
                        <form action="{{ route('admin.logs') }}" method="get" id="form">
                            <div class="row">
                                <div class="col-md-3 input-group input-group-sm hidden-xs  pull-right">
                                    <input type="text" name="q" class="form-control pull-right" placeholder="Log ara.." value="{{ request('q') }}">

                                    <div class="input-group-btn">
                                        <button type="submit" class="btn btn-default"><i class="fa fa-search"></i></button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                <!-- /.box-header -->
                <div class="box-body table-responsive">
                    <table class="table table-hover table-bordered">
                        <tbody>
                        <tr>
                            <th>Id</th>
                            <th>Kullanıcı Id</th>

                            <th>Url</th>
                            <th>Mesaj</th>
                            <th>Exception</th>
                            <th>Code</th>
                            <th>Level</th>
                            <th>Oluşturulma Tarihi</th>
                            <th>Sil</th>
                        </tr>
                        @foreach($list as $l)
                            <tr>
                                <td><a href="{{ route('admin.log.show',$l->id) }}">{{ $l->id }}</a></td>
                                <td><a href="{{ route('admin.user.edit',$l->user_id) }}"> {{ $l->user_id }}</a></td>

                                <td>{{ $l ->url }} </td>
                                <td>{{ str_limit($l->message,100) }}</td>
                                <td>{{ str_limit($l->exception, $limit = 50, $end = '...') }}</td>
                                <td>{{ $l ->code }}</td>
                                <td>{{ $l ->level }}</td>
                                <td>{{ $l->created_at }}</td>
                                <td><a href="{{ route('admin.log.delete',$l->id) }}" onclick="return confirm('Silmek istediğine emin misin ?')"><i class="fa fa-trash-o"></i></a>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>

                    </table>
                    <div class="text-right"> {{ $list->links() }}</div>
                </div>

                <!-- /.box-body -->
            </div>

            <!-- /.box -->
        </div>
    </div>
@endsection

@extends('layouts.app')

@section('title')
    {{$item->title}} | コンテンツダウンロード
@endsection

@section('content')
<div class="container">
    <span>{{$item->title }}の購入が完了しました。以下のリンクよりダウンロードください。</span>
    <form method="POST" action="{{ route('download',[$item->id]) }}" class="p-5">
        @csrf  
        <div class="form-group mb-0 mt-3">
            <button type="submit" class="btn btn-block btn-secondary">
                コンテンツをダウンロードする
            </button>
        </div>
    </form>
</div>
@endsection

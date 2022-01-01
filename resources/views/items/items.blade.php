
@extends('layouts.app')

@section('title')
    コンテンツ一覧
@endsection

@section('content')
<div class="container">
    <div class="row">
        @foreach ($items as $item)
            <div class="col-3 mb-3">
                <div class="card">
                    <div class="position-relative overflow-hidden">
                        <img class="card-img-top" src="/storage/item-images/{{$item->image_file_name}}">
                        <div class="position-absolute py-2 px-3" style="left: 0; bottom: 20px; color: white; background-color: rgba(0, 0, 0, 0.70)">
                            <i class="fas fa-yen-sign"></i>
                            <span class="ml-1">{{number_format($item->price)}}</span>
                        </div>
                    </div>
                    <div class="card-body">
                        <small class="text-muted">{{$item->itemCategory->category}}</small>
                        <h5 class="card-title">{{$item->title}}</h5>
                    </div>
                    <a href="{{ route('item', [$item->id]) }}" class="stretched-link"></a>
                </div>
            </div>
        @endforeach
    </div>
    <div class="d-flex justify-content-center">
         {{ $items->withQueryString()->links() }}
     </div>
</div>

<a href="{{route('sell')}}"
   class="bg-secondary text-white d-inline-block d-flex justify-content-center align-items-center flex-column"
   role="button"
   style="position: fixed; bottom: 30px; right: 30px; width: 150px; height: 150px; border-radius: 75px;"
>
    <div style="font-size: 24px;">出品</div>
    <div>
        <i class="fas fa-camera" style="font-size: 30px;"></i>
    </div>
</a>
@endsection
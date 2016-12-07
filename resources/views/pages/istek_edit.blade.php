@extends('pages.layout')
@section('title','Istek_redaktə')
@section('content')
<style type="text/css">
 form img{
    width: 60px;
    height: 60px;
    float: left;
    margin-left: 5px;
  }
  .img-wrap {
    display:inline-block;
    position: relative;
    /*margin-top: 20px;*/
    float: left;
  }
  .img-wrap .close {
    color:red;
    cursor: ;
    position: absolute;
    top: 2px;
    right: 4px;
    z-index: 100;
    opacity:1 !important;
    font-size: 20px !important;
}
  form span{
    color: red;
  }
</style>
<div id="breadcrumb">
<div class="container">
   <div class="row">
      <h1 class="text-left">Redaktə</h1>
  </div>
</div>
</div>
@if(Auth::user())
  <section id="add">
  <div class="container">
    <div class="row">
      <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
        <div id="map"></div>
      </div>
      <div class="col-lg-8 col-md-8 col-sm-8 col-xs-12">
        @if (Session::has('istek_edited'))
          <div class="alert alert-success" role="alert">{{Session::get('istek_edited')}}</div>
        @endif

        <div id="ajaxErrorImage"></div>

        @if ($errors->has('about') || $errors->has('about') || $errors->has('lat') || $errors->has('lng') || $errors->has('nov') || $errors->has('date'))
              <span class="help-block">
                <div class="alert alert-danger"><p>Ulduz ilə işarəli xanaları boş saxlamayın.</p></div>
              </span>
        @endif

        <form action="{{url('/istek-edit/'.$istek_edit->id)}}" method="POST" enctype="multipart/form-data">
          {{csrf_field()}}
          {{ method_field('PATCH')}}
          {{-- <=================title input ================> --}}
          <div class="col-lg-6">
            <div class="form-group{{ $errors->has('title') ? ' has-error' : '' }}">
              <label for="name">Başlıq<SPAN> *</SPAN></label>
              <input type="text" name="title" class="form-control" value="{{$istek_edit->title}}">
              {{-- @if ($errors->has('title'))
                 <span class="help-block">
                   <strong>Boşluq buraxmayın</strong>
                 </span>
              @endif --}}
            </div>

            {{-- <=================location input ================> --}}
            <div class="form-group{{ $errors->has('location') || $errors->has('lat') && $errors->has('lng')? ' has-error' : '' }}">
              <label for="name">Ünvan<SPAN> *</SPAN></label>
                 <input type="hidden" id="lat" name="lat" value="{{$istek_edit->lat}}">
                  <input type="hidden" id="lng" name="lng" value="{{$istek_edit->lng}}">
              <input type="text" name="location" class="form-control" id="adress" placeholder="" value="{{$istek_edit->location}}">
             {{--  @if ($errors->has('location'))
                  <span class="help-block">
                    <strong>Boşluq buraxmayın</strong>
                  </span>
                @elseif($errors->has('lat') && $errors->has('lng'))
                  <span class="help-block">
                    <strong>Boşluq buraxmayın </strong>
                  </span>
              @endif --}}
            </div>

            {{-- <=================organization input ================> --}}
            <div class="form-group">
              <label for="name">Təşkilat adı</label>
              <input type="text" name="org" class="form-control" value="{{$istek_edit->org}}">
            </div>

            {{-- <=================About input ================> --}}
            <div class="form-group{{ $errors->has('about') ? ' has-error' : '' }}">
              <label for="name">Açıqlama<SPAN> *</SPAN></label>
              <textarea name="about" class="form-control" rows="6" cols="80">{{$istek_edit->about}}</textarea>
             {{--  @if ($errors->has('about'))
                  <span class="help-block">
                    <strong>Boşluq buraxmayın</strong>
                  </span>
              @endif --}}
            </div>
          {{-- <=================image input ================> --}}
            <div id="afterImage" class="form-group{{ $errors->has('image') ? ' has-error' : '' }}">
              <label for="email">Şəkil<SPAN> *</SPAN></label>
              <a class="forImg form-control btn btn-default">Şəkil Seç</a>
              <input id="uploadAjax" type="file" name="image" class="hidden imgInput form-control" value="{{$istek_edit->image}}">
              {{-- @if ($errors->has('image'))
                  <span class="help-block">
                    <strong>Boşluq buraxmayın</strong>
                  </span>
              @endif --}}
            </div>
            {{-- image show from DB --}}
           @foreach($istek_edit->shekiller as $pic)
                <div class="img-wrap" imagename="{{$pic->imageName}}" data-remove="{{$pic->imageName}}">
                    <span class="close"  imagename="{{$pic->imageName}}">&times;</span>
                  <img class="im_" imagename="{{$pic->imageName}}" src="{{url('/image/'.$pic->imageName)}}" alt="İstək image" />
                  </div>
            @endforeach

            {{-- els id for ajax image upload --}}
            <input type="hidden" id="forPicsAjax" value="{{$istek_edit->id}}">

                <div class="images_">
                @foreach($istek_edit->shekiller as $pic)
                    <input class="picsArray" imagename="{{$pic->imageName}}"  type="hidden" name="picsArray[{{$pic->imageName}}]" value="1" >
                 @endforeach
                </div>
            </div>
          <div class="col-lg-6">

            {{-- <=================Name input ================> --}}
            <div class="form-group{{ $errors->has('name') ? ' has-error' : '' }}">
              <label for="city">Ad, Soyad<SPAN> *</SPAN></label>
              <input type="text" class="form-control" name="name" value="{{Auth::user()->name}}">
             {{--  @if ($errors->has('name'))
                  <span class="help-block">
                    <strong>Boşluq buraxmayın</strong>
                  </span>
              @endif --}}
            </div>

            {{-- <=================Phone input ================> --}}
            <div class="form-group{{ $errors->has('phone') ? ' has-error' : '' }}">
              <label for="operator">Əlaqə nömrəsi<SPAN> *</SPAN></label>
              <div class="input-group">
                  <div class="input-group-addon">
                      <input id="operator" type="hidden" name="operator" value="{{substr(Auth::user()->phone,4,2) == '55' ? '55' : substr(Auth::user()->phone,4,2) }}">
                      +994
                          <select id="operator-numbers" name="operator-numbers">
                            <option {{substr(Auth::user()->phone,4,2) == '55' ? 'selected' : '' }}>55</option>
                            <option {{substr(Auth::user()->phone,4,2) == '51' ? 'selected' : '' }}>51</option>
                            <option {{substr(Auth::user()->phone,4,2) == '50' ? 'selected' : '' }}>50</option>
                            <option {{substr(Auth::user()->phone,4,2) == '70' ? 'selected' : '' }}>70</option>
                            <option {{substr(Auth::user()->phone,4,2) == '77' ? 'selected' : '' }}>77</option>
                          </select>
                      </div>
                <input type="text" class="form-control" name="phone" value="{{substr(Auth::user()->phone,6)}}" maxlength="7">
                {{-- @if ($errors->has('phone'))
                    <span class="help-block">
                      <strong>Boşluq buraxmayın</strong>
                    </span>
                @endif --}}
              </div>
            </div>

            {{-- <=================Email input ================> --}}
            <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
              <label for="password">Email<SPAN> *</SPAN></label>
              <input type="email" name="email" class="form-control" placeholder="Email" value="{{Auth::user()->email}}">
              {{-- @if ($errors->has('email'))
                <span class="help-block">
                  <strong>Boşluq buraxmayın</strong>
                </span>
            @endif --}}
            </div>

            {{-- <=================Nov input ================> --}}
            <div class="form-group{{ $errors->has('nov') ? ' has-error' : '' }}">
              <label for="password">Növ<SPAN> *</SPAN></label>
              <input type="text" name="nov" class="form-control" value="{{$istek_edit->nov}}">
             {{--  @if ($errors->has('nov'))
                  <span class="help-block">
                    <strong>Boşluq buraxmayın</strong>
                  </span>
              @endif --}}
            </div>

            {{-- <=================Date input ================> --}}
            <div class="form-group">
              <label for="date">İstəyin müddəti<SPAN> *</SPAN></label>
              <input type="date" name="date" class="form-control" id="date" value="{{$istek_edit->deadline}}">
            </div>
            <div class="form-group text-center">
              <input type="submit" class="btn" value="GÖNDƏR">
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
  </section>
@endif

@endsection
@section('scripts')
  <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAanmTrOlQYWRepobnwqSO1E2SOoHYMRBA&libraries=places&callback=initAutocomplete&language=az"
         async defer></script>
@endsection

<form enctype="multipart/form-data"  action="/importers" method="POST">
    <input type="file" name="htmlFile" accept=".html">
    <input type="submit" value="Import & Store data">
    @if($errors->any())
        <span style="color: red; display: block">{{ $errors->first() }}</span>
    @endif
</form>
@if(Session::has('success'))
    <span style="color: green; display: block">{!! Session::get('success') !!}</span>
@endif
@if(Session::has('download'))
    <meta http-equiv="refresh" content="5;url={{ url(Storage::url(Session::get('download'))) }}">
@endif
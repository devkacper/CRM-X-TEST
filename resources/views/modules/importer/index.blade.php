<form enctype="multipart/form-data"  action="/importers" method="POST">
    <input type="file" name="htmlFile" accept=".html">
    <input type="submit" value="Import & Store data">
    @if($errors->any())
        <span style="color: red; display: block">{{ $errors->first() }}</span>
    @endif
</form>
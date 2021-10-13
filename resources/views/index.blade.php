<!DOCTYPE html>
<html lang="en">
<head>
    <title>Data Migration</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.10.0-12/css/all.min.css" />
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">
    <link href="{{assets('admin_assets/css/sb-admin-2.css') }}" rel="stylesheet">
    <link href="{{assets('admin_assets/css/custom.css') }}" rel="stylesheet">
    <link href="{{assets('admin_assets/vendor/datatables/dataTables.bootstrap4.min.css') }}" rel="stylesheet">
</head>
<body>
<div id="app">
    <app></app>
</div>
<script src="{{assets('admin_assets/vendor/jquery/jquery.min.js') }}"></script>
<script src="{{assets('admin_assets/vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
<script src="{{assets('admin_assets/vendor/jquery-easing/jquery.easing.min.js') }}"></script>
<script src="{{assets('admin_assets/js/sb-admin-2.min.js') }}"></script>

<script>window.baseUrl = '{{url('/')}}';</script>
<script>window.publicPath = '{{env('PUBLIC_PATH')}}';</script>
<script src="{{assets('js/app.js')}}"></script>
</body>
</html>
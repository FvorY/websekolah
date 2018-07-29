<!DOCTYPE html>
<html>

    <head>

        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">

        <title>DBoard | Login </title>

        <link rel="shortcut icon" type="image/png" href="{{ asset('assets/img/dboard/logo/faveicon.png') }}"/>

        <!-- Bootstrap -->
        <link href="{{ asset('assets/vendors/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet">
        <!-- Font Awesome -->
        <link href="{{ asset('assets/vendors/font-awesome/css/font-awesome.min.css') }}" rel="stylesheet">

        <!-- Custom Theme Style -->
        <link href="{{ asset('assets/css/inspiniaStyle.css') }}" rel="stylesheet">
        <link href="{{ asset('assets/css/inspiniaAnimate.css') }}" rel="stylesheet">

        <link href="{{asset('assets/vendors/ladda/ladda-themeless.min.css')}}" rel="stylesheet">

    </head>
    <style>

        #grad {
            background: red; /* For browsers that do not support gradients */
            background: -webkit-linear-gradient(-90deg, #296488, #42A5F5); /* For Safari 5.1 to 6.0 */
            background: -o-linear-gradient(-90deg, #296488, #42A5F5); /* For Opera 11.1 to 12.0 */
            background: -moz-linear-gradient(-90deg, #296488, #42A5F5); /* For Firefox 3.6 to 15 */
            background: linear-gradient(-90deg, #296488, #42A5F5); /* Standard syntax */
        }


    </style>

    <body class="gray-bg" id="grad" >
        <div class="text-center" style="padding-top:20px;">
            <!-- <img src="{{ asset('assets/img/dboard/logo/sublogo2.png') }}"><br> -->
            <div style="margin-bottom:120px;">

            </div>
            <a style="padding-left:2px; font-size: 28px; color: white" href="{{ url('/home') }}"><b>Web Sekolah</b></a>
        </div>
        <div class="passwordBox animated fadeInLeft">

            <div class="row">

                <div class="col-md-12">
                    <div class="ibox-content" style="margin-top: 0px; padding: 25px; border-radius: 15px;" >

                        <div class="row">
                            <div class="alert alert-danger pesan" style="display:none;">
                                <strong>Peringatan :</strong>
                                <ul></ul>
                            </div>
                            <form class="m-t" role="form" id="login-form">
                                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                <div class="form-group">
                                    <div class="input-group m-t">
                                        <span class="input-group-addon"> <i class="fa fa-user"></i></span>
                                        <input type="text" class="form-control " placeholder="Nama Pengguna" required name="username" id="username">
                                    </div>
                                    <span style="padding-left: 40px;color:#ed5565;display:none;" class="help-block m-b-none" id="username-error"><small>Nama pengguna harus diisi.</small></span>
                                </div>
                                <div class="form-group">
                                    <div class="input-group m-t">
                                        <span class="input-group-addon"> <i class="fa fa-key"></i></span>
                                        <input type="password" class="form-control" placeholder="Kata Sandi" required name="password" id="password">
                                    </div>
                                    <span style="padding-left: 40px;color:#ed5565; display:none;" class="help-block m-b-none" id="password-error"><small>Kata sandi ini wajib diisi !</small></span>
                                </div>
                                <button type="button" class="ladda-button  ladda-button-demo  btn btn-primary block full-width m-b" data-style="zoom-in">Masuk</button>

                            </form>
                        </div>
                    </div>
                </div>
            </div>

        </div>


        <script type="text/javascript" src="{{ asset('assets/plugins/jquery-1.12.3.min.js') }}"></script>

        <!-- Ladda -->
        <script src="{{ asset('assets/vendors/ladda/spin.min.js') }}"></script>
        <script src="{{ asset('assets/vendors/ladda/ladda.min.js') }}"></script>
        <script src="{{ asset('assets/vendors/ladda/ladda.jquery.min.js') }}"></script>

        <script type="text/javascript">
var info       = $('.pesan');
var buttonLadda = $('.ladda-button-demo').ladda();
var baseUrl = '{{ url('/') }}';
$('.ladda-button').click(function () {
    if (validateForm()) {
        buttonLadda.ladda('start');
        $.ajax({
            url: baseUrl+'/login',
            type: 'get',
            timeout: 10000,
            data: $('#login-form').serialize(),
            dataType: 'json',
            success: function (response) {

                if (response.status == 'sukses') {
                    if (response.content == 'authenticate') {
                        window.location = baseUrl + '/dashboard';
                    }
                } else if (response.status == 'gagal') {
                    if (response.content == 'Inputan Nama dan Password Tidak Sesuai !') {
                      info.css('display','');
                      info.find('ul').html('<li>' + response.content + '</li>');
                      $('.error-load').css('visibility', 'visible');
                      $('.error-load small').text(response.content);
                      buttonLadda.ladda('stop');
                    } else {
                          window.location = baseUrl + '/';
                    }
                }

            },
            error: function (xhr, status) {
                if (status == 'timeout') {
                    $('.error-load').css('visibility', 'visible');
                    $('.error-load small').text('Ups. Terjadi Kesalahan, Coba Lagi Nanti');
                }
                else if (xhr.status == 0) {
                    $('.error-load').css('visibility', 'visible');
                    $('.error-load small').text('Ups. Koneksi Internet Bemasalah, Coba Lagi Nanti');
                }
                else if (xhr.status == 500) {
                    $('.error-load').css('visibility', 'visible');
                    $('.error-load small').text('Ups. Server Bemasalah, Coba Lagi Nanti');
                }

                buttonLadda.ladda('stop');
            }
        });

    }
});

function validateForm() {
    var username = document.getElementById('username');
    var password = document.getElementById('password');

    //alert(username.value);

    if (username.validity.valueMissing) {
        $('#username-error').css('display', '');
        return false;
    }
    else if (password.validity.valueMissing) {
        $('#password-error').css('display', '');
        return false;
    }

    return true;
}
        </script>

    </body>

</html>

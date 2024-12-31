<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Creación de licencias</title>
    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap" rel="stylesheet">

    {{-- Bootstrap 5 | CSS --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">

    {{-- sweetalert2 --}}
    <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    {{-- Bootstrap 5 | JS --}}
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
    
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <script src="{{ asset('js/app.js') }}" defer></script>

</head>
<body class="py-2">
    <div class="container pt-4">
        <main>
            <h1>Generador de licencias</h1>
            <form action="POST" id="formCreateLicense" >
                @csrf
                <div class="row mb-3">
                    <div class="col-md-6 col-xl-4">
                        <div class="form-group">
                            <label for="uriGen" >Dominio <small>(URL)</small></label> 
                            <input type="url" name="uriGen" id="uriGen" class="form-control" placeholder="https://example.com" pattern="https://.*" size="30" >
                        </div>
                    </div>
                    <div class="col-md-4 col-xl-3">
                        <div class="form-group">
                            <label for="">Fecha de finalización</label>
                            <input type="date" id="dateEnd" name="dateEnd" class="form-control" max="2050-12-31" > <!-- min="2022-01-01" -->

                        </div>
                    </div>
                    <div class="col-md-2 ">
                        <div class="form-group">
                            <label for="">Días</label>
                            <input id="dayEl" type="number" value="" class="form-control" placeholder="0" min="0" >
                        </div>
                    </div>
                    <div class="mt-2 pt-3 col-md-3 col-xl-2">
                        <div class="form-group">
                            <button type="button" class="btn btn-sm btn-primary" id="generateLicense">Generar licencia</button>
                        </div>
                    </div>
                </div>
            </form>

            <div class="content__t tblLicenses">
                <table class="table" >
                    <thead>
                        <tr>
                            <th>Dominio <small>(URL)</small></th>
                            <th>Fecha de creación</th>
                            <th>Fecha de finalización</th>
                            <th>N° licencia</th>
                            <th>Token</th>
                            <th>Estatus</th>
                        </tr>
                    </thead>
                    <tbody id="tBodyContent" ></tbody>
                </table>
            </div>
        </main>
    </div>
</body>
</html>
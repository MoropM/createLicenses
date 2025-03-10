@extends('layouts.app')
@section('main_login_content')
    <div class="container py-5">
        <div class="title_link mb-3">
            <h1>Validador de licencias</h1>
            <a href="/" class="btn btn-link">Regresar <svg width="17"  height="17"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon icon-tabler icons-tabler-outline icon-tabler-arrow-left"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M5 12l14 0" /><path d="M5 12l6 6" /><path d="M5 12l6 -6" /></svg></a>
        </div>
        <section class="content_verify_licenses">

            <div class="content_verify_licenses-swbuttons">
                <button class="btn_verify_licenses btn px-3 py-2 active ">
                    Verificar licencia
                </button>
                <button class="btn_retrieve_licenses btn px-3 py-2 inactive ">
                    Recuperar licencia
                </button>
            </div>

            <div class="content_verify_licenses-forms d-flex justify-content-center align-items-center gap-3">
                <form action="" id="formVerifyLicense" class="p-4 d-block">
                    <div class="mb-3">
                        <label for="n_license" class="form-label">N° licencia</label>
                        <input type="text" class="form-control" id="n_license" placeholder="2h3jsdiIBksuKS983Nkasj2s0">
                    </div>
                    <div class="mb-3">
                        <button type="button" class="btn btn-primary">Verificar</button>
                    </div>
                </form>
    
                <form action="" id="formRetrieveLicense" class="p-4 d-none" >
                    <div class="mb-3">
                        <label for="token" class="form-label">Token</label>
                        <input type="text" class="form-control" id="token" placeholder="●●●●●●●●●●●●●●●●●●●●●●●●●●●">
                    </div>
                    <div class="mb-3">
                        <button type="button" class="btn btn-primary">Recuperar licencia</button>
                    </div>
                </form>
            </div>

            <div class="content_verify_licenses-data mt-4"></div>

        </section>
    </div>
@endsection
<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\License;
use Illuminate\Http\Request;
use App\Http\Resources\LicenseResource;
use App\Http\Requests\CreateLicenseRequest;

use Illuminate\Support\Facades\DB;

use Carbon\Carbon;
use Illuminate\Support\Str;

class LicenseController extends Controller
{
    private $valLis = 23;
    private $strPrivateVal = "create license personal";

    private $genCtrl;
    public function __construct(GeneralController $generalController)
    {
        $this->genCtrl = $generalController;
    }
    function index()
    {
        $allDataLicenses = License::all();
        $currentData = Carbon::now()->toDateString();
        if($allDataLicenses->count() > 0) { // ? Validación y actualización del estado de la licencia
            foreach($allDataLicenses as $item) {
                if($item->finish_date < $currentData && $item->status == 'active') {
                    $updateStatus = License::where('id', $item->id)->update(['status' => 'inactive']);
                    if($updateStatus == 1) {
                        $item->status = 'inactive';
                    }
                }
                if($item->finish_date > $currentData && $item->status == 'inactive') {
                    $updateStatus = License::where('id', $item->id)->update(['status' => 'inactive']);
                    if($updateStatus == 1) {
                        $item->status = 'active';
                    }
                }
            }
        }
        return LicenseResource::collection($allDataLicenses);
    }
    /** Creación de registro de la nueva licencia generada
     * 
     * @param App\Http\Requests\CreateLicenseRequest $request
     * @return \Illuminate\Http\Response $response
     */
    function create(CreateLicenseRequest $request)
    {
        DB::beginTransaction();
        $response = array();
        try{
            // $request->validate([
            //     "uri_access" => 'required|string|max:100|min:2|unique:licenses',
            //     "finishDate" => 'required|string|max:10|min:10',
            //     "daysActive" => 'max:100|min:1',
            // ]);
    
            $endYear = '';
            $endMonth = '';
            $endDay = '';
            $splodeDate = explode("-", $request->finishDate);
            $endYear = $splodeDate[0];
            $endMonth = $splodeDate[1];
            $endDay = $splodeDate[2];
            
            $fechaVigencia = Carbon::create($endYear, $endMonth, $endDay);
            $fecha_actual = Carbon::now();



            $uriRegister = License::where([
                'uri_access' => $request->uri_access,
                'status' => 'active',
            ])->count();
            if($uriRegister==1) {
                DB::rollBack();
                $response = [
                    "status" => 0,
                    "message" => "¡Error! La url ($request->uri_access) ya se encuentra registrada y tiene una licencia activa.",
                ];
                return response()->json($response,403);
            }
    
            // Para saber si la fecha actual es mayor que la vigencia:
            if($fecha_actual->gt($fechaVigencia)) {
                $response = [
                    "status" => 0,
                    "message" => "¡Error la fecha de vigencia ($fechaVigencia) debe ser mayor al dia actual ($fecha_actual)!"
                ];
                return response()->json($response,403);
            }
    
            // $cantidadDias = date_diff($fecha_actual, $fechaVigencia)->format('%R%a');
            $cantidadDias = $fechaVigencia->diffInDays($fecha_actual) + 1;
    
            $generatedLicense = [];
    
            $license = new License();
            $license->uri_access = $request->uri_access;
            $license->start_date = $fecha_actual->toDateString();
            $license->finish_date = $request->finishDate;
    
            $idRegister = 0;
            if( $license->save() ) {
                if( isset($license->id) ){
                    $idRegister = $license->id;
                    // Creación de token
                    $token = $license->createToken($request->uri_access.'.'.time())->plainTextToken;
                    // $token = $license->createToken("auth_t0kEn_Gener4tt3")->plainTextToken;
                    $generatedLicense = $this->generateLicenseNew($cantidadDias, $request);
                    if( $token != '' && count($generatedLicense) > 0 ){
    
                        $license_update = License::where('id', $idRegister)->update([
                            'access_token' => $token,
                            'status' => 'active',
                            'license_token' => $generatedLicense['license_token'],
                            'license' => $generatedLicense['license_number'],
                        ]);
    
                        if( $license_update == 1 ){
                            DB::commit();
                            $newLicense = License::find($idRegister);
                            return (new LicenseResource($newLicense))
                            ->additional([
                                'message' => 'Licencia creada correctamente',
                                // "accessToken" => $token,
                                // "generatedLicense" => $generatedLicense
                            ]);
                        }
                    }
                }
                else {
                    DB::commit();
                    $response = [
                        "status" => 1,
                        "message" => "¡Licencia creada correctamente!"
                    ];
                    return response()->json($response);
                    // return (new LicenseResource($license))
                    //     ->additional([
                    //         'message' => 'Licencia creada correctamente',
                    //     ]);
                }
            }
            else {
                DB::rollBack();
                $response = [
                    "status" => 0,
                    "message" => "¡Error al intentar registrar a la url ($request->uri_access)!"
                ];
                return response()->json($response,403);
                // return (new LicenseResource($license) )
                //         ->additional(['message' => "¡Error al intentar registrar a la marca ($request->uri_access)!"])
                //             ->response()
                //             ->setStatusCode(403);
            }
        }
        catch(\Exception $e) {
            DB::rollBack();
            $response['status'] = 0;
            $response['message'] = $e->getMessage();
            return response()->json($response,403);
        }
    }
    /** Desencriptación de token de licencia
     * Tomar encuenta los siguientes parametros de creación del token
     * para su respectiva desencriptación
     * 
     * == Composición del Token ==
     * [0] Fecha fin encriptado(1)
     * [1] Fecha incio encriptado(2)
     * [2] Clave privada encripttado(2)
     *      strPrivateVal=privateDomain+valLis
     * 
     * == Composición del Licencia ==
     * [0] Str aleatorio de 5 dígitos
     * [1] 7 digitos de la fecha de inicio encriptada a partir de la posición 5
     * [2] Primaros 5 dígitos de la fehca de finalización encriptada
     * [3] Str aleatorio de 6 dígitos
     * [4] Valor privado en la clase
     * 
    */
    private function tokenDencrypt($licenseToken, $verifyLicense)
    {
        $response = array(
            "status" => false,
            "message" => "La licencia ingresada es inválida"
        );
        $uriAccess = '';
        $startDate = '';
        $finishDate = '';
        $tokenLicense = '';
        foreach( $verifyLicense as $dataTokenLicense ) {
            $uriAccess = $dataTokenLicense->uri_access; // Obtenemos el valor de la uri
            $startDate = $dataTokenLicense->start_date; // Obtenemos el valor de fecha inicio
            $finishDate = $dataTokenLicense->finish_date; // Obtenemos el valor de fecha fin
            $tokenLicense = $dataTokenLicense->license_token; // Obtenemos el valor del token
        }

        // [1] 7 digitos de la fecha de final (2)
        // $dateStrt7 = substr($licenseToken, 5,7);

        // [2] 5 digitos de fecha final encriptado (1)
        $dateEnd5 = substr($licenseToken, 12,5);

        // [3] Digitos del valor privado en la clase
        $lengValLis = strlen($this->valLis);
        $privateValLis = substr($licenseToken, -$lengValLis);


        $tokenExplode = explode('.', $tokenLicense);
        // [1] Valor encriptdo de fecha de finalizanción
        $tokenExplode1 = $tokenExplode[0];
        $tokenExplode1 = substr($tokenExplode1, 0,5);

        // [2] Valor encriptdo de fecha de inicio
        // $tokenExplode2 = $tokenExplode[1];
        // $tokenExplode2 = $this->decrypt($tokenExplode2);

        // [3] Valor privado de la clase
        $tokenExplode3 = $tokenExplode[2];
        // $tokenExplode3 = $this->decrypt($tokenExplode3);
        $tokenExplode3 = $this->decrypt($this->decrypt($tokenExplode3));
        $explodeTokenExplode3 = explode('__', $tokenExplode3)[2];
        // $response['message'] = $tokenExplode3;

        if( trim($dateEnd5) == trim($tokenExplode1) && trim($privateValLis) == trim($explodeTokenExplode3) ) {
            $response['status'] = true;
            $response['message'] = '';
        }
        
        return $response;

        /*
        return [
            // "uriAccess" => $uriAccess,
            // "startDate" => $startDate,
            // "finishDate" => $finishDate,
            // "dateStrt7" => $dateStrt7,
            "dateEnd5" => $dateEnd5,
            "privateValLis" => $privateValLis,
            // "license" => $license,
            "license" => $licenseToken,
            "tokenExplode1" => $tokenExplode1,
            // "tokenExplode2" => $tokenExplode2,
            "tokenExplode3" => $tokenExplode3,
            "explodeTokenExplode3" => $explodeTokenExplode3,
            // "tokenLicense" => $tokenLicense,
            // "licenseToken" => $licenseToken,
        ];
        */
    }
    /** Verificación de licencia a través del id de registro
     * 
     * {
     *  "id": "1|gKhPjxxgyHPek1enQkd90CS68xAkFXfDou0N4GJx",
     *  "uri_access": "https://moroniperezm.com",
     *  "finishDate": "2025-01-31",
     *  "type": 0
     * }
     * 
     * @param \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response $response
    */
    function verifyLicense (Request $request)
    {
        $typeReq = $request->type_req??'';
        $verifyLicense = [];
        if( $typeReq==''){
            $verifyLicense = License::join('personal_access_tokens', 'licenses.id', '=', 'personal_access_tokens.tokenable_id')
                ->where('personal_access_tokens.id', $request->id)->get();
        }
        if( $typeReq=='license'){
            $verifyLicense = License::where('license', $request->id)->get();
        }
        if( $typeReq=='token'){
            $license = License::where('access_token', $request->id)->pluck('license')->first();
            $response = [
                "status" => false,
                "code_http" => 200,
                "message" => "",
                "data" => []
            ];
            if($license==''){
                $response["message"] = "El token no existe";
            }
            else {
                $response["status"] = true;
                $response["message"] = "Licencia registrada";
                $response["data"] = [
                    "license" => $license
                ];
            }
            return response()->json($response,200);

        }
        $response = [
            // "status" => true,
            // "code_http" => 200,
            // "message" => "Vamos bien",
            // "verifyLicense" => $verifyLicense,

            // "data" => $this->tokenDencrypt($request->license, $verifyLicense)
        ];
        // return response()->json($response,200);


        // Verificación de licencia
        if( $request->type == 0 ) {
            $statusLicense = '';
            if( count($verifyLicense) > 0 ) {
                $valNumberDays = '';
                $strLicense = '';
                $uriDomain = '';
                $srtToken = '';
                $startDate = '';
                $endDate = '';
                foreach( $verifyLicense as $dataStatusLicense ) {
                    $statusLicense = $dataStatusLicense->status;
                    $strLicense = $dataStatusLicense->license;

                    $uriDomain = $dataStatusLicense->uri_access;
                    $srtToken = $dataStatusLicense->access_token;
                    $startDate = $dataStatusLicense->start_date;
                    $endDate = $dataStatusLicense->finish_date;
                }
                $explodeStrLicense = explode('MS',$strLicense);
                if( count($explodeStrLicense) > 1 ) {
                    $valNumberDays = $explodeStrLicense[count($explodeStrLicense) - 1];
                }
                if( trim($statusLicense) == '' || $statusLicense == 'inactive' ) {
                    // return false;
                    $response = [
                        "status" => false,
                        "code_http" => 200,
                        "message" => "Licencia no activa",
                        "data" => [
                            "status" => $statusLicense,
                            "remaining_days" => 0,
                            "uri_domain" => $uriDomain,
                            "token" => $srtToken,
                            "start_date" => $startDate,
                            "finish_date" => $endDate,
                        ]
                    ];
                }
                else{
                    // return true;
                    $response = [
                        "status" => true,
                        "code_http" => 200,
                        "message" => "Licencia activa",
                        "data" => [
                            "status" => $statusLicense,
                            "remaining_days" => (int)$valNumberDays,
                            "uri_domain" => $uriDomain,
                            "token" => $srtToken,
                            "start_date" => $startDate,
                            "finish_date" => $endDate,
                        ]
                    ];
                }
                return response()->json($response,200);
            }
            else{ 
                // return false;
                $response = [
                    "status" => false,
                    "code_http" => 200,
                    "message" => "La licencia no existe",
                    "data" => [
                        "status" => $statusLicense,
                        "remaining_days" => 0,
                    ]
                ];
                return response()->json($response,200);
            }
        }
        // Verificar Licencia en cada incio de sesión
        if( $request->type == 1 ) {
            $tokenLicense = '';
            if( count($verifyLicense) > 0 ) {
                foreach( $verifyLicense as $dataTokenLicense ) { $tokenLicense = $dataTokenLicense->license_token; } // Obtenemos el valor del token
                if( trim($tokenLicense) == '' ) {
                    // return "0|No hay una licencia registrada. Consulte a su administrador del sitio";
                    $response = [
                        "status" => false,
                        "code_http" => 200,
                        "message" => "¡No hay una licencia registrada. Consulte a su administrador del sitio!",
                        "data" => [
                            "final_day" => 0,
                        ]
                    ];
                    return response()->json($response,200);
                }
                else{
                    // Desencriptamos el tocken
                    // $tokenDencrypt = $this->decrypt($tokenLicense);

                    // Obtenemos la fecha encriptada de finalización
                    $dateEndEncrypt = explode('.', $tokenLicense)[0];
                    $dateEndDencrypt = $this->genCtrl->decrypt($dateEndEncrypt);

                    $splodeToken = explode("-", $dateEndDencrypt);
                    $endYear = $splodeToken[0];
                    $endMonth = $splodeToken[1];
                    $endDay = $splodeToken[2];

                    $fechaVigencia = Carbon::create($endYear, $endMonth, $endDay);
                    $fecha_actual = Carbon::now();
                    // Para saber si la fecha actual es mayor que la vigencia:
                    if($fecha_actual->gt($fechaVigencia)) {
                        // return "0|Su licencia ha caducado. Consulte a su administrador del sitio";
                        $response = [
                            "status" => false,
                            "code_http" => 200,
                            "message" => "¡Su licencia ha caducado. Consulte a su administrador del sitio!",
                            "data" => [
                                "final_day" => 0,
                            ]
                        ];
                        return response()->json($response,200);
                    }
                    else{
                        $diffDayTotal = 0;
                        $diffDate = $fecha_actual->diffInDays($fechaVigencia);
                        $diffDayTotal = (int)$diffDate + 1;
                        if( $diffDayTotal <= 30 ){
                            // return "1|Su licencia está por vencer en $diffDayTotal días.|$diffDayTotal";
                            $response = [
                                "status" => true,
                                "code_http" => 200,
                                "message" => "¡Su licencia está por vencer en $diffDayTotal días!",
                                "data" => [
                                    "final_day" => $diffDayTotal,
                                //     "token" => $token,
                                //     "finish_date" => $request->finishDate,
                                //     "license_number" => $generatedLicense['license_number'],
                                ]
                            ];
                            return response()->json($response,200);
                        }
                        else{ 
                            // return "1|";
                            $response = [
                                "status" => true,
                                "code_http" => 200,
                                "message" => "Licencia activa",
                                "data" => [
                                    "final_day" => $diffDayTotal,
                                ]
                            ];
                            return response()->json($response,200);

                        }
                    }
                }
            }
            else{
                // return "0|No hay una licencia registrada. Consulte a su administrador del sitio";
                $response = [
                    "status" => false,
                    "code_http" => 200,
                    "message" => "¡No hay una licencia registrada. Consulte a su administrador del sitio!",
                    "data" => [ "final_day" => 0, ]
                ];
                return response()->json($response,200);
            }
            
        }
    }
    /** Creación de número de licencia y token de la licencia creada
     * 
     * @param int  $valGen
     * @return string $uri
     */
    function generateLicense ($valGen, $uri)
    {
        $valNumberDays = (int) $valGen;
        $response = array(); 
        $nowDate = "";
        $strRan = "";
        $numDays = 0;

        $nowDate = Carbon::now();
        $strRan = Str::random(5);

        // Obtener fecha yyyy-mm-dd
        $strDate = $nowDate->toDateString();
        // Encriptación fecha actual
        $dateStartEncrypt = $this->genCtrl->encrypt($this->genCtrl->encrypt($strDate));

        // Verificando el número de días agregados
        if( $valNumberDays == 0 ){ $numDays = 15; }
        else { $numDays = $valNumberDays; }


        // $date15 = $nowDate->addDay($numDays)->toDateString();
        // $date30 = $nowDate->addDay(30)->toDateString();
        $datePlusNumber = $nowDate->addDay($numDays)->toDateString();

        // Encriptación días agregados
        $dateEncrypt = $this->genCtrl->encrypt($datePlusNumber);
        // Desencriptación de días agregados
        // $dateDencrypt = $this->decrypt($dateEncrypt);


        // Obtenemos el dominio del usuario
        $privateDomain = '';
        if( count(explode('://',$uri)) > 1 ) {
            $privateDomain = explode('://',$uri)[1];
        }
        // Encriptación de último valor para token
        $strPrivateEncrypt = $this->genCtrl->encrypt($this->strPrivateVal."__".$privateDomain."__".$this->valLis);
        $strPrivateEncrypt = $this->genCtrl->encrypt($strPrivateEncrypt);

        // Creación de token único
        /** == Composición del Token ==
         * [0] Fecha fin encriptado(1)
         * [1] Fecha incio encriptado(2)
         * [2] Clave privada encripttado(2)
         *      strPrivateVal=privateDomain+valLis
        */
        $licenseToken = "$dateEncrypt.$dateStartEncrypt.$strPrivateEncrypt";


        // Obtener los 7 letras de fecha inicio encriptada
        $substrStrt5 = substr($dateStartEncrypt, 5, 7);
        // Obtener las primeras 5 letras de fecha fin encriptada
        $substrEnd5 = substr($dateEncrypt, 0, 5);
        
        // 6 dígitos aleatórios
        $strRan2 = Str::random(6);
        
        // $license = $strRan.$substrEnd5.$strRan2."22";
        /** == Composición del Licencia ==
         * [0] Str aleatorio de 5 dígitos
         * [1] 7 digitos de la fecha de inicio encriptada a partir de la posición 5
         * [2] Primaros 5 dígitos de la fecha de finalización encriptada
         * [3] Str aleatorio de 6 dígitos
         * [4] Valor privado en la clase
        */
        $license = $strRan.$substrStrt5.$substrEnd5.$strRan2.$this->valLis;

        // strtoupper($strRan)
        /*
        $response = array(
            "random" => $strRan,
            "date" => $strDate,
            "date_mas_15_dias" => $date15,
            "date_mas_30_dias" => $date30,
            "encrypt" => $dateEncrypt,
            "substr_" => $substr5,
            "decrypt" => $dateDencrypt,
            "random2" => $strRan2,
            "license" => $license,
            "strlen25" => strlen($license),
        );
        */
        $response = array(
            "license_token" => $licenseToken,
            // "license_date_tk" => $dateDencrypt,
            "license_number" => $license,
            // "license_uri" => explode('://',$uri),
            // "strPrivateEncrypt" => $this->decrypt($this->decrypt($strPrivateEncrypt)),
            //"dateStartEncrypt" => $this->decrypt($this->decrypt($dateStartEncrypt)),
        );

        return $response;
    }

    function generateLicenseNew($days = 15, Request $request)
    {
        $valNumberDays = $days;
        $response = array(); 
        $nowDate = "";
        $strRan = "";
        $numDays = 0;

        $nowDate = Carbon::now();
        // 10 dígitos aleatórios
        $strRan = Str::random(10);
        // 6 dígitos aleatórios
        $strRan2 = Str::random(6);

        // Fecha inicial
        $strDate = $nowDate->toDateString();
        $startDateEncrypt = $this->genCtrl->encrypt($strDate); // Encriptación de fecha inicial

        // Configuración inicial
        if( $valNumberDays == 0 ){
            // $numDays = 3;

            // Creación de fecha hasta final del año
            $addDay = 31 - $nowDate->day;
            $addMonth = 12 - $nowDate->month;
            $nowDate = $nowDate->addDay($addDay);
            $date15 = $nowDate->addMonth($addMonth)->toDateString();
        }
        else {
            if( strlen($valNumberDays) > 2 ) { $strRan2 = Str::random(5); }
            if( strlen($valNumberDays) > 3) { $strRan2 = Str::random(4); }
            $numDays = $valNumberDays;
            $date15 = $nowDate->addDay($numDays)->toDateString(); // Fecha de acuerdo al N° de días ingresado
        }

        // $date30 = $nowDate->addDay(30)->toDateString();
        // Obtener fecha yyyy-mm-dd
        // $strDate = $nowDate; // ->toDateString();
        // Encriptación
        $dateEncrypt = $this->genCtrl->encrypt($date15);
        // Desencriptación
        $dateDencrypt = $this->genCtrl->decrypt($dateEncrypt);
        // Obtener las primeras 5 letras de fecha encriptada
        $substr5 = substr($dateEncrypt, 0, 5);
       

        // $license = $strRan.$substr5.$strRan2."22";
        $license = $strRan.$substr5.$strRan2.'MS'.$valNumberDays;

        // strtoupper($strRan)
        $response = array(
            "random" => $strRan,
            "date" => $strDate,
            "date_mas_15_dias" => $date15,
            //"date_mas_30_dias" => $date30,
            "license_token" => $dateEncrypt.".".$startDateEncrypt,
            "substr_" => $substr5,
            "license_date_end" => $dateDencrypt,
            "random2" => $strRan2,
            "license_number" => $license,
            "strlen25" => strlen($license),
            "uri" => $request['uri_access'],
            "valNumberDays" => $valNumberDays,
        );
        /* Ejemplo de respuesta
        {
            "random": "XmRjs7YweD", // 10 dígitos aleatórios
            "date": "2023-02-13", //start_date - Fecha inicial
            "date_mas_15_dias": "2023-02-28", // finish_date - 5 dias despues de la creacion
            "license_token": "UVUvbzN6OVNxd3RxTzEyM3BIZWxsdz09.TlE4anNMVHZvZFBmWFVRdjJlQjFaQT09", // Fecha encriptada de acuerdo al N° de días ingresado . Encriptación de fecha inicial
            "substr_": "UVUvb", // Obtener las primeras 5 letras de fecha encriptada
            "license_date_end": "2023-02-28", // finish_date - 5 dias despues de la creacion
            "random2": "91SwuZ", // 6, 5 o 4 dígitos aleatórios
            "license_number": "XmRjs7YweDUVUvb91SwuZMS15", // 10 dígitos aleatórios + Obtener las primeras 5 letras de fecha encriptada + 6, 5 o 4 dígitos aleatórios + MS + No dias agregados
            "strlen25": 25 // No dias agregados
        }
        */

        return $response;
    }

}

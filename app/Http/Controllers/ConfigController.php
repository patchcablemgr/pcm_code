<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use App\Models\CSRModel;
use App\Models\KeyModel;
use App\Models\CertModel;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class ConfigController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function indexNetwork()
    {
        $filename = '/shared/network_config';
        $contents = file($filename);
        $data = array();

        foreach($contents as $line) {
            $kvp = explode("=", $line);
            $key = trim($kvp[0]);
            $value = rtrim(trim($kvp[1]), " \n\r\t\v\x00");

            switch($key) {
                case 'DHCP':
                    $data['dhcp'] = ($value == 'yes') ? true : false;
                    break;
                case 'HOST':
                    $data['host_address'] = $value;
                    break;
                case 'GATEWAY':
                    $data['gateway'] = $value;
                    break;
                case 'DNS':
                    $data['dns'] = $value;
                    break;
            }
        }

        return $data;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function indexCSR()
    {

        $CSRs = CSRModel::all();

        return $CSRs;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function indexCert()
    {

        $certs = CertModel::all();

        return $certs;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Store a network config to be apply to system.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function networkConfig(Request $request)
    {

        $dhcp = ($request['dhcp']) ? 'yes' : 'no';
        //$dhcp = $request['dhcp'];
        $hostAddress = $request['host_address'];
        $gateway = $request['gateway'];
        $dns = $request['dns'];

        $config = "DHCP=".$dhcp.PHP_EOL;
        $config .= "HOST=".$hostAddress.PHP_EOL;
        $config .= "GATEWAY=".$gateway.PHP_EOL;
        $config .= "DNS=".$dns.PHP_EOL;

        return array('success' => file_put_contents('/shared/network_config', $config));
    }

    /**
     * Store a CSR.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function generateCSR(Request $request)
    {

        // Configure certificate fields
        $dn = array(
            "countryName" => "US",
            "stateOrProvinceName" => "Washington",
            "localityName" => "Seattle",
            "organizationName" => "PatchCableMgr",
            "organizationalUnitName" => "IT",
            "commonName" => "PCM",
            "emailAddress" => "support@patchcablemgr.com"
        );

        // Generate private key object
        $privkey = openssl_pkey_new(array(
            "private_key_bits" => 2048,
            "private_key_type" => OPENSSL_KEYTYPE_RSA,
        ));

        // Export and store private key
        openssl_pkey_export($privkey, $privKeyStr);
        $keyFilename = Str::random(9).'.key';
        Storage::disk('shared')->put('keys/'.$keyFilename, $privKeyStr);
        $key = new KeyModel;
        $key->filename = $keyFilename;
        $key->save();

        // Generate and store CSR
        $csr = openssl_csr_new($dn, $privkey, array('digest_alg' => 'sha256'));
        openssl_csr_export($csr, $csr_string);
        $userID = Auth::id();
        $csr = new CSRModel;
        $csr->csr = $csr_string;
        $csr->user_id = $userID;
        $csr->key_id = $key['id'];
        $csr->save();

        return $csr->toArray();
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroyCSR($id)
    {
        $validatorInput = [
            'id' => $id
        ];
        $validatorRules = [
            'id' => [
                'required',
                'exists:csr'
            ]
        ];
        $validatorMessages = [
        ];
        Validator::make($validatorInput, $validatorRules, $validatorMessages)->validate();
				
		$CSR = CSRModel::where('id', $id)->first();

        $CSR->delete();

        return array('id' => $id);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroyCert($id)
    {
        $validatorInput = [
            'id' => $id
        ];
        $validatorRules = [
            'id' => [
                'required',
                'exists:cert'
            ]
        ];
        $validatorMessages = [
        ];
        Validator::make($validatorInput, $validatorRules, $validatorMessages)->validate();
				
		$cert = CertModel::where('id', $id)->first();

        $cert->delete();

        return array('id' => $id);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function storeCert(Request $request, $id)
    {
        // Prepare variables
        $validatorInput = [
            'id' => $id,
            'file' => $request->file
        ];
        $validatorRules = [
            'id' => [
                'integer',
                'exists:csr,id'
            ],
            'file' => [
                'max:512'
            ]
        ];
        $validatorMessages = [];
        Validator::make($validatorInput, $validatorRules, $validatorMessages)->validate();

        // Validate certificate
        $certStr = $request->file('file')->get();
        $CSR = CSRModel::where('id', $id)->first();
        $keyID = $CSR->key_id;
        $key = KeyModel::where('id', $keyID)->first();
        $keyFilename = $key->filename;
        $keyStr = Storage::disk('shared')->get('keys/'.$keyFilename);
        if(!openssl_x509_check_private_key($certStr, $keyStr)) {
            throw ValidationException::withMessages(['certificate_validation' => 'Certificate is not valid.']);
        }

        // Store certificate
        $path = $request->file('file')->store('certs', 'shared');
        $pathArray = explode('/', $path);

        // Get certificate details
        $certDetails = openssl_x509_parse($certStr);

        // Create certificate record
        $cert = new CertModel;
        $cert->filename = end($pathArray);
        $cert->valid_from = date('Y-m-d H:i:s', $certDetails['validFrom_time_t']);
        $cert->valid_to = date('Y-m-d H:i:s', $certDetails['validTo_time_t']);
        $cert->save();

        // Update CSR record with cert ID
        $CSR->cert_id = $cert->id;

        return $cert;
        /*

        // Store floorplan image
        $path = $request->file('file')->store('images');

        // Update location floorplan image
        $pathArray = explode('/', $path);
        $location->img = end($pathArray);
            
        // Save location record
        $location->save();

        // Return location record
        return $location->toArray();
        
        return true;
        */
    }
}

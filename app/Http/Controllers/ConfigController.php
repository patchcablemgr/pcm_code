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

        $validatorInput = [
            'country' => $request->country,
            'state' => $request->state,
            'city' => $request->city,
            'organization' => $request->organization,
            'cn' => $request->cn,
        ];
        $validatorRules = [
            'country' => [
                'required',
                'regex:/^[A-Z]{2}$/',
                'size:2'
            ],
            'state' => [
                'required',
                'regex:/^[a-zA-Z\s\-]*$/',
                'max:255'
            ],
            'city' => [
                'required',
                'regex:/^[a-zA-Z\s\-]*$/',
                'max:255'
            ],
            'organization' => [
                'required',
                'regex:/^[a-zA-Z\s\-]*$/',
                'max:255'
            ],
            'cn' => [
                'required',
                'regex:/(?=^.{4,253}$)(^((?!-)[a-zA-Z0-9-]{0,62}[a-zA-Z0-9]\.)+[a-zA-Z]{2,63}$)/',
                'max:255'
            ]
        ];
        $validatorMessages = [
        ];
        Validator::make($validatorInput, $validatorRules, $validatorMessages)->validate();

        // Configure certificate fields
        $dn = array(
            "countryName" => $request->country,
            "stateOrProvinceName" => $request->state,
            "localityName" => $request->city,
            "organizationName" => $request->organization,
            "commonName" => $request->cn,
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
        $csr->country = $request->country;
        $csr->state = $request->state;
        $csr->city = $request->city;
        $csr->organization = $request->organization;
        $csr->cn = $request->cn;
        $csr->save();

        return $csr->toArray();
    }

    /**
     * Generate a self-signed certificate
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function generateSelfSigned(Request $request, $id)
    {

        // Prepare variables
        $validatorInput = [
            'id' => $id,
        ];
        $validatorRules = [
            'id' => [
                'integer',
                'exists:csr,id'
            ],
        ];
        $validatorMessages = [];
        Validator::make($validatorInput, $validatorRules, $validatorMessages)->validate();

        // Get CSR
        $CSR = CSRModel::where('id', $id)->first();
        $CSRStr = $CSR['csr'];

        // Get Key
        $keyID = $CSR->key_id;
        $key = KeyModel::where('id', $keyID)->first();
        $keyFilename = $key->filename;
        $keyStr = Storage::disk('shared')->get('keys/'.$keyFilename);

        // Generate Certificate
        $selfSignedCert = openssl_csr_sign($CSRStr, NULL, $keyStr, 365);

        // Store certificate
        $certFilename = Str::random(9).'.txt';
        openssl_x509_export($selfSignedCert, $certStr);
        Storage::disk('shared')->put('certs/'.$certFilename, $certStr);

        // Get certificate details
        $certDetails = openssl_x509_parse($certStr);

        // Create certificate record
        $cert = new CertModel;
        $cert->filename = $certFilename;
        $cert->valid_from = date('Y-m-d H:i:s', $certDetails['validFrom_time_t']);
        $cert->valid_to = date('Y-m-d H:i:s', $certDetails['validTo_time_t']);
        $cert->key_id = $keyID;
        $cert->country = $certDetails['subject']['C'];
        $cert->state = $certDetails['subject']['ST'];
        $cert->city = $certDetails['subject']['L'];
        $cert->organization = $certDetails['subject']['O'];
        $cert->cn = $certDetails['subject']['CN'];
        $cert->issuer = substr($certDetails['issuer']['CN'], 0, 254);
        $cert->active = false;
        $cert->save();

        // Update CSR record with cert ID
        $CSR->cert_id = $cert->id;

        return $cert;
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

        // Ensure certificate is not active
        if($cert['active']) {
            throw ValidationException::withMessages(['certificate_validation' => 'Cannot delete active certificate.']);
        }

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
        $cert->key_id = $keyID;
        $cert->country = $certDetails['subject']['C'];
        $cert->state = $certDetails['subject']['ST'];
        $cert->city = $certDetails['subject']['L'];
        $cert->organization = $certDetails['subject']['O'];
        $cert->cn = $certDetails['subject']['CN'];
        $cert->active = false;
        $cert->save();

        // Update CSR record with cert ID
        $CSR->cert_id = $cert->id;

        return $certDetails;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function activateCert(Request $request, $id)
    {
        // Prepare variables
        $validatorInput = [
            'id' => $id,
        ];
        $validatorRules = [
            'id' => [
                'integer',
                'exists:cert,id'
            ]
        ];
        $validatorMessages = [];
        Validator::make($validatorInput, $validatorRules, $validatorMessages)->validate();

        // Get certificate
        $cert = CertModel::where('id', $id)->first();

        // Reset any currently active certificate
        $activeCerts = CertModel::where('active', true)->update(['active' => false]);

        // Mark cert as active
        $cert->active = true;
        $cert->save();

        // Get key entry
        $keyID = $cert['key_id'];
        $key = KeyModel::where('id', $keyID)->first();

        $config = "CERT=".$cert['filename'].PHP_EOL;
        $config .= "KEY=".$key['filename'].PHP_EOL;

        file_put_contents('/shared/ssl_config', $config);

        return $cert;

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function updateApp(Request $request)
    {
        $time = now();

        $signalUpdate = file_put_contents('/shared/app_update', $time);

        return $signalUpdate;

    }

}
